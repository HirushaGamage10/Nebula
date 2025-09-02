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
    public function getDiscounts(Request $request)
    {
        try {
            $query = \App\Models\Discount::where('status', 'active');
            
            // Filter by discount category if provided
            if ($request->has('category') && $request->category) {
                $query->where('discount_category', $request->category);
            }
            
            $discounts = $query->orderBy('created_at', 'desc')->get();

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
            'student_id'   => 'required|string',   // Student ID or NIC
            'course_id'    => 'required|integer|exists:courses,course_id',
            'payment_type' => 'required|in:course_fee,franchise_fee,registration_fee',
        ]);

        // find student by student_id or NIC (id_value)
        $student = \App\Models\Student::where('student_id', $request->student_id)
            ->orWhere('id_value', $request->student_id)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // need registration to know intake/location
        $registration = \App\Models\CourseRegistration::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->with(['course','intake'])
            ->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Student is not registered for the selected course.'], 404);
        }

        $rows = [];
        switch ($request->payment_type) {
            case 'course_fee':
                // per-student plan → payment_installments
                $studentPlan = \App\Models\StudentPaymentPlan::where('student_id', $student->student_id)
                    ->where('course_id', $request->course_id)
                    ->first();

                if (!$studentPlan) {
                    return response()->json([
                        'success' => true,
                        'payment_details' => [],
                        'message' => 'No student payment plan found. Create a plan first.',
                    ]);
                }

                $installments = \App\Models\PaymentInstallment::where('payment_plan_id', $studentPlan->id)
                    ->orderBy('installment_number')
                    ->get();

                foreach ($installments as $ins) {
                    $final = $ins->final_amount ?? $ins->amount;
                    $rows[] = [
                    'installment_number'  => $ins->installment_number,
                    'due_date'            => optional($ins->due_date)->toDateString(),
                    'amount'              => (float) $final,
                    'base_amount'         => (float) $ins->amount,
                    'status'              => $ins->status ?? 'pending',
                    'paid_date'           => optional($ins->paid_date)->toDateString(),
                    'receipt_no'          => null,
                    'currency'            => 'LKR',

                    // ✅ Add these fields
                    'approved_late_fee'   => (float) ($ins->approved_late_fee ?? 0),
                    'calculated_late_fee' => (float) ($ins->calculated_late_fee ?? 0),
                ];

                }

                break;

            case 'franchise_fee':
                // intake plan → split by installments JSON: international_amount
                $plan = \App\Models\PaymentPlan::where('course_id', $request->course_id)
                    ->where('intake_id', $registration->intake_id)
                    ->first();

                if (!$plan) {
                    return response()->json(['success' => false, 'message' => 'No payment plan found for this course/intake.'], 404);
                }

                $instData = $plan->installments;
                if (is_string($instData)) {
                    $instData = json_decode($instData, true);
                }

                if (is_array($instData)) {
                    foreach ($instData as $item) {
                        $fx = (float) ($item['international_amount'] ?? 0);
                        if ($fx <= 0) continue; // only franchise (international) rows
                        $rows[] = [
                            'installment_number' => $item['installment_number'] ?? null,
                            'due_date'           => $item['due_date'] ?? null,
                            'amount'             => $fx,
                            'base_amount'        => $fx,
                            'status'             => 'pending',
                            'paid_date'          => null,
                            'receipt_no'         => null,
                            'currency'           => $plan->international_currency ?: 'USD',
                            // pass-through flags/rates so the frontend can show/apply them
                            'apply_tax'          => (bool)($item['apply_tax'] ?? false),
                            'sscl_tax'           => (float)($plan->sscl_tax ?? 0),
                            'bank_charges'       => (float)($plan->bank_charges ?? 0),
                        ];
                    }
                }

                // if there were no franchise rows at all, still respond with empty array
                break;

            case 'registration_fee':
                // intake plan → single row
                $plan = \App\Models\PaymentPlan::where('course_id', $request->course_id)
                    ->where('intake_id', $registration->intake_id)
                    ->first();

                if (!$plan) {
                    return response()->json(['success' => false, 'message' => 'No payment plan found for this course/intake.'], 404);
                }

                $rows[] = [
                    'installment_number' => 1,
                    'due_date'           => now()->toDateString(),
                    'amount'             => (float) $plan->registration_fee,
                    'base_amount'        => (float) $plan->registration_fee,
                    'status'             => 'pending',
                    'paid_date'          => null,
                    'receipt_no'         => null,
                    'currency'           => 'LKR',
                ];
                break;
        }

        return response()->json([
            'success'         => true,
            'payment_details' => $rows,
        ]);

    } catch (\Throwable $e) {
        \Log::error('getPaymentDetails error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['success' => false, 'message' => 'An error occurred: '.$e->getMessage()], 500);
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
            'student_id'        => 'required|exists:students,student_id',
            'course_id'         => 'required|exists:courses,course_id',
            'payment_plan_type' => 'required|in:installments,full',

            'discounts'                     => 'nullable|array',
            'discounts.*.discount_id'       => 'required|integer|exists:discounts,id',
            'discounts.*.discount_type'     => 'required|in:percentage,amount', // "amount" = fixed
            'discounts.*.discount_value'    => 'required|numeric|min:0',

            'slt_loan_applied'  => 'nullable|in:yes',
            'slt_loan_amount'   => 'nullable|numeric|min:0',

            // We compute these from rows (frontend shows bases in `amount`)
            'installments'                          => 'required|array|min:1',
            'installments.*.installment_number'     => 'required|integer|min:1',
            'installments.*.due_date'               => 'required|date',
            'installments.*.amount'                 => 'required|numeric|min:0', // base local amount (pre-discount)
            'installments.*.status'                 => 'required|in:pending,paid,overdue',
        ]);

        // Must be registered
        $registration = \App\Models\CourseRegistration::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Student is not registered for this course.'
            ], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        // No duplicate plan
        $exists = \App\Models\StudentPaymentPlan::where('student_id', $request->student_id)
            ->where('course_id',  $request->course_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A payment plan already exists for this student and course.'
            ], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        return \DB::transaction(function () use ($request, $registration) {

            // ====== 1) Inputs (use FE rows only) ======
            $rows = collect($request->installments)
                ->sortBy('installment_number')
                ->values()
                ->all();

            // Original local total L = sum of bases (pre-discount)
            $L = 0.0;
            foreach ($rows as $r) { $L += (float)($r['amount'] ?? 0); }
            $L = round($L, 2);
            if ($L <= 0) {
                throw new \RuntimeException('Total local fee (sum of installments) must be greater than zero.');
            }

            // Discounts (frontend collects multiple, applies ALL to last row)
            $pct = 0.0; $fixed = 0.0;
            foreach (($request->discounts ?? []) as $d) {
                $type = strtolower($d['discount_type'] ?? '');
                $val  = (float)($d['discount_value'] ?? 0);
                if ($type === 'percentage') $pct  += $val;     // % of L
                if ($type === 'amount')     $fixed += $val;     // fixed LKR
            }

            // FE logic: percentage on L, then fixed, all on LAST row; clamp result to ≥0
            $lastIdx  = count($rows) - 1;
            $lastBase = (float)$rows[$lastIdx]['amount'];
            $pAmt     = $L * ($pct / 100);
            $discAsk  = $pAmt + $fixed;                   // requested to subtract
            $lastDiscEff = min($lastBase, $discAsk);      // effective (clamped)
            $lastDiscBase = round($lastBase - $lastDiscEff, 2);
            if ($lastDiscBase < 0) $lastDiscBase = 0.0;

            // Build discounted bases array A_i
            $discountedBases = [];
            foreach ($rows as $i => $r) {
                $base = (float)$r['amount'];
                $discountedBases[$i] = ($i === $lastIdx) ? $lastDiscBase : round($base, 2);
            }

            // Sum after discounts (ΣAi)
            $sumAfterDiscounts = 0.0;
            foreach ($discountedBases as $Ai) $sumAfterDiscounts += $Ai;
            $sumAfterDiscounts = round($sumAfterDiscounts, 2);

            // Loan S and target total T by your rule:
            // targetTotal = (ΣAi / L) * (L - S)
            $S = ($request->slt_loan_applied === 'yes') ? (float)($request->slt_loan_amount ?? 0) : 0.0;
            $S = max(0, min($S, $L)); // clamp
            $T = ($sumAfterDiscounts > 0)
                ? round(($sumAfterDiscounts / $L) * ($L - $S), 2)
                : 0.0;

            // ====== 2) Compute per-row finals exactly like FE ======
            $computed = [];
            $runningFinals = 0.0;

            foreach ($rows as $i => $r) {
                $base = round((float)$r['amount'], 2);
                $Ai   = round($discountedBases[$i], 2);
                $isLast = ($i === $lastIdx);

                // Precompute Fi
                if (!$isLast) {
                    // Fi = round((Ai / L) * (L - S), 2)
                    $Fi = round(($Ai / $L) * ($L - $S), 2);
                    $runningFinals += $Fi;
                } else {
                    // Last gets the remainder to fix rounding drift
                    $Fi = round($T - $runningFinals, 2);
                }

                $Fi = max(0, $Fi);
                $loanPart = round($Ai - $Fi, 2); // for audit (what FE shows in "SLT Loan")

                // Only last row shows discountApplied value (purely informational)
                $discApplied = $isLast ? round($lastDiscEff, 2) : 0.0;

                $computed[] = [
                    'installment_number' => $r['installment_number'],
                    'due_date'           => $r['due_date'],
                    'status'             => $r['status'],
                    'base'               => $base,
                    'discount_amount'    => $discApplied, // display value
                    'discounted_base'    => $Ai,
                    'loan_amount'        => $loanPart,
                    'final'              => $Fi,
                ];
            }

            // ====== 3) Persist ======
            $plan = \App\Models\StudentPaymentPlan::create([
                'student_id'        => $request->student_id,
                'course_id'         => $request->course_id,
                'payment_plan_type' => $request->payment_plan_type,
                'slt_loan_applied'  => $request->slt_loan_applied,
                'slt_loan_amount'   => $S,
                'total_amount'      => $L,   // original sum
                'final_amount'      => $T,   // FE rule total
                'status'            => 'active',
            ]);

            // Save selected discounts (as chosen)
            if ($request->filled('discounts')) {
                foreach ($request->discounts as $d) {
                    \App\Models\PaymentPlanDiscount::create([
                        'payment_plan_id' => $plan->id,
                        'discount_id'     => $d['discount_id'],
                        'discount_type'   => $d['discount_type'],
                        'discount_value'  => $d['discount_value'],
                    ]);
                }
            }

            // Column presence (safe fallback)
            $hasBase   = \Schema::hasColumn('payment_installments', 'base_amount');
            $hasDisc   = \Schema::hasColumn('payment_installments', 'discount_amount');
            $hasLoan   = \Schema::hasColumn('payment_installments', 'slt_loan_amount');
            $hasFinal  = \Schema::hasColumn('payment_installments', 'final_amount');

            $saved = 0;
            foreach ($computed as $c) {
                // If you don't have extra cols, put FINAL into legacy `amount`
                $legacyAmount = ($hasFinal || $hasBase) ? $c['base'] : $c['final'];

                $data = [
                    'payment_plan_id'    => $plan->id,
                    'installment_number' => $c['installment_number'],
                    'due_date'           => $c['due_date'],
                    'amount'             => $legacyAmount, // legacy
                    'status'             => $c['status'],
                ];
                if ($hasBase)  $data['base_amount']     = $c['base'];
                if ($hasDisc)  $data['discount_amount'] = $c['discount_amount'];
                if ($hasLoan)  $data['slt_loan_amount'] = $c['loan_amount'];
                if ($hasFinal) $data['final_amount']    = $c['final'];

                \App\Models\PaymentInstallment::create($data);
                $saved++;
            }

            // Link plan to registration
            $registration->update(['payment_plan_id' => $plan->id]);

            return response()->json([
                'success'            => true,
                'message'            => 'Payment plan created successfully.',
                'payment_plan_id'    => $plan->id,
                'installments_saved' => $saved,
                'total_amount'       => $L,
                'final_amount'       => $T,
                'debug' => [
                    'L' => $L,
                    'pct_discount_on_L' => $pct,
                    'fixed_discount'    => $fixed,
                    'discount_applied_last' => $lastDiscEff,
                    'sum_after_discounts'   => $sumAfterDiscounts,
                    'loan_total'            => $S,
                    'target_total'          => $T,
                ],
            ], \Illuminate\Http\Response::HTTP_OK);
        });

    } catch (\Illuminate\Validation\ValidationException $ve) {
        $msg = collect($ve->errors())->flatten()->first() ?? 'Validation error';
        return response()->json(['success' => false, 'message' => $msg], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Exception $e) {
        \Log::error('createPaymentPlan error', ['e' => $e]);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
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

    // app/Http/Controllers/PaymentController.php

public function getExistingPaymentPlans(\Illuminate\Http\Request $request)
{
    try {
        $request->validate([
            'student_nic' => 'required|string',
            'course_id'   => 'nullable|integer|exists:courses,course_id',
        ]);

        // find the student by NIC or by student_id
        $student = \App\Models\Student::where('id_value', $request->student_nic)
            ->orWhere('student_id', $request->student_nic)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // fetch plans for the student (optionally for a specific course)
        $plans = \App\Models\StudentPaymentPlan::with(['installments', 'discounts'])
            ->where('student_id', $student->student_id)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->orderByDesc('created_at')
            ->get();

        // map to a frontend-friendly shape
        $payload = $plans->map(function ($p) use ($student) {
            $courseName = \App\Models\Course::where('course_id', $p->course_id)->value('course_name') ?? 'N/A';

            $inst = $p->installments->map(function ($i) {
                // normalize fields; support schemas with/without extra columns
                $baseAmount  = (float) ($i->base_amount ?? $i->amount ?? 0);
                $finalAmount = (float) ($i->final_amount ?? $i->amount ?? 0);

                return [
                    'installment_number' => $i->installment_number,
                    'due_date'           => optional($i->due_date)->format('Y-m-d'),
                    'amount'             => $baseAmount,
                    'discount_amount'    => (float) ($i->discount_amount ?? 0),
                    'slt_loan_amount'    => (float) ($i->slt_loan_amount ?? 0),
                    'final_amount'       => $finalAmount,
                    'status'             => $i->status ?? 'pending',
                ];
            })->values();

            return [
                'payment_plan_id'   => $p->id,
                'student_id'        => $student->student_id,
                'student_name'      => $student->full_name,
                'student_nic'       => $student->id_value,
                'course_id'         => $p->course_id,
                'course_name'       => $courseName,
                'payment_plan_type' => $p->payment_plan_type,
                'total_amount'      => (float) $p->total_amount,
                'final_amount'      => (float) $p->final_amount,
                'status'            => $p->status,
                'installments'      => $inst,
            ];
        });

        return response()->json(['success' => true, 'plans' => $payload], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'An error occurred: '.$e->getMessage()], 500);
    }
}
public function deletePaymentPlan($id)
{
    try {
        $plan = \App\Models\StudentPaymentPlan::find($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Payment plan not found'
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment plan deleted successfully'
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting payment plan: '.$e->getMessage()
        ], 500);
    }
}

