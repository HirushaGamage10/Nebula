<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\PaymentPlan;
use App\Models\Intake;

class PaymentPlanController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        
        return view('payment_plan', compact('courses'));
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
                'installments' => 'nullable', // Will be handled as JSON
            ]);

            $installments = $request->input('installments');
            if (is_string($installments)) {
                $installments = json_decode($installments, true);
            }

            // Validate installment amounts if installment plan is enabled
            if ($request->input('franchisePayment') === 'yes' && $installments) {
                $this->validateInstallmentAmounts($installments, $validated['localFee'], $validated['internationalFee']);
            }

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
                'installments' => $installments ? json_encode($installments) : null,
            ]);

            return redirect()->back()->with('success', 'Payment plan created successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while creating the payment plan. Please try again.')
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
} 