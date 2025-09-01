<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\StudentPaymentPlan;
use App\Models\PaymentInstallment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LateFeeApprovalController extends Controller
{
    /**
     * Show NIC + course selection form
     */
    public function index()
    {
        return view('late_fee.approval');
    }

    /**
     * Load approval page (with installments & late fee calculation)
     */
    public function approvalPage($studentNic, $courseId)
    {
        $student = Student::where('id_value', $studentNic)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $plan = StudentPaymentPlan::where('student_id', $student->student_id)
            ->where('course_id', $courseId)
            ->with('installments')
            ->first();

        if (!$plan) {
            return redirect()->back()->with('error', 'No payment plan found for this course.');
        }

        $installments = $plan->installments()->orderBy('due_date')->get();

        // Preprocess late fee calculation
        $installments = $installments->map(function ($inst) {
            $dueDate  = \Carbon\Carbon::parse($inst->due_date);
            $isLate   = $dueDate->isPast() && $inst->status !== 'paid';
            $daysLate = $isLate ? $dueDate->diffInDays(now()) : 0;
            $finalAmt = $inst->final_amount ?? $inst->amount ?? 0;

            $inst->calculated_late_fee = $isLate ? $this->calculateLateFee($finalAmt, $daysLate) : 0;
            $inst->days_late           = $daysLate;
            return $inst;
        });

        return view('late_fee.approval', compact('student', 'plan', 'installments', 'studentNic', 'courseId'));
    }

    /**
     * Ajax – return payment plan + installments (JSON)
     */
    public function getApprovalPaymentPlan(Request $request)
    {
        $request->validate([
            'student_nic' => 'required|string',
            'course_id'   => 'required|integer|exists:courses,course_id',
        ]);

        $student = Student::where('id_value', $request->student_nic)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], Response::HTTP_NOT_FOUND);
        }

        $registration = CourseRegistration::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Not registered for this course.'], Response::HTTP_NOT_FOUND);
        }

        $studentPaymentPlan = StudentPaymentPlan::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->with('installments')
            ->first();

        if (!$studentPaymentPlan) {
            return response()->json(['success' => false, 'message' => 'No payment plan found.'], Response::HTTP_NOT_FOUND);
        }

        $installments = $studentPaymentPlan->installments->map(function ($inst) {
            $dueDate  = \Carbon\Carbon::parse($inst->due_date);
            $isLate   = $dueDate->isPast() && $inst->status !== 'paid';
            $daysLate = $isLate ? $dueDate->diffInDays(now()) : 0;
            $finalAmt = $inst->final_amount ?? $inst->amount ?? 0;

            return [
                'id'                  => $inst->id,
                'installment_number'  => $inst->installment_number,
                'due_date'            => $inst->due_date,
                'amount'              => $finalAmt,
                'status'              => $inst->status,
                'is_late'             => $isLate,
                'days_late'           => $daysLate,
                'calculated_late_fee' => $isLate ? $this->calculateLateFee($finalAmt, $daysLate) : 0,
                'approved_late_fee'   => $inst->approved_late_fee,
                'approval_note'       => $inst->approval_note,
            ];
        });

        return response()->json([
            'success'      => true,
            'student'      => $student,
            'course_id'    => $request->course_id,
            'installments' => $installments,
        ]);
    }

    /**
 * Approve/reduce per installment
 */