// Teleshop constants (from Teleshop Payment slip)
private const TS_PAYMENT_TYPE = 'Miscellaneous';
private const TS_COST_CENTRE  = '5212';
private const TS_ACCOUNT_CODE = '481.910';

// Map "program/course" to Teleshop Payment Code
private function teleshopPaymentCode(string $program): string
{
    // You can make this array configurable later
    $map = [
        'CAIT'           => '1010',
        'Foundation'     => '1020',
        'BTEC DT'        => '1030',
        'BTEC EE'        => '1040',
        'UH'             => '1050',
        'English'        => '1060',
        'BTEC Computing' => '1070',
        'Other Courses'  => '1080',
        'Hostel'         => '1090',
    ];

    // try exact, then loose contains
    foreach ($map as $key => $code) {
        if (strcasecmp($program, $key) === 0 || stripos($program, $key) !== false) {
            return $code;
        }
    }
    return '1080'; // default: Other Courses
}

// Make a clean "Reference 1" like: UH-B09-L5-1st Installment  OR  UH-B09-L5-Franchise Payment
private function teleshopRef1(string $programShort, ?string $batch, ?string $level, string $paymentType, ?int $instNo): string
{
    $left  = trim($programShort);
    $mid1  = $batch ? trim($batch) : '';
    $mid2  = $level ? trim($level) : '';
    $right = ($paymentType === 'franchise_fee')
        ? 'Franchise Payment'
        : ($instNo ? $this->ordinal($instNo) . ' Installment' : 'Installment');

    return implode('-', array_filter([$left, $mid1, $mid2])) . '-' . $right;
}

