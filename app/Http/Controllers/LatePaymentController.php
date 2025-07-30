<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\PaymentDetail;
use App\Models\StudentPaymentPlan;
use App\Models\PaymentInstallment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LatePaymentController extends Controller
{
    /**
     * Display the late payment page.
     */
    public function index()
    {
        return view('late_payment');
    }

    /**
     * Get payment plan for local course fee.
     */
    public function getPaymentPlan(Request $request)
    {
        try {
            $request->validate([
                'student_nic' => 'required|string',
                'course_id' => 'required|integer|exists:courses,course_id',
            ]);

            // Find student by NIC
            $student = Student::where('id_value', $request->student_nic)->first();
            
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found with the provided NIC.'], Response::HTTP_NOT_FOUND);
            }

            // Get course registration for this student and course
            $registration = CourseRegistration::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->with(['student', 'course', 'intake'])
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], Response::HTTP_NOT_FOUND);
            }

            // Get payment plan
            $paymentPlan = StudentPaymentPlan::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->with(['installments'])
                ->first();

            if (!$paymentPlan) {
                return response()->json(['success' => false, 'message' => 'No payment plan found for this student and course.'], Response::HTTP_NOT_FOUND);
            }

            // Calculate course fee (local course fee)
            $courseFee = $registration->intake->course_fee ?? $registration->course->course_fee ?? 0;

            $studentData = [
                'student_id' => $registration->student->student_id,
                'student_name' => $registration->student->full_name,
                'student_nic' => $registration->student->id_value,
                'course_id' => $request->course_id,
                'course_name' => $registration->course->course_name,
                'intake_name' => $registration->intake->batch ?? 'N/A',
                'course_fee' => $courseFee,
                'total_amount' => $courseFee,
                'registration_date' => $registration->registration_date,
                'status' => $registration->status,
            ];

            // Get installments with late payment status
            $installments = $paymentPlan->installments->map(function ($installment) {
                $dueDate = \Carbon\Carbon::parse($installment->due_date);
                $isLate = $dueDate->isPast() && $installment->status !== 'paid';
                $daysLate = $isLate ? $dueDate->diffInDays(now()) : 0;
                
                return [
                    'installment_number' => $installment->installment_number,
                    'due_date' => $installment->due_date,
                    'amount' => $installment->amount,
                    'status' => $installment->status,
                    'is_late' => $isLate,
                    'days_late' => $daysLate,
                    'late_fee' => $isLate ? $this->calculateLateFee($installment->amount, $daysLate) : 0,
                    'total_due' => $installment->amount + ($isLate ? $this->calculateLateFee($installment->amount, $daysLate) : 0),
                ];
            });

            return response()->json([
                'success' => true,
                'student' => $studentData,
                'payment_plan' => [
                    'plan_id' => $paymentPlan->id,
                    'plan_type' => $paymentPlan->payment_plan_type,
                    'total_amount' => $paymentPlan->total_amount,
                    'final_amount' => $paymentPlan->final_amount,
                    'installments' => $installments,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get paid local course fee payment details.
     */
    public function getPaidPaymentDetails(Request $request)
    {
        try {
            $request->validate([
                'student_nic' => 'required|string',
                'course_id' => 'required|integer|exists:courses,course_id',
            ]);

            // Find student by NIC
            $student = Student::where('id_value', $request->student_nic)->first();
            
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found with the provided NIC.'], Response::HTTP_NOT_FOUND);
            }

            // Get course registration
            $registration = CourseRegistration::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], Response::HTTP_NOT_FOUND);
            }

            // Get paid payment details for course fee
            $paidPayments = PaymentDetail::where('student_id', $student->student_id)
                ->where('course_registration_id', $registration->id)
                ->where('status', 'paid') // Only paid payments
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'payment_id' => $payment->id,
                        'payment_date' => $payment->created_at->format('Y-m-d'),
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'receipt_no' => $payment->transaction_id,
                        'installment_number' => $payment->installment_number,
                        'due_date' => $payment->due_date ? $payment->due_date->format('Y-m-d') : null,
                        'paid_slip_path' => $payment->paid_slip_path,
                        'remarks' => $payment->remarks,
                        'days_late' => $this->calculateDaysLate($payment->due_date, $payment->created_at),
                        'late_fee_paid' => $this->calculateLateFeePaid($payment->amount, $payment->due_date, $payment->created_at),
                    ];
                });

            return response()->json([
                'success' => true,
                'paid_payments' => $paidPayments
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get student courses by NIC.
     */
    public function getStudentCourses(Request $request)
    {
        try {
            $request->validate([
                'student_nic' => 'required|string',
            ]);

            // Find student by NIC
            $student = Student::where('id_value', $request->student_nic)->first();
            
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found with the provided NIC.'], Response::HTTP_NOT_FOUND);
            }

            // Get courses that the student is registered for
            $courses = CourseRegistration::where('student_id', $student->student_id)
                ->with(['course'])
                ->get()
                ->map(function ($registration) {
                    return [
                        'course_id' => $registration->course->course_id,
                        'course_name' => $registration->course->course_name,
                        'registration_date' => $registration->registration_date->format('Y-m-d'),
                        'status' => $registration->status,
                    ];
                });

            return response()->json([
                'success' => true,
                'courses' => $courses
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Calculate late fee based on amount and days late.
     */
    private function calculateLateFee($amount, $daysLate)
    {
        // Late fee calculation: 5% per month (approximately 0.167% per day)
        $dailyRate = 0.00167; // 0.167% per day
        $lateFee = $amount * $dailyRate * $daysLate;
        
        // Cap late fee at 25% of original amount
        $maxLateFee = $amount * 0.25;
        
        return min($lateFee, $maxLateFee);
    }

    /**
     * Calculate days late for a payment.
     */
    private function calculateDaysLate($dueDate, $paymentDate)
    {
        if (!$dueDate || !$paymentDate) {
            return 0;
        }

        $due = \Carbon\Carbon::parse($dueDate);
        $paid = \Carbon\Carbon::parse($paymentDate);
        
        if ($paid->isAfter($due)) {
            return $due->diffInDays($paid);
        }
        
        return 0;
    }

    /**
     * Calculate late fee paid for a payment.
     */
    private function calculateLateFeePaid($amount, $dueDate, $paymentDate)
    {
        if (!$dueDate || !$paymentDate) {
            return 0;
        }

        $dueDate = \Carbon\Carbon::parse($dueDate);
        $paymentDate = \Carbon\Carbon::parse($paymentDate);

        if ($paymentDate->lte($dueDate)) {
            return 0; // Payment was on time
        }

        $daysLate = $dueDate->diffInDays($paymentDate);
        return $this->calculateLateFee($amount, $daysLate);
    }
} 