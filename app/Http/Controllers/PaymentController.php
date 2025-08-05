<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Student;
use App\Models\PaymentDetail;
use App\Models\PaymentPlan;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

class PaymentController extends Controller
{
    /**
     * Show the payment management view.
     */
    public function index()
    {
        $courses = Course::orderBy('course_name')->get();
        $intakes = Intake::join('courses', 'intakes.course_name', '=', 'courses.course_name')
            ->select('intakes.*', 'courses.course_name as course_display_name')
            ->get()
            ->map(function ($intake) {
                $intake->intake_display_name = $intake->course_display_name . ' - ' . $intake->intake_no;
                return $intake;
            });

        return view('payment', compact('courses', 'intakes'));
    }

    /**
     * Get available discounts.
     */
    public function getDiscounts()
    {
        try {
            $discounts = \App\Models\Discount::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'discounts' => $discounts
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment details for a student and payment type.
     */
    public function getPaymentDetails(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|string',
                'course_id' => 'required|string',
                'payment_type' => 'required|string',
            ]);

            // Find student by ID/NIC
            $student = Student::where('student_id', $request->student_id)
                ->orWhere('id_value', $request->student_id)
                ->first();
            
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.'], Response::HTTP_NOT_FOUND);
            }

            // Get course registration for this student and specific course
            $registration = CourseRegistration::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->with(['course', 'intake'])
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for the selected course.'], Response::HTTP_NOT_FOUND);
            }

            // Get student-specific payment plan (with discounts and loans applied)
            $studentPaymentPlan = \App\Models\StudentPaymentPlan::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->with(['installments', 'discounts'])
                ->first();

            $paymentDetails = [];

            if ($studentPaymentPlan) {
                // Use student-specific payment plan with final amounts after discounts
                if (in_array($request->payment_type, ['registration_fee'])) {
                    $amount = $registration->intake->registration_fee ?? 0;
                    if ($amount > 0) {
                        $paymentDetails[] = [
                            'installment_number' => 1,
                            'due_date' => now()->addDays(30)->toDateString(),
                            'amount' => $amount,
                            'currency' => 'LKR',
                            'paid_date' => null,
                            'status' => 'pending',
                            'receipt_no' => null
                        ];
                    }
                } else {
                    // Handle installment-based payments with final amounts after discounts
                    foreach ($studentPaymentPlan->installments as $installment) {
                        if ($installment->amount > 0) {
                            $paymentDetails[] = [
                                'installment_number' => $installment->installment_number,
                                'due_date' => $installment->due_date,
                                'amount' => $installment->amount, // Final amount after discounts
                                'currency' => ($request->payment_type === 'franchise_fee') ? 
                                    ($registration->intake->franchise_payment_currency ?? 'LKR') : 'LKR',
                                'paid_date' => null,
                                'status' => 'pending',
                                'receipt_no' => null
                            ];
                        }
                    }
                }
            } else {
                // Fallback to general payment plan if no student-specific plan exists
                $paymentPlan = PaymentPlan::where('course_id', $request->course_id)
                    ->where('intake_id', $registration->intake_id)
                    ->first();

                if ($paymentPlan && $paymentPlan->installments) {
                    $installmentsData = $paymentPlan->installments;
                    if (is_string($installmentsData)) {
                        $installmentsData = json_decode($installmentsData, true);
                    }

                    if (is_array($installmentsData)) {
                        if (in_array($request->payment_type, ['registration_fee'])) {
                            $amount = $paymentPlan->registration_fee ?? 0;
                            if ($amount > 0) {
                                $paymentDetails[] = [
                                    'installment_number' => 1,
                                    'due_date' => now()->addDays(30)->toDateString(),
                                    'amount' => $amount,
                                    'currency' => 'LKR',
                                    'paid_date' => null,
                                    'status' => 'pending',
                                    'receipt_no' => null
                                ];
                            }
                        } else {
                            // For course_fee, we need to calculate final amounts with discounts and loans
                            if ($request->payment_type === 'course_fee') {
                                // Get student-specific payment plan to check for SLT loan
                                $studentPlan = \App\Models\StudentPaymentPlan::where('student_id', $student->student_id)
                                    ->where('course_id', $request->course_id)
                                    ->first();
                                
                                // Get SLT loan information
                                $sltLoanAmount = 0;
                                if ($studentPlan && $studentPlan->slt_loan_applied === 'yes') {
                                    $sltLoanAmount = $studentPlan->slt_loan_amount;
                                }
                                
                                // Get student-specific discounts if any
                                $discounts = collect();
                                if ($studentPlan) {
                                    $discounts = $studentPlan->discounts;
                                } else {
                                    // Fallback to active discounts if no student-specific plan
                                    $discounts = \App\Models\Discount::where('status', 'active')->get();
                                }
                                
                                foreach ($installmentsData as $installment) {
                                    $baseAmount = $installment['local_amount'] ?? 0;
                                    $finalAmount = $this->calculateFinalAmount($baseAmount, $discounts, $sltLoanAmount, count($installmentsData));
                                    
                                    if ($finalAmount > 0) {
                                        $paymentDetails[] = [
                                            'installment_number' => $installment['installment_number'] ?? 1,
                                            'due_date' => $installment['due_date'] ?? now()->addDays(30)->toDateString(),
                                            'amount' => $finalAmount,
                                            'currency' => 'LKR',
                                            'paid_date' => null,
                                            'status' => 'pending',
                                            'receipt_no' => null
                                        ];
                                    }
                                }
                            } else {
                                // For other payment types, use the original logic
                                foreach ($installmentsData as $installment) {
                                    $amount = 0;
                                    switch ($request->payment_type) {
                                        case 'franchise_fee':
                                            $amount = $installment['international_amount'] ?? 0;
                                            break;
                                        default:
                                            $amount = $installment['local_amount'] ?? 0;
                                            break;
                                    }
                                    if ($amount > 0) {
                                        $paymentDetails[] = [
                                            'installment_number' => $installment['installment_number'] ?? 1,
                                            'due_date' => $installment['due_date'] ?? now()->addDays(30)->toDateString(),
                                            'amount' => $amount,
                                            'currency' => ($request->payment_type === 'franchise_fee') ? 
                                                ($registration->intake->franchise_payment_currency ?? 'LKR') : 'LKR',
                                            'paid_date' => null,
                                            'status' => 'pending',
                                            'receipt_no' => null
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // If no payment plan exists or no installments found, create a default entry
            if (empty($paymentDetails)) {
                $defaultAmount = 0;
                
                switch ($request->payment_type) {
                    case 'course_fee':
                        $defaultAmount = $registration->course->course_fee ?? 0;
                        break;
                    case 'franchise_fee':
                        $defaultAmount = $registration->course->international_fee ?? 0;
                        break;
                    case 'registration_fee':
                        $defaultAmount = $registration->registration_fee ?? 0;
                        break;
                    case 'library_fee':
                    case 'hostel_fee':
                        // These fees are not in the database yet, so return empty
                        $defaultAmount = 0;
                        break;
                    default:
                        $defaultAmount = $registration->course->course_fee ?? 0;
                        break;
                }

                if ($defaultAmount > 0) {
                    $paymentDetails[] = [
                        'installment_number' => 1,
                        'due_date' => now()->addDays(30)->toDateString(),
                        'amount' => $defaultAmount,
                        'paid_date' => null,
                        'status' => 'pending',
                        'receipt_no' => null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'payment_details' => $paymentDetails
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment plan installments for a student and course.
     */
    public function getPaymentPlanInstallments(Request $request)
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

            // Get course registration to find the intake
            $registration = CourseRegistration::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->with(['intake'])
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], Response::HTTP_NOT_FOUND);
            }

            // Get payment plan for this specific course and intake
            $paymentPlan = \App\Models\PaymentPlan::where('course_id', $request->course_id)
                ->where('intake_id', $registration->intake_id)
                ->first();

            \Log::info('Payment plan query result:', [
                'course_id' => $request->course_id,
                'intake_id' => $registration->intake_id,
                'payment_plan_found' => $paymentPlan ? 'yes' : 'no',
                'payment_plan_id' => $paymentPlan ? $paymentPlan->id : null,
                'installments' => $paymentPlan ? $paymentPlan->installments : null
            ]);

            if (!$paymentPlan) {
                return response()->json(['success' => false, 'message' => 'No payment plan found for this course and intake. Please create a payment plan in the Payment Plan page first.'], Response::HTTP_NOT_FOUND);
            }

            \Log::info('Processing installments:', [
                'installments_count' => is_array($paymentPlan->installments) ? count($paymentPlan->installments) : 0,
                'installments_data' => $paymentPlan->installments
            ]);

            // Prepare installment data from payment plan
            $installments = [];
            $localFeeTotal = 0;
            
            // Decode installments if it's a JSON string
            $installmentsData = $paymentPlan->installments;
            if (is_string($installmentsData)) {
                $installmentsData = json_decode($installmentsData, true);
            }
            
            if ($installmentsData && is_array($installmentsData)) {
                // First pass: calculate total local fee and filter local fee installments
                $localFeeInstallments = [];
                foreach ($installmentsData as $index => $installment) {
                    $localAmount = $installment['local_amount'] ?? 0;
                    $internationalAmount = $installment['international_amount'] ?? 0;
                    
                    // Only include installments that have local amount > 0
                    if ($localAmount > 0) {
                        $localFeeInstallments[] = $installment;
                        // Add to total only local amounts for discount calculation
                        $localFeeTotal += $localAmount;
                    }
                }

                // Second pass: create installments with proper discount and SLT loan logic
                foreach ($localFeeInstallments as $index => $installment) {
                    $installmentNumber = $installment['installment_number'] ?? ($index + 1);
                    $localAmount = $installment['local_amount'] ?? 0;
                    $internationalAmount = $installment['international_amount'] ?? 0;
                    
                    // Use local amount since we're only showing local installments
                    $amount = $localAmount;
                    $dueDate = $installment['due_date'] ?? null;
                    
                    // Initialize discount and SLT loan
                    $discountText = '';
                    $sltLoanText = '';
                    $finalAmount = $amount;
                    
                    // Apply discount only to the last installment
                    $isLastInstallment = ($index === count($localFeeInstallments) - 1);
                    if ($isLastInstallment && $paymentPlan->apply_discount && $paymentPlan->discount) {
                        if ($paymentPlan->discount > 0) {
                            $discountAmount = ($localFeeTotal * $paymentPlan->discount) / 100;
                            $discountText = 'Discount (' . $paymentPlan->discount . '% on total)';
                            $finalAmount -= $discountAmount;
                        }
                    }

                    // SLT loan will be applied to every installment (this will be handled by frontend)
                    // For now, we'll return the base amount and let frontend handle SLT loan

                    $installments[] = [
                        'installment_number' => $installmentNumber,
                        'due_date' => $dueDate,
                        'amount' => $amount,
                        'discount' => $discountText,
                        'slt_loan' => '', // Will be handled by frontend
                        'final_amount' => max(0, $finalAmount),
                        'status' => 'pending',
                        'is_last_installment' => $isLastInstallment,
                        'local_fee_total' => $localFeeTotal
                    ];
                }
            }

            \Log::info('Final installments array:', [
                'installments_count' => count($installments),
                'installments' => $installments
            ]);

            return response()->json([
                'success' => true,
                'installments' => $installments,
                'payment_plan' => [
                    'id' => $paymentPlan->id,
                    'location' => $paymentPlan->location,
                    'local_fee' => $paymentPlan->local_fee,
                    'registration_fee' => $paymentPlan->registration_fee,
                    'total_amount' => $paymentPlan->local_fee + $paymentPlan->registration_fee,
                    'apply_discount' => $paymentPlan->apply_discount,
                    'discount' => $paymentPlan->discount,
                    'local_fee_total' => $localFeeTotal
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get courses for a specific student based on NIC.
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
                ->with('course')
                ->get()
                ->map(function ($registration) {
                    return [
                        'course_id' => $registration->course->course_id,
                        'course_name' => $registration->course->course_name,
                        'registration_date' => $registration->registration_date,
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
     * Get payment plans for students.
     */
    public function getPaymentPlans(Request $request)
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

            // Use intake-based fees if available, otherwise fall back to course fees
            $courseFee = $registration->intake->course_fee ?? $registration->course->course_fee ?? 0;
            $franchiseFee = $registration->intake->franchise_payment ?? $registration->course->franchise_payment ?? 0;
            $registrationFee = $registration->intake->registration_fee ?? $registration->registration_fee ?? 0;
            $totalAmount = $courseFee + $franchiseFee + $registrationFee;
            
            $studentData = [
                'student_id' => $registration->student->student_id,
                'student_name' => $registration->student->full_name,
                'student_nic' => $registration->student->id_value,
                'course_id' => $request->course_id,
                'course_name' => $registration->course->course_name,
                'intake_name' => $registration->intake->batch ?? 'N/A',
                'course_fee' => $courseFee,
                'franchise_fee' => $franchiseFee,
                'registration_fee' => $registrationFee,
                'total_amount' => $totalAmount,
                'registration_date' => $registration->registration_date,
                'status' => $registration->status,
            ];

            return response()->json([
                'success' => true,
                'student' => $studentData
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    /**
     * Create a new payment plan for a student and course.
     */
    public function createPaymentPlan(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,student_id',
                'course_id' => 'required|exists:courses,course_id',
                'payment_plan_type' => 'required|in:installments,full',
                'discounts' => 'nullable|array',
                'discounts.*.discount_id' => 'required|integer|exists:discounts,id',
                'discounts.*.discount_type' => 'required|in:percentage,amount',
                'discounts.*.discount_value' => 'required|numeric|min:0',
                'slt_loan_applied' => 'nullable|in:yes',
                'slt_loan_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'final_amount' => 'required|numeric|min:0',
                'installments' => 'required|array|min:1',
                'installments.*.installment_number' => 'required|integer|min:1',
                'installments.*.due_date' => 'required|date',
                'installments.*.amount' => 'required|numeric|min:0',
                'installments.*.status' => 'required|in:pending,paid,overdue'
            ]);

            // Check if student is registered for this course
            $registration = CourseRegistration::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Student is not registered for this course.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Check if payment plan already exists
            $existingPlan = \App\Models\StudentPaymentPlan::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->first();

            if ($existingPlan) {
                return response()->json([
                    'success' => false, 
                    'message' => 'A payment plan already exists for this student and course.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Create payment plan
            $paymentPlan = \App\Models\StudentPaymentPlan::create([
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'payment_plan_type' => $request->payment_plan_type,
                'slt_loan_applied' => $request->slt_loan_applied,
                'slt_loan_amount' => $request->slt_loan_amount,
                'total_amount' => $request->total_amount,
                'final_amount' => $request->final_amount,
                'status' => 'active'
            ]);

            \Log::info('Payment plan created:', [
                'payment_plan_id' => $paymentPlan->id,
                'student_id' => $paymentPlan->student_id,
                'course_id' => $paymentPlan->course_id,
                'payment_plan_type' => $paymentPlan->payment_plan_type,
                'total_amount' => $paymentPlan->total_amount,
                'final_amount' => $paymentPlan->final_amount
            ]);

            // Create payment plan discounts
            if ($request->discounts && is_array($request->discounts)) {
                foreach ($request->discounts as $discount) {
                    \App\Models\PaymentPlanDiscount::create([
                        'payment_plan_id' => $paymentPlan->id,
                        'discount_id' => $discount['discount_id'],
                        'discount_type' => $discount['discount_type'],
                        'discount_value' => $discount['discount_value']
                    ]);
                }
            }

            // Create installments
            $installmentCount = 0;
            foreach ($request->installments as $installment) {
                \App\Models\PaymentInstallment::create([
                    'payment_plan_id' => $paymentPlan->id,
                    'installment_number' => $installment['installment_number'],
                    'due_date' => $installment['due_date'],
                    'amount' => $installment['amount'],
                    'status' => $installment['status']
                ]);
                $installmentCount++;
            }

            \Log::info('Installments created:', [
                'payment_plan_id' => $paymentPlan->id,
                'installment_count' => $installmentCount
            ]);

            // Update course registration with payment plan
            $registration->update([
                'payment_plan_id' => $paymentPlan->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment plan created successfully.',
                'payment_plan_id' => $paymentPlan->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save payment plans.
     */
    public function savePaymentPlans(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,student_id',
                'course_id' => 'required|exists:courses,course_id',
                'payment_plan' => 'required|string',
            ]);

            // Update course registration with payment plan
            $registration = CourseRegistration::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], Response::HTTP_NOT_FOUND);
            }

            $registration->update(['payment_plan' => $request->payment_plan]);

            return response()->json(['success' => true, 'message' => 'Payment plan saved successfully.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate payment slip for pending payments.
     */
    public function generatePaymentSlip(Request $request)
{
    try {
        $request->validate([
            'student_id' => 'required|string',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'installment_number' => 'nullable|integer',
            'due_date' => 'nullable|date',
            'conversion_rate' => 'nullable|numeric|min:0',
            'currency_from' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Find student
        $student = Student::where('student_id', $request->student_id)
            ->orWhere('id_value', $request->student_id)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // Get registration
        $registration = CourseRegistration::where('student_id', $student->student_id)
            ->with(['course', 'intake'])
            ->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Student is not registered for any course.'], 404);
        }

        // ✅ Check for existing payment record with same student_id, course_registration_id, and installment_number
        $existingPayment = PaymentDetail::where('student_id', $student->student_id)
            ->where('course_registration_id', $registration->id)
            ->where('installment_number', $request->installment_number)
            ->where('status', 'pending') // Optional: only return if still unpaid
            ->first();

        if ($existingPayment) {
            // Build slipData from existing record
            $existingSlipData = [
                'receipt_no' => $existingPayment->transaction_id,
                'student_id' => $student->student_id,
                'student_name' => $student->full_name,
                'student_nic' => $student->id_value,
                'course_name' => $registration->course->course_name ?? 'N/A',
                'course_code' => $registration->course->course_code ?? 'N/A',
                'intake' => $registration->intake->batch ?? 'N/A',
                'intake_id' => $registration->intake->intake_id ?? null,
                'payment_type' => $request->payment_type,
                'payment_type_display' => $this->getPaymentTypeDisplay($request->payment_type),
                'amount' => $existingPayment->amount,
                'installment_number' => $existingPayment->installment_number,
                'due_date' => $existingPayment->due_date,
                'payment_date' => $existingPayment->created_at->toDateString(),
                'payment_method' => $existingPayment->payment_method ?? 'Cash',
                'remarks' => $existingPayment->remarks,
                'status' => $existingPayment->status,
                'location' => $registration->location ?? 'N/A',
                'registration_date' => $registration->registration_date,
                'course_fee' => 0, // optional
                'franchise_fee' => 0, // optional
                'franchise_fee_currency' => $registration->intake->franchise_payment_currency ?? 'LKR',
                'registration_fee' => $registration->intake->registration_fee ?? 0,
                'conversion_rate' => $request->conversion_rate,
                'currency_from' => $request->currency_from,
                'lkr_amount' => $request->conversion_rate ? ($existingPayment->amount * $request->conversion_rate) : null,
                'generated_at' => $existingPayment->created_at->toDateTimeString(),
                'valid_until' => now()->addDays(7)->toDateString(),
            ];

            return response()->json([
                'success' => true,
                'slip_data' => $existingSlipData,
                'message' => 'Existing payment slip found.'
            ]);
        }

        // ➕ Generate new receipt number
        $today = date('Ymd');
        $latest = PaymentDetail::where('transaction_id', 'like', "RCP{$today}%")
            ->orderBy('transaction_id', 'desc')
            ->first();

        $lastNumber = $latest ? (int) substr($latest->transaction_id, -4) : 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $receiptNo = 'RCP' . $today . $nextNumber;

        // ➕ Insert into DB
        $payment = PaymentDetail::create([
            'student_id' => $student->student_id,
            'course_registration_id' => $registration->id,
            'amount' => $request->amount,
            'payment_method' => 'Cash',
            'transaction_id' => $receiptNo,
            'status' => 'pending',
            'remarks' => $request->remarks,
            'installment_number' => $request->installment_number,
            'due_date' => $request->due_date,
        ]);

        // 📦 Create slipData for return
        $slipData = [
            'receipt_no' => $receiptNo,
            'student_id' => $student->student_id,
            'student_name' => $student->full_name,
            'student_nic' => $student->id_value,
            'course_name' => $registration->course->course_name ?? 'N/A',
            'course_code' => $registration->course->course_code ?? 'N/A',
            'intake' => $registration->intake->batch ?? 'N/A',
            'intake_id' => $registration->intake->intake_id ?? null,
            'payment_type' => $request->payment_type,
            'payment_type_display' => $this->getPaymentTypeDisplay($request->payment_type),
            'amount' => $request->amount,
            'installment_number' => $request->installment_number,
            'due_date' => $request->due_date,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'Cash',
            'remarks' => $request->remarks,
            'status' => 'pending',
            'location' => $registration->location ?? 'N/A',
            'registration_date' => $registration->registration_date,
            'course_fee' => 0,
            'franchise_fee' => 0,
            'franchise_fee_currency' => $registration->intake->franchise_payment_currency ?? 'LKR',
            'registration_fee' => $registration->intake->registration_fee ?? 0,
            'conversion_rate' => $request->conversion_rate,
            'currency_from' => $request->currency_from,
            'lkr_amount' => $request->conversion_rate ? ($request->amount * $request->conversion_rate) : null,
            'generated_at' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(7)->toDateString(),
        ];

        return response()->json([
            'success' => true,
            'slip_data' => $slipData,
            'message' => 'New payment slip generated.'
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}


    /**
     * Get payment type display name.
     */
    private function getPaymentTypeDisplay($paymentType)
    {
        $types = [
            'course_fee' => 'Course Fee',
            'franchise_fee' => 'Franchise Fee',
            'registration_fee' => 'Registration Fee',
        ];

        return $types[$paymentType] ?? ucfirst(str_replace('_', ' ', $paymentType));
    }

    /**
     * Download payment slip as PDF.
     */
    public function downloadPaymentSlipPDF(Request $request)
    {
        try {
            $request->validate([
                'receipt_no' => 'required|string',
            ]);

            // Retrieve the generated slip data from session
            $slipData = session('generated_slip_' . $request->receipt_no);
            
            if (!$slipData) {
                return response()->json(['success' => false, 'message' => 'Payment slip not found or expired.'], Response::HTTP_NOT_FOUND);
            }

            // Generate PDF using DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payment_slip', [
                'slipData' => $slipData
            ]);

            // Set PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            // Generate filename
            $filename = 'Payment_Slip_' . $slipData['receipt_no'] . '_' . date('Y-m-d') . '.pdf';

            // Return PDF as download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save payment record after payment is made.
     */
    public function savePaymentRecord(Request $request)
{
    try {
        $request->validate([
            'receipt_no' => 'required|string',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        // ✅ Find the existing payment record by receipt number
        $paymentDetail = PaymentDetail::where('transaction_id', $request->receipt_no)->first();

        if (!$paymentDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found for this receipt number.'
            ], Response::HTTP_NOT_FOUND);
        }

        // ✅ Update the payment record
        $paymentDetail->update([
            'payment_method' => $request->payment_method,
            'status' => 'paid',
            'remarks' => $request->remarks ?? $paymentDetail->remarks,
            'paid_date' => $request->payment_date,
        ]);

        // ✅ Also update installment status if applicable
        if ($paymentDetail->installment_number) {
            $registration = CourseRegistration::find($paymentDetail->course_registration_id);
            if ($registration) {
                $this->updateInstallmentStatus(
                    $paymentDetail->student_id,
                    $registration->course_id,
                    $paymentDetail->installment_number
                );
            }
        }

        // ✅ Clear the slip from session (optional)
        session()->forget('generated_slip_' . $request->receipt_no);

        return response()->json([
            'success' => true,
            'message' => 'Payment record updated successfully.',
            'payment_id' => $paymentDetail->id
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * Update installment status when payment is made.
     */
    private function updateInstallmentStatus($studentId, $courseId, $installmentNumber)
    {
        try {
            $paymentPlan = \App\Models\StudentPaymentPlan::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();

            if ($paymentPlan) {
                $installment = \App\Models\PaymentInstallment::where('payment_plan_id', $paymentPlan->id)
                    ->where('installment_number', $installmentNumber)
                    ->first();

                if ($installment) {
                    $installment->update([
                        'status' => 'paid',
                        'paid_date' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error updating installment status: ' . $e->getMessage());
        }
    }

    /**
     * Upload paid slip and update payment record.
     */
    public function uploadPaidSlip(Request $request)
    {
        try {
            $request->validate([
                'receipt_no' => 'required|string',
                'payment_method' => 'required|string',
                'payment_date' => 'required|date',
                'paid_slip' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'remarks' => 'nullable|string',
            ]);

            // Check if payment record exists
            $existingPayment = PaymentDetail::where('payment_reference', $request->receipt_no)->first();
            
            if ($existingPayment) {
                return response()->json(['success' => false, 'message' => 'Payment record already exists for this receipt number.'], Response::HTTP_BAD_REQUEST);
            }

            // Retrieve the generated slip data
            $slipData = session('generated_slip_' . $request->receipt_no);
            
            if (!$slipData) {
                return response()->json(['success' => false, 'message' => 'Payment slip not found or expired. Please generate a new slip.'], Response::HTTP_NOT_FOUND);
            }

            // Handle file upload
            $file = $request->file('paid_slip');
            $fileName = 'paid_slip_' . $request->receipt_no . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('paid_slips', $fileName, 'public');

            // Find student
            $student = Student::where('student_id', $slipData['student_id'])->first();
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found.'], Response::HTTP_NOT_FOUND);
            }

            // Get course registration
            $registration = CourseRegistration::where('student_id', $student->student_id)
                ->where('course_id', $slipData['course_id'] ?? 1)
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Course registration not found.'], Response::HTTP_NOT_FOUND);
            }

            // Create payment record
            $paymentDetail = PaymentDetail::create([
                'student_id' => $student->student_id,
                'course_registration_id' => $registration->id,
                'amount' => $slipData['amount'],
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->receipt_no,
                'status' => 'paid', // Mark as paid
                'remarks' => $request->remarks ?? $slipData['remarks'],
                'installment_number' => $slipData['installment_number'],
                'due_date' => $slipData['due_date'],
                'paid_slip_path' => $filePath, // Store the uploaded slip path
            ]);

            // Update installment status if this is part of a payment plan
            if ($slipData['installment_number']) {
                $this->updateInstallmentStatus($student->student_id, $registration->course_id, $slipData['installment_number']);
            }

            // Clear the slip from session
            session()->forget('generated_slip_' . $request->receipt_no);

            return response()->json([
                'success' => true, 
                'message' => 'Paid slip uploaded and payment record saved successfully.',
                'payment_id' => $paymentDetail->id,
                'file_path' => $filePath
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment records with slip upload functionality.
     */
    public function getPaymentRecords(Request $request)
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
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], Response::HTTP_NOT_FOUND);
            }

            // Get payment records for this student and course registration
            $records = PaymentDetail::where('student_id', $student->student_id)
                ->where('course_registration_id', $registration->id)
                ->with(['student', 'registration.course'])
                ->get()
                ->map(function($payment) {
                    return [
                        'payment_id' => $payment->id,
                        'student_id' => $payment->student->student_id,
                        'student_name' => $payment->student->full_name,
                        'payment_type' => $payment->payment_type ?? 'course_fee',
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'payment_date' => $payment->created_at->format('Y-m-d'),
                        'receipt_no' => $payment->transaction_id,
                        'status' => $payment->status,
                        'remarks' => $payment->remarks,
                        'installment_number' => $payment->installment_number,
                        'due_date' => $payment->due_date,
                        'paid_slip_path' => $payment->paid_slip_path,
                        'has_slip' => !empty($payment->paid_slip_path),
                    ];
                });

            return response()->json([
                'success' => true,
                'records' => $records
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update payment record.
     */
    public function updatePaymentRecord(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|exists:payment_details,payment_id',
                'payment_type' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'payment_date' => 'required|date',
                'receipt_no' => 'required|string',
                'remarks' => 'nullable|string',
            ]);

            $payment = PaymentDetail::find($request->payment_id);
            $payment->update([
                'payment_type' => $request->payment_type,
                'payment_amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'payment_reference' => $request->receipt_no,
                'remarks' => $request->remarks,
            ]);

            return response()->json(['success' => true, 'message' => 'Payment record updated successfully.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete payment record.
     */
    public function deletePaymentRecord(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|exists:payment_details,payment_id',
            ]);

            PaymentDetail::find($request->payment_id)->delete();

            return response()->json(['success' => true, 'message' => 'Payment record deleted successfully.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment summary for a specific student and course.
     */
    public function getPaymentSummary(Request $request)
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
                ->with(['course', 'intake'])
                ->first();

            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], Response::HTTP_NOT_FOUND);
            }

            // Get all payment records for this student and course registration
            $payments = PaymentDetail::where('student_id', $student->student_id)
                ->where('course_registration_id', $registration->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get student-specific payment plan (with discounts and loans applied)
            $studentPaymentPlan = \App\Models\StudentPaymentPlan::where('student_id', $student->student_id)
                ->where('course_id', $registration->course_id)
                ->with(['installments', 'discounts'])
                ->first();

            // Calculate course fees from student payment plan
            $courseFee = 0;
            $franchiseFee = 0;
            $registrationFee = $registration->intake->registration_fee ?? 0;
            
            if ($studentPaymentPlan) {
                // Use the final amount from student payment plan (after discounts and loans)
                $totalCourseAmount = $studentPaymentPlan->final_amount;
                
                // Calculate individual fees from installments
                foreach ($studentPaymentPlan->installments as $installment) {
                    $courseFee += $installment->amount; // This is the final amount after discounts
                }
            } else {
                // Fallback to general payment plan if no student-specific plan exists
                $paymentPlan = PaymentPlan::where('course_id', $registration->course_id)
                    ->where('intake_id', $registration->intake_id)
                    ->first();
                
                if ($paymentPlan && $paymentPlan->installments) {
                    $installmentsData = $paymentPlan->installments;
                    if (is_string($installmentsData)) {
                        $installmentsData = json_decode($installmentsData, true);
                    }
                    
                    if (is_array($installmentsData)) {
                        foreach ($installmentsData as $installment) {
                            $courseFee += $installment['local_amount'] ?? 0;
                            $franchiseFee += $installment['international_amount'] ?? 0;
                        }
                    }
                }
                
                $totalCourseAmount = $courseFee + $franchiseFee + $registrationFee;
            }

            // Group payments by type
            $paymentTypes = [
                'course_fee' => ['name' => 'Course Fee', 'total' => $courseFee, 'paid' => 0, 'payments' => []],
                'franchise_fee' => ['name' => 'Franchise Fee', 'total' => $franchiseFee, 'paid' => 0, 'payments' => []],
                'registration_fee' => ['name' => 'Registration Fee', 'total' => $registrationFee, 'paid' => 0, 'payments' => []],
                'library_fee' => ['name' => 'Library Fee', 'total' => 0, 'paid' => 0, 'payments' => []],
                'hostel_fee' => ['name' => 'Hostel Fee', 'total' => 0, 'paid' => 0, 'payments' => []],
                'other' => ['name' => 'Other', 'total' => 0, 'paid' => 0, 'payments' => []],
            ];

            // Process payments
            $totalPaid = 0;
            $paymentHistory = [];

            foreach ($payments as $payment) {
                $paymentType = $payment->payment_type ?? 'course_fee';
                $amount = $payment->amount;
                
                // Categorize payment type
                $categorizedType = $this->categorizePaymentType($paymentType);
                
                if (isset($paymentTypes[$categorizedType])) {
                    $paymentTypes[$categorizedType]['paid'] += $amount;
                    $paymentTypes[$categorizedType]['payments'][] = $payment;
                } else {
                    // If unknown type, add to "other"
                    $paymentTypes['other']['paid'] += $amount;
                    $paymentTypes['other']['payments'][] = $payment;
                }
                
                $totalPaid += $amount;
                
                // Add to payment history
                $paymentHistory[] = [
                    'payment_date' => $payment->created_at->format('Y-m-d'),
                    'payment_type' => $this->getPaymentTypeDisplay($paymentType),
                    'amount' => $amount,
                    'payment_method' => $payment->payment_method,
                    'receipt_no' => $payment->transaction_id,
                    'status' => $payment->status === 'paid' ? 'Paid' : 'Pending'
                ];
            }

            // Calculate summary for each payment type
            $paymentDetails = [];
            foreach ($paymentTypes as $type => $data) {
                if ($data['total'] > 0 || $data['paid'] > 0) {
                    $outstanding = $data['total'] - $data['paid'];
                    $paymentRate = $data['total'] > 0 ? round(($data['paid'] / $data['total']) * 100, 2) : 0;
                    
                    // Get installment count and last payment date
                    $installmentCount = count(array_filter($data['payments'], function($p) {
                        return !empty($p->installment_number);
                    }));
                    
                    $lastPayment = collect($data['payments'])->sortByDesc('created_at')->first();
                    $lastPaymentDate = $lastPayment ? $lastPayment->created_at->format('Y-m-d') : null;

                    // Prepare detailed payment records for this type
                    $detailedPayments = [];
                    foreach ($data['payments'] as $payment) {
                        $detailedPayments[] = [
                            'total_amount' => $data['total'],
                            'paid_amount' => $payment->amount,
                            'outstanding' => $data['total'] - $data['paid'],
                            'payment_date' => $payment->created_at->format('Y-m-d'),
                            'due_date' => $payment->due_date ? $payment->due_date->format('Y-m-d') : null,
                            'receipt_no' => $payment->transaction_id,
                            'uploaded_receipt' => $payment->paid_slip_path,
                            'installment_number' => $payment->installment_number,
                            'payment_method' => $payment->payment_method,
                            'status' => $payment->status === 'paid' ? 'Paid' : 'Pending'
                        ];
                    }

                    $paymentDetails[] = [
                        'payment_type' => $data['name'],
                        'total_amount' => $data['total'],
                        'paid_amount' => $data['paid'],
                        'outstanding' => $outstanding,
                        'payment_rate' => $paymentRate,
                        'installment_count' => $installmentCount,
                        'last_payment_date' => $lastPaymentDate,
                        'payments' => $detailedPayments
                    ];
                }
            }

            $totalOutstanding = $totalCourseAmount - $totalPaid;
            $overallPaymentRate = $totalCourseAmount > 0 ? round(($totalPaid / $totalCourseAmount) * 100, 2) : 0;



            $summary = [
                'student' => [
                    'student_id' => $student->student_id,
                    'student_name' => $student->full_name,
                    'course_name' => $registration->course->course_name,
                    'registration_date' => $registration->registration_date->format('Y-m-d'),
                    'total_amount' => $totalCourseAmount
                ],
                'total_amount' => $totalCourseAmount,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
                'payment_rate' => $overallPaymentRate,
                'payment_details' => $paymentDetails,
                'payment_history' => $paymentHistory
            ];

            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Export payment summary.
     */
    public function exportPaymentSummary(Request $request)
    {
        try {
            $request->validate([
                'format' => 'required|in:pdf,excel,csv',
                'summary_data' => 'required|array',
            ]);

            // This is a placeholder for the actual export functionality
            // You would implement PDF, Excel, or CSV generation here
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($request->format) . ' export generated successfully.',
                'download_url' => '/downloads/payment-summary.' . $request->format
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get intakes for course and location.
     */
    public function getIntakesForCourseAndLocation($courseID, $location)
    {
        try {
            $intakes = Intake::where('course_id', $courseID)
                ->where('location', $location)
                ->get();

            return response()->json(['success' => true, 'intakes' => $intakes]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Categorize payment type for summary.
     */
    private function categorizePaymentType($paymentType)
    {
        $types = [
            'course_fee' => 'course_fee',
            'franchise_fee' => 'franchise_fee',
            'registration_fee' => 'registration_fee',
            'library_fee' => 'library_fee',
            'hostel_fee' => 'hostel_fee',
            'other' => 'other', // Default for unknown types
        ];

        return $types[$paymentType] ?? 'other';
    }

    /**
     * Calculate final amount after applying discounts and loans.
     */
    private function calculateFinalAmount($baseAmount, $discounts, $sltLoanAmount, $totalInstallments)
    {
        $finalAmount = $baseAmount;
        
        // Apply percentage discounts
        $totalDiscountPercentage = 0;
        foreach ($discounts as $discount) {
            // Handle both Discount model and PaymentPlanDiscount model
            $discountType = $discount->discount_type ?? $discount->type ?? null;
            $discountValue = $discount->discount_value ?? $discount->value ?? 0;
            
            if ($discountType === 'percentage') {
                $totalDiscountPercentage += $discountValue;
            }
        }
        
        if ($totalDiscountPercentage > 0) {
            $finalAmount = $finalAmount - ($finalAmount * $totalDiscountPercentage / 100);
        }
        
        // Apply fixed amount discounts
        $totalDiscountAmount = 0;
        foreach ($discounts as $discount) {
            // Handle both Discount model and PaymentPlanDiscount model
            $discountType = $discount->discount_type ?? $discount->type ?? null;
            $discountValue = $discount->discount_value ?? $discount->value ?? 0;
            
            if ($discountType === 'fixed') {
                $totalDiscountAmount += $discountValue;
            }
        }
        
        if ($totalDiscountAmount > 0) {
            $finalAmount = $finalAmount - $totalDiscountAmount;
        }
        
        // Apply SLT loan (distributed across installments)
        if ($sltLoanAmount > 0 && $totalInstallments > 0) {
            $sltLoanPerInstallment = $sltLoanAmount / $totalInstallments;
            $finalAmount = $finalAmount - $sltLoanPerInstallment;
        }
        
        // Ensure final amount is not negative
        return max(0, $finalAmount);
    }
} 