// Reference Number (e.g., BTEC/UH Number). Fall back to student_id if not present.
private function teleshopRefNumber(\App\Models\Student $student, \App\Models\CourseRegistration $reg): string
{
    // If you store UH/BTEC number somewhere, plug it here:
    // return $student->uh_no ?? $student->btec_no ?? $student->student_id;
    return $student->student_id;
}

private function ordinal(int $n): string
{
    $suf = 'th';
    if (!in_array($n % 100, [11,12,13])) {
        $suf = [1=>'st',2=>'nd',3=>'rd'][$n % 10] ?? 'th';
    }
    return $n . $suf;
}
//Late Fee 
private function calculateLateFee($amount, $daysLate)
{
    if ($daysLate <= 0) return 0;

    $dailyRate = (0.05 / 30); // 5% monthly → daily
    $lateFee   = $amount * $dailyRate * $daysLate;

    return round(min($lateFee, $amount * 0.25), 2);
}


/**
 * Generate payment slip for pending payments.
 */
/**
 * Generate payment slip for pending payments.
 */
public function generatePaymentSlip(Request $request)
{
    try {
        $request->validate([
            'student_id'         => 'required|string',
            'course_id'          => 'required|integer|exists:courses,course_id',
            'payment_type'       => 'required|string', // 'course_fee' | 'franchise_fee' | 'registration_fee'
            'amount'             => 'required|numeric|min:0',
            'installment_number' => 'nullable|integer',
            'due_date'           => 'nullable|date',
            'conversion_rate'    => 'nullable|numeric|min:0',
            'currency_from'      => 'nullable|string',
            'remarks'            => 'nullable|string',
        ]);

        // 🔹 Find Student
        $student = \App\Models\Student::where('student_id', $request->student_id)
            ->orWhere('id_value', $request->student_id)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // 🔹 Check Registration
        $registration = \App\Models\CourseRegistration::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->with(['course', 'intake'])
            ->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Student is not registered for this course.'], 404);
        }

        $course      = $registration->course;
        $intake      = $registration->intake;
        $paymentType = $request->payment_type;
        $amount      = (float) $request->amount;

        // 🔹 Breakdown by payment type
        $courseFee       = $paymentType === 'course_fee'       ? $amount : 0;
        $franchiseFee    = $paymentType === 'franchise_fee'    ? $amount : 0;
        $registrationFee = $paymentType === 'registration_fee' ? ($intake->registration_fee ?? 0) : 0;

        // 🔹 Calculate late fee (ONLY for course_fee installments)
        $lateFee = 0;
        $approvedLateFee = 0;
        if ($paymentType === 'course_fee' && $request->installment_number) {
            $plan = \App\Models\StudentPaymentPlan::where('student_id', $student->student_id)
                ->where('course_id', $request->course_id)
                ->first();

            if ($plan) {
                $inst = \App\Models\PaymentInstallment::where('payment_plan_id', $plan->id)
                    ->where('installment_number', $request->installment_number)
                    ->first();

                if ($inst) {
                    $dueDate  = \Carbon\Carbon::parse($inst->due_date);
                    $daysLate = $dueDate->isPast() ? $dueDate->diffInDays(now()) : 0;
                    $baseAmt  = $inst->final_amount ?? $inst->amount ?? 0;

                    // 🔹 Calculate late fee and update installment
                    $calcFee = $this->calculateLateFee($baseAmt, $daysLate);
                    $inst->calculated_late_fee = $calcFee;
                    $inst->save();

                    $lateFee         = $calcFee;
                    $approvedLateFee = $inst->approved_late_fee ?? 0;
                }
            }
        }

        // 🔹 Total Fee = base fee + late fee - approved late fee
        $totalFee = $courseFee + $franchiseFee + $registrationFee + $lateFee - $approvedLateFee;

        // --- Prevent duplicate pending slips ---
$existingPayment = \App\Models\PaymentDetail::where('student_id', $student->student_id)
    ->where('course_registration_id', $registration->id)
    ->when($request->installment_number, fn($q) => $q->where('installment_number', $request->installment_number))
    ->when($request->due_date, fn($q) => $q->whereDate('due_date', $request->due_date))
    ->where('status', 'pending')
    ->first();

if ($existingPayment) {
    $paidSoFar = collect($existingPayment->partial_payments ?? [])->sum('amount');
    $remaining = max(($existingPayment->total_fee - $paidSoFar), 0);

    $existingPayment->update([
        'late_fee'          => $lateFee,
        'approved_late_fee' => $approvedLateFee,
        'total_fee'         => $totalFee,
        'remaining_amount'  => $remaining,  // ✅ recalc if needed
    ]);

    $slipData = $this->buildSlipArray(
        $existingPayment, $student, $course, $intake,
        $courseFee, $franchiseFee, $registrationFee,
        $lateFee, $approvedLateFee, $totalFee
    );
    $slipData['id'] = $existingPayment->id;
    $slipData['can_delete'] = true;

    session(['generated_slip_' . $existingPayment->transaction_id => $slipData]);

    return response()->json([
        'success'   => true,
        'slip_data' => $slipData,
        'message'   => 'Existing payment slip found. You can delete it if you want to regenerate.',
        'can_delete'=> true,
        'id'        => $existingPayment->id,
    ]);
}



        // --- Generate New Receipt Number ---
        $today      = date('Ymd');
        $latest     = \App\Models\PaymentDetail::where('transaction_id', 'like', "RCP{$today}%")
                        ->orderBy('transaction_id', 'desc')->first();
        $lastNumber = $latest ? (int) substr($latest->transaction_id, -4) : 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $receiptNo  = 'RCP' . $today . $nextNumber;


        // --- Create new Payment record ---
$payment = \App\Models\PaymentDetail::create([
    'student_id'             => $student->student_id,
    'course_registration_id' => $registration->id,
    'amount'                 => $amount,
    'payment_method'         => 'Cash',
    'transaction_id'         => $receiptNo,
    'status'                 => 'pending',
    'remarks'                => $request->remarks,
    'due_date'               => $request->due_date,

    // 👇 Fix: Only course_fee/franchise_fee should store installment_number
    'installment_number'     => in_array($paymentType, ['course_fee','franchise_fee']) 
                                    ? $request->installment_number 
                                    : null,

    // ✅ New fields
    'late_fee'          => $lateFee,
    'approved_late_fee' => $approvedLateFee,
    'total_fee'         => $totalFee,
    'remaining_amount'  => (float) $totalFee,
'partial_payments'  => json_encode([]), // ensures proper JSON

]);


        $slipData = $this->buildSlipArray(
            $payment, $student, $course, $intake,
            $courseFee, $franchiseFee, $registrationFee,
            $lateFee, $approvedLateFee, $totalFee
        );

        session(['generated_slip_' . $receiptNo => $slipData]);

        return response()->json([
            'success'   => true,
            'slip_data' => $slipData,
            'message'   => 'New payment slip generated.',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}


/**
 * Helper: Build slip array
 */
private function buildSlipArray(\App\Models\PaymentDetail $payment, $student, $course, $intake, $courseFee, $franchiseFee, $registrationFee, $lateFee, $approvedLateFee, $totalFee)
{
    // Normalize partial payments
    $partials = $payment->partial_payments ?? [];
    if (!is_array($partials)) {
        $partials = json_decode($partials, true) ?? [];
    }

    return [
        'receipt_no'        => $payment->transaction_id,
        'student_id'        => $student->student_id,
        'student_name'      => $student->full_name,
        'student_nic'       => $student->id_value,
        'course_name'       => $course->course_name ?? 'N/A',
        'course_code'       => $course->course_code ?? 'N/A',
        'intake'            => $intake->batch ?? 'N/A',
        'intake_id'         => $intake->intake_id ?? null,
        'payment_type'      => $payment->payment_type ?? '',
        'amount'            => (float) $payment->amount,
        'installment_number'=> $payment->installment_number,
        'due_date'          => $payment->due_date,
        'payment_date'      => $payment->payment_date,
        'payment_method'    => $payment->payment_method ?? 'Cash',
        'remarks'           => $payment->remarks,
        'status'            => $payment->status,
        'course_fee'        => $courseFee,
        'franchise_fee'     => $franchiseFee,
        'registration_fee'  => $registrationFee,
        'late_fee'          => (float) $payment->late_fee,
        'approved_late_fee' => (float) $payment->approved_late_fee,
        'total_fee'         => (float) $payment->total_fee,
        'remaining_amount'  => (float) $payment->remaining_amount,
        'partial_payments'  => $partials,   // ✅ Always an array
        'generated_at'      => now()->format('Y-m-d H:i:s'),
        'valid_until'       => now()->addDays(7)->format('Y-m-d'),
    ];
}


public function deletePaymentSlip($id)
{
    try {
        $payment = \App\Models\PaymentDetail::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending slips can be deleted.'
            ], 400);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment slip deleted successfully. You can now generate a new one.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting slip: ' . $e->getMessage()
        ], 500);
    }
}

