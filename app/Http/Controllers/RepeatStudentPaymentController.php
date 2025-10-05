<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SemesterRegistration;
use App\Models\StudentPaymentPlan;
use App\Models\PaymentInstallment;

class RepeatStudentPaymentController extends Controller
{
    /**
     * 🔹 Show Repeat Student Payment Plan Page
     */
    public function index()
    {
        return view('repeat_students.payment_plan');
    }

    /**
     * 🔹 Get Archived Payment Plan for Student + Course
     * (used by: /api/repeat-payment-plan/{student_id}/{course_id})
     */
    public function getArchivedPaymentPlan($student_id, $course_id)
    {
        try {
            // Find student first
            $student = Student::find($student_id);
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ]);
            }

            // Find archived plan
            $plan = StudentPaymentPlan::where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->where('status', 'archived')
                ->latest('updated_at')
                ->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No archived payment plan found.'
                ]);
            }

            // Get related installments
            $installments = PaymentInstallment::where('payment_plan_id', $plan->id)->get();

            return response()->json([
                'success' => true,
                'plan' => $plan,
                'installments' => $installments
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching archived plan.',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🔹 Save New Payment Plan for Re-registered Student
     * (used by: /repeat-student-payment/save)
     */
    public function saveNewPaymentPlan(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,student_id',
            'course_id' => 'required|integer|exists:courses,course_id',
            'installments' => 'required|array|min:1',
            'installments.*.due_date' => 'required|date',
            'installments.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create new plan
            $plan = StudentPaymentPlan::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'payment_plan_type' => 'installments',
                'status' => 'active',
                'total_amount' => collect($validated['installments'])->sum('amount'),
                'final_amount' => collect($validated['installments'])->sum('amount'),
            ]);

            // Add installments
            foreach ($validated['installments'] as $index => $item) {
                PaymentInstallment::create([
                    'payment_plan_id' => $plan->id,
                    'installment_number' => $index + 1,
                    'due_date' => $item['due_date'],
                    'amount' => $item['amount'],
                    'status' => 'pending',
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
}
