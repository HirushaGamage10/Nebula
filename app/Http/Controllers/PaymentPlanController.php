<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\PaymentPlan;
use App\Models\Intake;
use App\Models\CourseRegistration;
use App\Models\StudentPaymentPlan;
use App\Models\PaymentInstallment;
use App\Models\PaymentPlanDiscount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PaymentPlanController extends Controller
{
    // NEW: list all plans with filters/pagination
    public function index(Request $request)
{
    $locations = ['Welisara','Moratuwa','Peradeniya'];

    $query = PaymentPlan::query()
        ->with(['course','intake'])
        ->when($request->filled('location'), fn($q) => $q->where('location', $request->location))
        ->when($request->filled('course_id'), fn($q) => $q->where('course_id', $request->course_id))
        ->when($request->filled('intake_id'), fn($q) => $q->where('intake_id', $request->intake_id))
        ->orderByDesc('id');

    $plans   = $query->paginate(10)->withQueryString();
    $courses = Course::orderBy('course_name')->get(['course_id','course_name']);

    // 🧩 FIXED — use course_id for intake filter (not course_name)
    $intakes = collect();
    if ($request->filled('course_id') && $request->filled('location')) {
        $intakes = Intake::where('course_id', $request->course_id)
            ->where('location', $request->location)
            ->orderBy('batch')
            ->get(['intake_id', 'batch']);
    }

    return view('payments.payment_plan_index', compact('plans','locations','courses','intakes'));
}

    public function getCoursesByLocation(Request $request)
{
    $request->validate([
        'location' => 'required|string',
    ]);

    $courses = Course::where('location', $request->location)
        ->orderBy('course_name')
        ->get(['course_id','course_name']);

    return response()->json([
        'success' => true,
        'data' => $courses
    ]);
}


    // Your original page now lives here, unchanged logic:
    public function create(Request $request)
{
    $locations = ['Welisara','Moratuwa','Peradeniya'];
    $selectedLocation = $request->query('location');

    // Filter courses based on selected location
    $courses = collect();
    if ($selectedLocation) {
        $courses = Course::where('location', $selectedLocation)
            ->orderBy('course_name')
    ->get(['course_id', 'course_name', 'course_type']);
    }

    return view('payments.payment_plan', compact('courses', 'locations', 'selectedLocation'));
}



    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'location' => 'required|string',
            'course' => 'required|exists:courses,course_id',
            'intake' => 'required|exists:intakes,intake_id',
            'registrationFee' => 'required|numeric|min:0',
            'localFee' => 'required|numeric|min:0',
            'internationalFee' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'ssclTax' => 'required|numeric|min:0',
            'bankCharges' => 'nullable|numeric|min:0',
            'applyDiscount' => 'required|string',
            'fullPaymentDiscount' => 'nullable|numeric|min:0',
            'installmentPlan' => 'nullable|string',
            'installments' => 'nullable',
        ]);

        $exists = PaymentPlan::where('location', $validated['location'])
            ->where('course_id', $validated['course'])
            ->where('intake_id', $validated['intake'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A payment plan already exists for this Location, Course, and Intake.');
        }

        $installments = $request->input('installments');
        if (is_string($installments)) {
            $installments = json_decode($installments, true);
        }

        if ($request->input('franchisePayment') === 'yes' && $installments) {
            $this->validateInstallmentAmounts($installments, $validated['localFee'], $validated['internationalFee']);
        }

        $syncSummary = DB::transaction(function () use ($validated, $request, $installments) {
            $plan = PaymentPlan::create([
                'location' => $validated['location'],
                'course_id' => $validated['course'],
                'intake_id' => $validated['intake'],
                'registration_fee' => $validated['registrationFee'],
                'local_fee' => $validated['localFee'],
                'international_fee' => $validated['internationalFee'],
                'international_currency' => $validated['currency'],
                'sscl_tax' => $validated['ssclTax'],
                'bank_charges' => $validated['bankCharges'] ?? null,
                'apply_discount' => $validated['applyDiscount'] === 'yes',
                'discount' => $validated['fullPaymentDiscount'] ?? null,
                'installment_plan' => $request->input('franchisePayment') === 'yes',
                'installments' => $installments ?: null,
            ]);

            return $this->syncStudentsForIntakePlan($plan);
        });

        $syncedPlans = ($syncSummary['plans_created'] ?? 0) + ($syncSummary['plans_updated'] ?? 0);

        return redirect()->back()->with(
            'success',
            "Payment plan created successfully! Synced {$syncedPlans} student payment plan(s)."
        );

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        Log::error('PaymentPlan store failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->with('error', 'An error occurred while creating the payment plan. Please try again.')
            ->withInput();
    }
}

    public function edit($id)
    {
        $plan = PaymentPlan::with('course','intake')->findOrFail($id);
        $courses = Course::orderBy('course_name')->get(['course_id','course_name']);

        $intakes = Intake::where('course_name', $plan->course->course_name ?? '')
            ->orderBy('batch')
            ->get(['intake_id','batch']);

        // decode installments JSON (safe)
        $installments = is_array($plan->installments) 
            ? $plan->installments 
            : (json_decode($plan->installments, true) ?? []);

        return view('payments.payment_plan_edit', compact('plan','courses','intakes','installments'));
    }