public function recordPartialPayment(Request $request, $id)
{
    $request->validate([
        'amount'  => 'required|numeric|gt:0',
        'method'  => 'nullable|string',
        'date'    => 'nullable|date',
    ]);

    $payment = \App\Models\PaymentDetail::findOrFail($id);

    // Load history
    $history = $payment->partial_payments ?? [];

    // Append new payment
    $history[] = [
        'date'   => $request->date ?? now()->toDateString(),
        'amount' => (float) $request->amount,
        'method' => $request->method ?? 'Cash',
    ];

    // Update remaining
    $paidSoFar = collect($history)->sum('amount');
    $remaining = max(($payment->total_fee - $paidSoFar), 0);

    // Save
    $payment->partial_payments = $history;
    $payment->remaining_amount = $remaining;

    if ($remaining <= 0) {
        $payment->status = 'paid';
    }

    $payment->save();

    return back()->with('success', 'Partial payment recorded successfully.');
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

        // Try session first
        $slipData = session('generated_slip_' . $request->receipt_no);

        if (!$slipData) {
            // Fallback to DB with correct relations
            $payment = PaymentDetail::with(['student', 'registration.course', 'registration.intake'])
                ->where('transaction_id', $request->receipt_no)
                ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment slip not found or expired.'
                ], \Illuminate\Http\Response::HTTP_NOT_FOUND);
            }

            // Build in the exact shape the Blade expects
            $slipData = $this->buildSlipDataFromPaymentDetail($payment);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payment_slip', compact('slipData'))
              ->setPaper('A4', 'portrait')
              ->setOptions([
                  'isHtml5ParserEnabled' => true,
                  'isRemoteEnabled'      => true,
                  'defaultFont'          => 'Arial',
              ]);

        $filename = 'Payment_Slip_' . $slipData['receipt_no'] . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
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
 * Get payment records for Update Records tab.
 */