public function approveLateFeePerInstallment(Request $request, $installmentId)
{
    $request->validate([
        'approved_late_fee' => 'required|numeric|gt:0', // must be > 0
        'approval_note'     => 'nullable|string'
    ]);

    $inst = PaymentInstallment::findOrFail($installmentId);

    // 🔹 Check due date (only allow after due date passed)
    $dueDate = \Carbon\Carbon::parse($inst->due_date);
    if ($dueDate->isFuture()) {
        return back()->with('error', 'You can only approve late fees after the due date has passed.');
    }

    // 🔹 Always recalc late fee at the time of approval
    $isLate   = $dueDate->isPast() && $inst->status !== 'paid';
    $daysLate = $isLate ? $dueDate->diffInDays(now()) : 0;
    $finalAmt = $inst->final_amount ?? $inst->amount ?? 0;

    $inst->calculated_late_fee = $isLate ? $this->calculateLateFee($finalAmt, $daysLate) : 0;

    // 🔹 Append to history
    $history = is_array($inst->approval_history) ? $inst->approval_history : [];
    $history[] = [
        'calculated_late_fee' => $inst->calculated_late_fee,
        'approved_late_fee'   => (float)$request->approved_late_fee,
        'approval_note'       => $request->approval_note,
        'approved_by'         => auth()->user()->name ?? 'System',
        'approved_at'         => now()->toDateTimeString(),
    ];

    // 🔹 Save latest approval
    $inst->approved_late_fee = $request->approved_late_fee;
    $inst->approval_note     = $request->approval_note;
    $inst->approved_by       = auth()->id();
    $inst->approval_history  = $history;
    $inst->save();

    // 🔹 Update related payment_details if exists
    $registrationId = \App\Models\CourseRegistration::where('student_id', $inst->paymentPlan->student_id)
    ->where('course_id', $inst->paymentPlan->course_id)
    ->value('id');

    $paymentDetail = \App\Models\PaymentDetail::where('student_id', $inst->paymentPlan->student_id)
        ->where('course_registration_id', $registrationId)
        ->where('installment_number', $inst->installment_number)
        ->where('status', 'pending')
        ->first();


    if ($paymentDetail) {
        $baseAmt  = $inst->final_amount ?? $inst->amount ?? 0;
        $lateFee  = $inst->calculated_late_fee;
        $approved = $inst->approved_late_fee ?? 0;

        $paymentDetail->late_fee          = $lateFee;
        $paymentDetail->approved_late_fee = $approved;
        $paymentDetail->total_fee         = $baseAmt + $lateFee - $approved;
        $paymentDetail->save();
    }

    return back()->with('success', 'Late fee approved for installment.');
}



/**
 * Approve/reduce global late fee across installments
 */
public function approveLateFeeGlobal(Request $request, $studentNic, $courseId)
{
    $request->validate([
        'reduction_amount' => 'required|numeric|gt:0', // total approved pool
        'approval_note'    => 'nullable|string'
    ]);

    $student = Student::where('id_value', $studentNic)->firstOrFail();

    $installments = PaymentInstallment::whereHas('paymentPlan', function ($q) use ($student, $courseId) {
            $q->where('student_id', $student->student_id)
              ->where('course_id', $courseId);
        })
        ->orderBy('due_date', 'asc')
        ->get();

    $remaining = $request->reduction_amount;

    foreach ($installments as $inst) {
        // 🔹 Recalc fee
        $dueDate  = \Carbon\Carbon::parse($inst->due_date);
        $isLate   = $dueDate->isPast() && $inst->status !== 'paid';
        $daysLate = $isLate ? $dueDate->diffInDays(now()) : 0;
        $finalAmt = $inst->final_amount ?? $inst->amount ?? 0;
        $calcFee  = $isLate ? $this->calculateLateFee($finalAmt, $daysLate) : 0;

        $inst->calculated_late_fee = $calcFee;

        if ($remaining <= 0) {
            $inst->save();
            continue;
        }

        if ($remaining >= $calcFee) {
            // Approve full fee
            $inst->approved_late_fee = $calcFee;
            $remaining -= $calcFee;
        } else {
            // Approve partial fee
            $inst->approved_late_fee = $remaining;
            $remaining = 0;
        }

        // 🔹 Append to history
        $history = $inst->approval_history ?? [];
        $history[] = [
            'calculated_late_fee' => $inst->calculated_late_fee,
            'approved_late_fee'   => (float)$inst->approved_late_fee,
            'approval_note'       => $request->approval_note,
            'approved_by'         => auth()->user()->name ?? 'System',
            'approved_at'         => now()->toDateTimeString(),
        ];

        $inst->approval_note    = $request->approval_note;
        $inst->approved_by      = auth()->id();
        $inst->approval_history = $history;
        $inst->save();
    }

    return back()->with('success', 'Global late fee approved successfully.');
}



    /**
     * Helper: Calculate late fee
     */
    private function calculateLateFee($amount, $daysLate)
    {
        if ($daysLate <= 0) return 0;

        $dailyRate = (0.05 / 30); // 5% monthly → daily
        $lateFee   = $amount * $dailyRate * $daysLate;

        return round(min($lateFee, $amount * 0.25), 2);
    }

    /**
     * Ajax – get courses by NIC
     */
    public function getStudentCourses(Request $request)
    {
        $request->validate([
            'student_nic' => 'required|string',
        ]);

        $student = Student::where('id_value', $request->student_nic)->first();

        if (!$student) {
            return response()->json(['success' => false, 'courses' => []]);
        }

        $courses = CourseRegistration::where('student_id', $student->student_id)
            ->with('course')
            ->get()
            ->map(function ($registration) {
                return [
                    'course_id'   => $registration->course->course_id,
                    'course_name' => $registration->course->course_name,
                ];
            });

        return response()->json(['success' => true, 'courses' => $courses]);
    }
}
