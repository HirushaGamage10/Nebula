<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SemesterRegistration;
use App\Models\StudentPaymentPlan;
use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;

class RepeatStudentPaymentController extends Controller
{
    public function index()
    {
        return view('repeat_students.payment_plan');
    }

    /**
     * Get Archived + Current Payment Plan for Student + Course
     */
    public function getArchivedPaymentPlan($student_id, $course_id)
    {
        try {
            // 1️⃣ Find student
            $student = Student::find($student_id);
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            }

            // 2️⃣ Archived plan (previous one)
            $archivedPlan = StudentPaymentPlan::where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->where('status', 'archived')
                ->latest('updated_at')
                ->first();

            $archivedInstallments = [];
            if ($archivedPlan) {
                $archivedInstallments = PaymentInstallment::where('payment_plan_id', $archivedPlan->id)->get();
            }

            // 3️⃣ Get student's current registration
            $currentReg = SemesterRegistration::where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->latest('registration_date')
                ->first();

            if (!$currentReg) {
                return response()->json([
                    'success' => true,
                    'archived_plan' => $archivedPlan,
                    'archived_installments' => $archivedInstallments,
                    'current_plan' => null,
                    'message' => 'No active registration found.'
                ]);
            }

            $intakeId = $currentReg->intake_id;
            $courseId = $currentReg->course_id;

            // 4️⃣ Get payment plan for this intake & course
            $currentPlan = PaymentPlan::where('intake_id', $intakeId)
                ->where('course_id', $courseId)
                ->latest('updated_at')
                ->first();

            if (!$currentPlan) {
                return response()->json([
                    'success' => true,
                    'archived_plan' => $archivedPlan,
                    'archived_installments' => $archivedInstallments,
                    'current_plan' => null,
                    'message' => 'No payment plan found for this intake.'
                ]);
            }

            // 5️⃣ Decode the installments JSON safely
            $installments = [];
            if ($currentPlan->installments) {
                $raw = $currentPlan->installments;
                if (is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    if (is_string($decoded)) $decoded = json_decode($decoded, true);
                    $raw = $decoded ?? [];
                }

                $installments = collect($raw)->map(function ($item) use ($currentPlan) {
                    return [
                        'installment_number' => $item['installment_number'] ?? null,
                        'due_date' => $item['due_date'] ?? null,
                        'base_amount' => $item['local_amount'] ?? 0,
                        'amount' => ($item['local_amount'] ?? 0) + ($item['international_amount'] ?? 0),
                        'international_amount' => $item['international_amount'] ?? 0,
                        'currency' => $currentPlan->international_currency ?? 'USD',
                        'status' => 'active',
                    ];
                });

            }

            // ✅ Return everything
            return response()->json([
                'success' => true,
                'archived_plan' => $archivedPlan,
                'archived_installments' => $archivedInstallments,
                'current_plan' => [
                    'plan' => $currentPlan,
                    'installments' => $installments
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payment plan.',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Save New Payment Plan for Re-registered Student
     */

public function saveNewPaymentPlan(Request $request)
{
    $validated = $request->validate([
        'student_id' => 'required|integer|exists:students,student_id',
        'course_id' => 'required|integer|exists:courses,course_id',
        'installments' => 'required|array|min:1',
        'installments.*.due_date' => 'required|date',
        'installments.*.local_amount' => 'nullable|numeric|min:0',
        'installments.*.international_amount' => 'nullable|numeric|min:0',
        'installments.*.currency' => 'nullable|string|max:10'
    ]);

    try {
        DB::beginTransaction();

        // create main payment plan
        $plan = StudentPaymentPlan::create([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'payment_plan_type' => 'installments',
            'status' => 'active',
            'total_amount' => collect($validated['installments'])->sum(fn($i) => ($i['local_amount'] ?? 0) + ($i['international_amount'] ?? 0)),
            'final_amount' => collect($validated['installments'])->sum(fn($i) => ($i['local_amount'] ?? 0) + ($i['international_amount'] ?? 0)),
        ]);

        foreach ($validated['installments'] as $index => $item) {
            $local = $item['local_amount'] ?? 0;
            $intl = $item['international_amount'] ?? 0;
            $currency = $item['currency'] ?? null;

            // Determine installment type based on amounts
            $installmentType = 'local'; // default
            if ($intl > 0 && $local > 0) {
                $installmentType = 'both';
            } elseif ($intl > 0) {
                $installmentType = 'international';
            }

            PaymentInstallment::create([
                'payment_plan_id' => $plan->id,
                'installment_number' => $index + 1,
                'due_date' => $item['due_date'],
                'amount' => $local, // local amount
                'base_amount' => $local,
                'international_amount' => $intl > 0 ? $intl : null,
                'international_currency' => $intl > 0 ? ($currency ?? 'USD') : null,
                'status' => 'pending',
                'installment_type' => $installmentType, // Added this line
            ]);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'New payment plan created successfully.'
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error saving payment plan.',
            'error' => $e->getMessage()
        ]);
    }
}
public function getCreatedPaymentPlans($student_id, $course_id)
{
    try {
        $plans = StudentPaymentPlan::where('student_id', $student_id)
            ->where('course_id', $course_id)
            ->orderByDesc('created_at')
            ->get();

        if ($plans->isEmpty()) {
            return response()->json([
                'success' => true,
                'installments' => [],
                'message' => 'No created plans found.'
            ]);
        }

        $installments = PaymentInstallment::whereIn('payment_plan_id', $plans->pluck('id'))
            ->orderBy('due_date')
            ->get();

        return response()->json([
            'success' => true,
            'installments' => $installments
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching created payment plans.',
            'error' => $e->getMessage()
        ]);
    }
}


}