public function getPaymentRecords(Request $request)
{
    try {
        $request->validate([
            'student_nic' => 'required|string',
            'course_id'   => 'required|integer|exists:courses,course_id',
        ]);

        // 🔹 Find student by NIC
        $student = Student::where('id_value', $request->student_nic)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found with the provided NIC.'
            ], Response::HTTP_NOT_FOUND);
        }

        // 🔹 Verify registration
        $registration = CourseRegistration::where('student_id', $student->student_id)
            ->where('course_id', $request->course_id)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Student is not registered for this course.'
            ], Response::HTTP_NOT_FOUND);
        }

        // 🔹 Fetch payment details
        $records = PaymentDetail::where('student_id', $student->student_id)
            ->where('course_registration_id', $registration->id)
            ->get()
            ->map(function ($payment) use ($student) {
    return [
        'payment_id'         => $payment->id,
        'student_id'         => $student->student_id,
        'student_name'       => $student->full_name,
        'payment_type'       => $payment->payment_type ?? 'course_fee',
        'installment_number' => $payment->installment_number,
        'amount'             => (float) $payment->amount,
        'late_fee'           => (float) ($payment->late_fee ?? 0),
        'approved_late_fee'  => (float) ($payment->approved_late_fee ?? 0),
        'total_fee'          => (float) ($payment->total_fee ?? 0),
        'remaining_amount'   => (float) ($payment->remaining_amount ?? 0),

        // ✅ always return array instead of raw JSON string
        'partial_payments' => $payment->partial_payments 
                            ? (is_array($payment->partial_payments) 
                                ? $payment->partial_payments 
                                : json_decode($payment->partial_payments, true)) 
                            : [],


        'payment_method'     => $payment->payment_method,
        'payment_date'       => $payment->payment_date
            ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d')
            : null,
        'receipt_no'         => $payment->transaction_id,
        'status'             => $payment->status,
        'remarks'            => $payment->remarks,
    ];
});



        return response()->json([
            'success' => true,
            'records' => $records
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
 * Update payment record (Update Records tab).
 */
public function updatePaymentRecord(Request $request)
{
    try {
        $request->validate([
            'id'                => 'required|exists:payment_details,id', // ✅ correct PK
            'payment_type'      => 'required|string',
            'amount'            => 'required|numeric|min:0',
            'late_fee'          => 'nullable|numeric|min:0',
            'approved_late_fee' => 'nullable|numeric|min:0',
            'total_fee'         => 'nullable|numeric|min:0',
            'remaining_amount'  => 'nullable|numeric|min:0',
            'payment_method'    => 'required|string',
            'payment_date'      => 'required|date',
            'receipt_no'        => 'required|string',
            'status'            => 'required|string',
            'remarks'           => 'nullable|string',
        ]);

        $payment = PaymentDetail::findOrFail($request->id);

        $payment->update([
            'payment_type'      => $request->payment_type,
            'amount'            => $request->amount,
            'late_fee'          => $request->late_fee ?? 0,
            'approved_late_fee' => $request->approved_late_fee ?? 0,
            'total_fee'         => $request->total_fee ?? ($request->amount + ($request->late_fee ?? 0) - ($request->approved_late_fee ?? 0)),
            'remaining_amount'  => $request->remaining_amount ?? $payment->remaining_amount,
            'payment_method'    => $request->payment_method,
            'payment_date'      => $request->payment_date,
            'transaction_id'    => $request->receipt_no, // ✅ correct column
            'status'            => $request->status,
            'remarks'           => $request->remarks,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment record updated successfully.'
        ], Response::HTTP_OK);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
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

public function makePayment(Request $request)
{
    try {
        $request->validate([
            'payment_id'     => 'required|exists:payment_details,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'remarks'        => 'nullable|string',
        ]);

        $payment = PaymentDetail::findOrFail($request->payment_id);

        // Decode existing partial payments (JSON field)
        $partials = $payment->partial_payments ?? [];
        if (!is_array($partials)) {
            $partials = json_decode($partials, true) ?? [];
        }

        // Add new partial payment entry
        $partials[] = [
            'amount'  => (float)$request->amount,
            'method'  => $request->payment_method,
            'date'    => $request->payment_date,
            'remarks' => $request->remarks,
        ];

        // Calculate totals
        $paidSoFar  = collect($partials)->sum('amount');
        $remaining  = max(($payment->total_fee - $paidSoFar), 0);

       // 🔹 Update main payment record
// 🔹 Update main payment record
$payment->update([
    'partial_payments' => $partials,
    'remaining_amount' => $remaining,
    'payment_method'   => $request->payment_method,
    'payment_date'     => $request->payment_date,
    'remarks'          => $request->remarks,
    'status'           => $remaining <= 0 ? 'paid' : 'pending',
]);

// 🔹 If fully paid, also mark installment as paid
if ($remaining <= 0 && $payment->payment_type === 'course_fee' && $payment->installment_number) {
    // 1. Find student payment plan (via student_id + course_id)
    $registration = $payment->registration; // relationship already defined
    if ($registration) {
        $plan = \App\Models\StudentPaymentPlan::where('student_id', $payment->student_id)
            ->where('course_id', $registration->course_id)
            ->first();

        if ($plan) {
            // 2. Find and update the installment
            $inst = \App\Models\PaymentInstallment::where('payment_plan_id', $plan->id)
                ->where('installment_number', $payment->installment_number)
                ->first();

            if ($inst) {
                $inst->status    = 'paid';
                $inst->paid_date = now();
                $inst->save();
            }
        }
    }
}



        return response()->json([
            'success' => true,
            'message' => $remaining <= 0 
                ? 'Payment completed. Status updated to PAID.' 
                : 'Partial payment recorded successfully.',
            'remaining_amount' => $remaining,
            'status' => $payment->status,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
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
  /**
 * Build the array the Blade view expects from a PaymentDetail.
 * $overrides lets you inject values we only know at request-time (e.g., FX rate).
 */
private function buildSlipDataFromPaymentDetail(\App\Models\PaymentDetail $payment, array $overrides = []): array
{
    $student       = $payment->student;
    $registration  = $payment->registration;              // CourseRegistration
    $course        = optional($registration)->course;
    $intake        = optional($registration)->intake;

    // what type was this? if not stored, guess "course_fee" and let UI text override
    $type = $overrides['payment_type'] ?? ($payment->payment_type ?? 'course_fee');

    // FX (for franchise): if a rate is provided via overrides, compute LKR too
    $currencyFrom = $overrides['currency_from'] ?? null;
    $convRate     = isset($overrides['conversion_rate']) ? (float)$overrides['conversion_rate'] : null;
    $lkrAmount    = ($type === 'franchise_fee' && $convRate) ? round(((float)$payment->amount) * $convRate, 2) : null;

    // currency to display for franchise (fallback to intake’s currency fields if you have them)
    $fxCurrency   = $overrides['franchise_fee_currency']
        ?? ($intake->franchise_payment_currency
            ?? $intake->international_currency
            ?? 'USD');

    // simple per-type breakdown for the “Payment Breakdown” table
    $courseFee       = $type === 'course_fee'       ? (float)$payment->amount : 0.0;
    $franchiseFee    = $type === 'franchise_fee'    ? (float)$payment->amount : 0.0;
    $registrationFee = $type === 'registration_fee' ? (float)($intake->registration_fee ?? 0) : 0.0;

    return [
        'receipt_no'             => $payment->transaction_id,
        'student_id'             => $payment->student_id,
        'student_name'           => optional($student)->full_name ?? 'N/A',
        'student_nic'            => optional($student)->id_value ?? 'N/A',

        'course_name'            => optional($course)->course_name ?? 'N/A',
        'course_code'            => optional($course)->course_code ?? 'N/A',
        'intake'                 => optional($intake)->batch ?? 'N/A',
        'intake_id'              => optional($intake)->intake_id,

        'payment_type'           => $type,
        'payment_type_display'   => $this->getPaymentTypeDisplay($type),

        // amount is ALWAYS numeric; Blade formats it
        'amount'                 => (float)$payment->amount,

        // franchise extras (null for non-franchise)
        'currency_from'          => $currencyFrom,
        'conversion_rate'        => $convRate,
        'lkr_amount'             => $lkrAmount,
        'franchise_fee_currency' => $fxCurrency,

        'installment_number'     => $payment->installment_number,
        'due_date'               => optional($payment->due_date)->format('Y-m-d'),
        'payment_date'           => optional($payment->updated_at)->format('Y-m-d'),
        'payment_method'         => $payment->payment_method,
        'remarks'                => $payment->remarks,
        'status'                 => $payment->status,

        'location'               => $registration->location ?? 'N/A',
        'registration_date'      => optional($registration->registration_date)->format('Y-m-d'),

        // breakdown
        'course_fee'             => $courseFee,
        'franchise_fee'          => $franchiseFee,
        'registration_fee'       => $registrationFee,

        'generated_at'           => optional($payment->created_at)->format('Y-m-d H:i:s'),
        'valid_until'            => optional($payment->created_at)->copy()->addDays(7)->format('Y-m-d'),
    ];
}
  
    
} 