public function update(Request $request, $id)
{
    try {
        $plan = PaymentPlan::findOrFail($id);

        $request->validate([
            'location'               => 'required|string',
            'course_id'              => 'required|integer',
            'intake_id'              => 'nullable|integer',
            'registration_fee'       => 'required|numeric|min:0',
            'local_fee'              => 'required|numeric|min:0',
            'international_fee'      => 'required|numeric|min:0',
            'international_currency' => 'required|string',
            'sscl_tax'               => 'nullable|numeric|min:0',
            'bank_charges'           => 'nullable|numeric|min:0',
            'apply_discount'         => 'nullable|boolean',
            'discount'               => 'nullable|numeric|min:0',
            'installment_plan'       => 'nullable|boolean',
            'installments'           => 'nullable|array',
        ]);

        $syncSummary = DB::transaction(function () use ($request, $plan) {
            $plan->location               = $request->location;
            $plan->course_id              = $request->course_id;
            $plan->intake_id              = $request->intake_id;
            $plan->registration_fee       = $request->registration_fee;
            $plan->local_fee              = $request->local_fee;
            $plan->international_fee      = $request->international_fee;
            $plan->international_currency = $request->international_currency;
            $plan->sscl_tax               = $request->sscl_tax;
            $plan->bank_charges           = $request->bank_charges;
            $plan->apply_discount         = $request->apply_discount ? 1 : 0;
            $plan->discount               = $request->discount;
            $plan->installment_plan       = $request->installment_plan ? 1 : 0;

            $installments = [];
            if ($request->has('installments')) {
                foreach ($request->installments as $i => $inst) {
                    $installments[] = [
                        'installment_number'   => $i + 1,
                        'due_date'             => $inst['due_date'] ?? null,
                        'local_amount'         => (float) ($inst['local_amount'] ?? 0),
                        'international_amount' => (float) ($inst['international_amount'] ?? 0),
                        'apply_tax'            => isset($inst['apply_tax']),
                    ];
                }
            }

            $plan->installments = $installments;
            $plan->save();

            return $this->syncStudentsForIntakePlan($plan);
        });

        $syncedPlans = ($syncSummary['plans_created'] ?? 0) + ($syncSummary['plans_updated'] ?? 0);

        return redirect()
            ->route('payment.plan.index')
            ->with('success', "Payment plan updated successfully. Synced {$syncedPlans} student payment plan(s).");

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()
            ->back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        Log::error('PaymentPlan update failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with('error', 'An unexpected error occurred while updating the payment plan.')
            ->withInput();
    }
}



    /**
     * Create or refresh student payment plans for the students currently registered in the intake.
     */
    private function syncStudentsForIntakePlan(PaymentPlan $templatePlan): array
    {
        $registrations = CourseRegistration::query()
            ->where('course_id', $templatePlan->course_id)
            ->where('intake_id', $templatePlan->intake_id)
            ->when(!empty($templatePlan->location), function ($query) use ($templatePlan) {
                $query->where('location', $templatePlan->location);
            })
            ->whereIn('status', ['Registered', 'registered'])
            ->get(['id', 'student_id', 'course_id']);

        $summary = [
            'registrations_found' => $registrations->count(),
            'plans_created' => 0,
            'plans_updated' => 0,
            'installments_synced' => 0,
        ];

        foreach ($registrations as $registration) {
            $studentPlan = StudentPaymentPlan::where('student_id', $registration->student_id)
                ->where('course_id', $registration->course_id)
                ->where('status', '!=', 'archived')
                ->orderByDesc('id')
                ->first();

            $isNew = !$studentPlan;

            if (!$studentPlan) {
                $studentPlan = new StudentPaymentPlan([
                    'student_id' => $registration->student_id,
                    'course_id' => $registration->course_id,
                ]);
            }

            $computed = $this->buildStudentPlanFromIntake($templatePlan, $studentPlan);

            $studentPlan->fill($computed['attributes']);
            $studentPlan->status = $studentPlan->status ?: 'active';
            $studentPlan->save();

            $summary[$isNew ? 'plans_created' : 'plans_updated']++;
            $summary['installments_synced'] += $this->syncPlanInstallments($studentPlan, $computed['installments']);

            if (Schema::hasColumn('course_registration', 'payment_plan_id')) {
                DB::table('course_registration')
                    ->where('id', $registration->id)
                    ->update([
                        'payment_plan_id' => $studentPlan->id,
                        'updated_at' => now(),
                    ]);
            }
        }

        Log::info('Synced intake payment plan to student payment plans', [
            'payment_plan_id' => $templatePlan->id,
            'course_id' => $templatePlan->course_id,
            'intake_id' => $templatePlan->intake_id,
            'summary' => $summary,
        ]);

        return $summary;
    }

    private function buildStudentPlanFromIntake(PaymentPlan $templatePlan, ?StudentPaymentPlan $existingPlan = null): array
    {
        $registrationFee = round((float) ($templatePlan->registration_fee ?? 0), 2);
        $localFee = round((float) ($templatePlan->local_fee ?? 0), 2);
        $totalAmount = round($registrationFee + $localFee, 2);

        $discountSummary = $this->getStudentDiscountSummary($templatePlan, $existingPlan, $registrationFee, $totalAmount);
        $rows = $this->getTemplateInstallmentRows($templatePlan, $localFee);

        $lastIndex = count($rows) - 1;
        $discountedBases = array_map(function ($row) {
            return round((float) ($row['base_amount'] ?? 0), 2);
        }, $rows);

        $normalDiscountApplied = 0.0;
        $discountApplied = array_fill(0, count($discountedBases), 0.0);
        $remainingNormalDiscount = $discountSummary['normal_discount_total'];

        for ($i = $lastIndex; $i >= 0 && $remainingNormalDiscount > 0; $i--) {
            $available = $discountedBases[$i];
            $deduct = min($available, $remainingNormalDiscount);
            $discountedBases[$i] = round($available - $deduct, 2);
            $discountApplied[$i] = round($deduct, 2);
            $remainingNormalDiscount -= $deduct;
            $normalDiscountApplied += $deduct;
        }

        $registrationDiscountApplied = 0.0;
        if (!empty($discountedBases)) {
            $registrationDiscountApplied = min($discountSummary['registration_discount_excess'], $discountedBases[0]);
            $discountedBases[0] = round($discountedBases[0] - $registrationDiscountApplied, 2);
        }

        $sumAfterDiscounts = round(array_sum($discountedBases), 2);
        $sltLoanApplied = ($existingPlan?->slt_loan_applied ?? 'no') === 'yes' ? 'yes' : 'no';
        $sltLoanAmount = $sltLoanApplied === 'yes' ? round((float) ($existingPlan?->slt_loan_amount ?? 0), 2) : 0.0;
        $sltLoanAmount = min($sltLoanAmount, $sumAfterDiscounts);
        $targetFinalTotal = round($sumAfterDiscounts - $sltLoanAmount, 2);

        $installments = [];
        $runningTotal = 0.0;

        foreach ($rows as $index => $row) {
            $discountedBase = $discountedBases[$index] ?? 0.0;
            $isLast = $index === $lastIndex;

            if ($isLast) {
                $finalAmount = round($targetFinalTotal - $runningTotal, 2);
            } else {
                $finalAmount = $sumAfterDiscounts > 0
                    ? round(($discountedBase / $sumAfterDiscounts) * $targetFinalTotal, 2)
                    : 0.0;
                $runningTotal += $finalAmount;
            }

            $loanShare = round($discountedBase - $finalAmount, 2);

            $installments[] = [
                'installment_number' => (int) ($row['installment_number'] ?? ($index + 1)),
                'due_date' => $row['due_date'] ?? null,
                'status' => $row['status'] ?? 'pending',
                'base_amount' => round((float) ($row['base_amount'] ?? 0), 2),
                'amount' => max(0, $finalAmount),
                'discount_amount' => $isLast ? round($normalDiscountApplied, 2) : 0.0,
                'discount_note' => $isLast && $normalDiscountApplied > 0 ? 'Normal Discounts Applied' : null,
                'slt_loan_amount' => max(0, $loanShare),
                'registration_fee_discount_applied' => $index === 0 ? round($registrationDiscountApplied, 2) : 0.0,
                'registration_fee_discount_note' => $index === 0 && $registrationDiscountApplied > 0 ? 'Reg. Fee Excess' : null,
                'final_amount' => max(0, $finalAmount),
            ];
        }

        return [
            'attributes' => [
                'payment_plan_type' => $templatePlan->installment_plan ? 'installments' : 'full',
                'slt_loan_applied' => $sltLoanApplied,
                'slt_loan_amount' => $sltLoanAmount,
                'total_amount' => $totalAmount,
                'final_amount' => round(array_sum(array_column($installments, 'final_amount')), 2),
                'remaining_registration_discount' => $discountSummary['remaining_registration_discount'],
                'status' => $existingPlan?->status ?? 'active',
            ],
            'installments' => $installments,
        ];
    }

    private function getStudentDiscountSummary(PaymentPlan $templatePlan, ?StudentPaymentPlan $studentPlan, float $registrationFee, float $totalFeeForDiscount): array
    {
        $normalPercentage = $templatePlan->apply_discount ? (float) ($templatePlan->discount ?? 0) : 0.0;
        $normalFixed = 0.0;
        $registrationDiscountAmount = 0.0;

        if ($studentPlan && $studentPlan->exists) {
            $discountRows = PaymentPlanDiscount::with('discount')
                ->where('payment_plan_id', $studentPlan->id)
                ->get();

            foreach ($discountRows as $row) {
                $category = $row->discount->discount_category ?? 'local_course_fee';
                $type = strtolower((string) ($row->discount_type ?? ''));
                $value = (float) ($row->discount_value ?? 0);

                if ($category === 'registration_fee') {
                    $registrationDiscountAmount += $type === 'percentage'
                        ? ($registrationFee * ($value / 100))
                        : $value;
                } else {
                    if ($type === 'percentage') {
                        $normalPercentage += $value;
                    } elseif ($type === 'amount') {
                        $normalFixed += $value;
                    }
                }
            }
        }

        $normalDiscountTotal = (($totalFeeForDiscount * $normalPercentage) / 100) + $normalFixed;
        $registrationDiscountExcess = max(0, $registrationDiscountAmount - $registrationFee);
        $existingRemaining = $studentPlan && $studentPlan->exists
            ? (float) ($studentPlan->remaining_registration_discount ?? 0)
            : 0.0;
        $remainingRegistrationDiscount = $existingRemaining > 0
            ? min($existingRemaining, $registrationDiscountExcess > 0 ? round($registrationDiscountExcess, 2) : $existingRemaining)
            : round($registrationDiscountExcess, 2);

        return [
            'normal_discount_total' => round($normalDiscountTotal, 2),
            'registration_discount_excess' => round($registrationDiscountExcess, 2),
            'remaining_registration_discount' => $remainingRegistrationDiscount,
        ];
    }

    private function getTemplateInstallmentRows(PaymentPlan $templatePlan, float $localFee): array
    {
        $rows = [];
        $templateRows = is_array($templatePlan->installments)
            ? $templatePlan->installments
            : (json_decode($templatePlan->installments ?? '[]', true) ?: []);

        foreach ($templateRows as $index => $row) {
            $baseAmount = round((float) ($row['local_amount'] ?? $row['amount'] ?? 0), 2);

            if ($baseAmount <= 0) {
                continue;
            }

            $rows[] = [
                'installment_number' => (int) ($row['installment_number'] ?? ($index + 1)),
                'due_date' => $row['due_date'] ?? null,
                'base_amount' => $baseAmount,
                'status' => 'pending',
            ];
        }

        if (empty($rows)) {
            $defaultDueDate = $templatePlan->intake && $templatePlan->intake->start_date
                ? date('Y-m-d', strtotime($templatePlan->intake->start_date))
                : now()->addDays(7)->toDateString();

            $rows[] = [
                'installment_number' => 1,
                'due_date' => $defaultDueDate,
                'base_amount' => $localFee,
                'status' => 'pending',
            ];
        }

        return $rows;
    }

    private function syncPlanInstallments(StudentPaymentPlan $studentPlan, array $installments): int
    {
        $seenNumbers = [];

        foreach ($installments as $installment) {
            $seenNumbers[] = (int) $installment['installment_number'];

            $existing = PaymentInstallment::where('payment_plan_id', $studentPlan->id)
                ->where('installment_number', $installment['installment_number'])
                ->first();

            PaymentInstallment::updateOrCreate(
                [
                    'payment_plan_id' => $studentPlan->id,
                    'installment_number' => $installment['installment_number'],
                ],
                [
                    'due_date' => $installment['due_date'],
                    'amount' => $installment['amount'],
                    'base_amount' => $installment['base_amount'],
                    'discount_amount' => $installment['discount_amount'],
                    'discount_note' => $installment['discount_note'],
                    'slt_loan_amount' => $installment['slt_loan_amount'],
                    'registration_fee_discount_applied' => $installment['registration_fee_discount_applied'],
                    'registration_fee_discount_note' => $installment['registration_fee_discount_note'],
                    'final_amount' => $installment['final_amount'],
                    'installment_type' => 'local',
                    'status' => $existing?->status ?? $installment['status'],
                    'paid_date' => $existing?->paid_date,
                    'approved_late_fee' => $existing?->approved_late_fee ?? 0,
                    'calculated_late_fee' => $existing?->calculated_late_fee ?? 0,
                ]
            );
        }

        if (!empty($seenNumbers)) {
            PaymentInstallment::where('payment_plan_id', $studentPlan->id)
                ->whereNotIn('installment_number', $seenNumbers)
                ->where('status', '!=', 'paid')
                ->delete();
        }

        return count($seenNumbers);
    }

    /**
     * Validate that the sum of installment amounts matches the course fees
     */
    private function validateInstallmentAmounts($installments, $localFee, $internationalFee)
    {
        $totalLocalAmount = 0;
        $totalInternationalAmount = 0;

        foreach ($installments as $installment) {
            $totalLocalAmount += floatval($installment['local_amount'] ?? 0);
            $totalInternationalAmount += floatval($installment['international_amount'] ?? 0);
        }

        $errors = [];

        // Check if local amounts sum equals local course fee
        if (abs($totalLocalAmount - $localFee) > 0.01) { // Using small tolerance for floating point comparison
            $errors[] = "The sum of local installment amounts (Rs. " . number_format($totalLocalAmount, 2) . ") must equal the local course fee (Rs. " . number_format($localFee, 2) . "). Difference: Rs. " . number_format(abs($totalLocalAmount - $localFee), 2);
        }

        // Check if international amounts sum equals franchise payment amount
        if (abs($totalInternationalAmount - $internationalFee) > 0.01) { // Using small tolerance for floating point comparison
            $errors[] = "The sum of international installment amounts (" . number_format($totalInternationalAmount, 2) . ") must equal the franchise payment amount (" . number_format($internationalFee, 2) . "). Difference: " . number_format(abs($totalInternationalAmount - $internationalFee), 2);
        }

        if (!empty($errors)) {
            // Create a custom validation exception with detailed messages
            $validator = validator([], []);
            $validator->errors()->add('installments', $errors);
            
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * API endpoint to fetch intake fee details for autofill in payment plan page.
     */
    public function getIntakeFees(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'location' => 'required|string',
            'intake_id' => 'required|integer',
        ]);

        $course = \App\Models\Course::find($request->course_id);
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }

        $intake = \App\Models\Intake::forCourse($course, $request->location)
            ->where('intake_id', $request->intake_id)
            ->first();

        if (!$intake) {
            return response()->json(['success' => false, 'message' => 'No intake found for this course/location.'], 404);
        }

        return response()->json([
            'success' => true,
            'registration_fee' => $intake->registration_fee,
            'course_fee' => $intake->course_fee,
            'franchise_payment' => $intake->franchise_payment,
            'franchise_payment_currency' => $intake->franchise_payment_currency ?? 'LKR',
            'sscl_tax' => $intake->sscl_tax ?? 0.00,
            'bank_charges' => $intake->bank_charges ?? 0.00,
        ]);
    }
    public function getIntakesByCourse(Request $request)
{
    $request->validate([
        'course_id' => 'required|integer',
        'location'  => 'required|string',
    ]);

    $course = Course::find($request->course_id);
    if (!$course) {
        return response()->json(['success' => false, 'data' => []]);
    }

    $intakes = Intake::forCourse($course, $request->location)
        ->orderBy('batch')
        ->get(['intake_id','batch']);

    return response()->json([
        'success' => true,
        'data' => $intakes
    ]);
}

} 