<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\PaymentPlan;
use App\Models\Intake;

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

    return view('payment_plan_index', compact('plans','locations','courses','intakes'));
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
    public function create()
    {
        $courses = Course::all();
        return view('payment_plan', compact('courses')); // your create form view
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

        // ✅ Step 1: Prevent duplicate Payment Plan
        $exists = PaymentPlan::where('location', $validated['location'])
            ->where('course_id', $validated['course'])
            ->where('intake_id', $validated['intake'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A payment plan already exists for this Location, Course, and Intake.');
        }

        // ✅ Step 2: Handle installments logic as before
        $installments = $request->input('installments');
        if (is_string($installments)) {
            $installments = json_decode($installments, true);
        }

        if ($request->input('franchisePayment') === 'yes' && $installments) {
            $this->validateInstallmentAmounts($installments, $validated['localFee'], $validated['internationalFee']);
        }

        // ✅ Step 3: Save payment plan
        PaymentPlan::create([
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
            'installments' => $installments ? json_encode($installments) : null,
        ]);

        return redirect()->back()->with('success', 'Payment plan created successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
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

        return view('payment_plan_edit', compact('plan','courses','intakes','installments'));
    }


public function update(Request $request, $id)
{
    try {
        $plan = PaymentPlan::findOrFail($id);

        // Validate input
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

        // Assign values
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

        // Build installments
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

        // Let Laravel cast array → JSON
        $plan->installments = $installments;

        // Save to DB
        $plan->save();

        return redirect()
            ->route('payment.plan.index')
            ->with('success', 'Payment plan updated successfully.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()
            ->back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        \Log::error('PaymentPlan update failed: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with('error', 'An unexpected error occurred while updating the payment plan.')
            ->withInput();
    }
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

        $intake = \App\Models\Intake::where('intake_id', $request->intake_id)
            ->where('course_name', $course->course_name)
            ->where('location', $request->location)
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

    $courseName = Course::where('course_id', $request->course_id)->value('course_name');

$intakes = Intake::whereRaw('LOWER(TRIM(course_name)) = ?', [strtolower(trim($courseName))])
    ->where('location', $request->location)
    ->orderBy('batch')
    ->get(['intake_id','batch']);


    return response()->json([
        'success' => true,
        'data' => $intakes
    ]);
}

} 