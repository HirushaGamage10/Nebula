<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intake;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IntakeCreationController extends Controller
{
    /**
     * Show the form for creating a new intake.
     */
    public function create()
    {
        // Use both ID and name 
        $courses = Course::orderBy('course_name', 'asc')->get(['course_id', 'course_name']);
        $intakes = Intake::with(['registrations', 'course'])->orderBy('start_date', 'desc')->get();

        return view('intake_creation', compact('courses', 'intakes'));
    }


    /**
     * Store a newly created intake in storage.
     */
    public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'location' => ['required', Rule::in(['Welisara', 'Moratuwa', 'Peradeniya'])],
            'course_id' => 'required|exists:courses,course_id',
            'batch' => 'required|string|max:255',
            'batch_size' => 'required|integer|min:1',
            'intake_mode' => ['required', Rule::in(['Physical', 'Online', 'Hybrid'])],
            'intake_type' => ['required', Rule::in(['Fulltime', 'Parttime'])],
            'registration_fee' => 'required|numeric|min:0',
            'franchise_payment' => 'required|numeric|min:0',
            'franchise_payment_currency' => 'required|string|in:LKR,USD,GBP,EUR',
            'course_fee' => 'required|numeric|min:0',
            'sscl_tax' => 'required|numeric|min:0|max:100',
            'bank_charges' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'enrollment_end_date' => 'nullable|date|before_or_equal:start_date',
            'course_registration_id_pattern' => 'required|string|regex:/^.*\d+$/',
        ]);

        // 🧩 Automatically fetch the course name and store both
        $course = \App\Models\Course::find($request->course_id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }

        //  If enrollment_end_date is empty, set it equal to start_date
        if (empty($validatedData['enrollment_end_date'])) {
            $validatedData['enrollment_end_date'] = $validatedData['start_date'];
        }
        
        $validatedData['course_name'] = $course->course_name;

        // 🧩 Create the intake with both course_id and course_name
        $intake = Intake::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Intake created successfully.',
            'intake' => $intake
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Error storing intake data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while creating the intake.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Show the form for editing the specified intake.
     */
    public function edit($id)
    {
        try {
            Log::info('Fetching intake for edit with ID: ' . $id);
            $intake = Intake::with('course')->findOrFail($id);

            return response()->json([
                'success' => true,
                'intake'  => $intake
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching intake for edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Intake not found.'
            ], 404);
        }
    }

    /**
     * Update the specified intake in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Updating intake with ID: ' . $id);
            Log::info('Request data: ', $request->all());
            
            $intake = Intake::findOrFail($id);
            
            $validatedData = $request->validate([
                'location' => ['required', Rule::in(['Welisara', 'Moratuwa', 'Peradeniya'])],
                'course_id' => 'required|exists:courses,course_id',
                'batch' => 'required|string|max:255',
                'batch_size' => 'required|integer|min:1',
                'intake_mode' => ['required', Rule::in(['Physical', 'Online', 'Hybrid'])],
                'intake_type' => ['required', Rule::in(['Fulltime', 'Parttime'])],
                'registration_fee' => 'required|numeric|min:0',
                'franchise_payment' => 'required|numeric|min:0',
                'franchise_payment_currency' => 'required|string|in:LKR,USD,GBP,EUR',
                'course_fee' => 'required|numeric|min:0',
                'sscl_tax' => 'required|numeric|min:0|max:100',
                'bank_charges' => 'nullable|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'enrollment_end_date' => 'nullable|date|before_or_equal:start_date',
                'course_registration_id_pattern' => 'required|string|regex:/^.*\d+$/',
            ], [
                'location.required' => 'Location is required.',
                'location.in' => 'Please select a valid location.',
                'course_id.required' => 'Course selection is required.',
                'course_id.exists' => 'Selected course does not exist.',
                'batch.required' => 'Batch name is required.',
                'batch_size.required' => 'Batch size is required.',
                'batch_size.integer' => 'Batch size must be a number.',
                'batch_size.min' => 'Batch size must be at least 1.',
                'intake_mode.required' => 'Intake mode is required.',
                'intake_mode.in' => 'Please select a valid intake mode.',
                'intake_type.required' => 'Intake type is required.',
                'intake_type.in' => 'Please select a valid intake type.',
                'registration_fee.required' => 'Registration fee is required.',
                'registration_fee.numeric' => 'Registration fee must be a number.',
                'registration_fee.min' => 'Registration fee must be at least 0.',
                'franchise_payment.required' => 'Franchise payment is required.',
                'franchise_payment.numeric' => 'Franchise payment must be a number.',
                'franchise_payment.min' => 'Franchise payment must be at least 0.',
                'franchise_payment_currency.required' => 'Franchise payment currency is required.',
                'franchise_payment_currency.in' => 'Please select a valid currency.',
                'course_fee.required' => 'Course fee is required.',
                'course_fee.numeric' => 'Course fee must be a number.',
                'course_fee.min' => 'Course fee must be at least 0.',
                'sscl_tax.required' => 'SSCL tax is required.',
                'sscl_tax.numeric' => 'SSCL tax must be a number.',
                'sscl_tax.min' => 'SSCL tax must be at least 0.',
                'sscl_tax.max' => 'SSCL tax cannot exceed 100%.',
                'bank_charges.numeric' => 'Bank charges must be a number.',
                'bank_charges.min' => 'Bank charges must be at least 0.',
                'start_date.required' => 'Start date is required.',
                'start_date.date' => 'Please enter a valid start date.',
                'end_date.required' => 'End date is required.',
                'end_date.date' => 'Please enter a valid end date.',
                'end_date.after_or_equal' => 'End date must be after or equal to start date.',
                'enrollment_end_date.date' => 'Please enter a valid enrollment end date.',
                'enrollment_end_date.after_or_equal' => 'Enrollment end date must be after or equal to start date.',
                'enrollment_end_date.before_or_equal' => 'Enrollment end date must be before or equal to end date.',
                'course_registration_id_pattern.required' => 'Course registration ID pattern is required.',
                'course_registration_id_pattern.regex' => 'Course registration ID pattern must contain at least one number.',
            ]);

            // 🧩 Fetch and store the course name as well
            $course = \App\Models\Course::find($request->course_id);
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found.',
                ], 404);
            }

            $validatedData['course_name'] = $course->course_name;

            // 🧩 Update intake with both course_id and course_name
            $intake->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Intake updated successfully.',
                'intake' => $intake
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for intake update: ', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating intake data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the intake.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * API endpoint to fetch payment plan details for autofill.
     */
    public function getPaymentPlanDetails(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string',
            'location' => 'required|string',
            'course_type' => 'required|string',
        ]);

        $course = \App\Models\Course::where('course_name', $request->course_name)->first();
        if (!$course) {
            return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
        }

        $plan = \App\Models\PaymentPlan::where('course_id', $course->course_id)
            ->where('location', $request->location)
            ->where('course_type', $request->course_type)
            ->latest()
            ->first();

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'No payment plan found for this course/location/type.'], 404);
        }

        return response()->json([
            'success' => true,
            'registration_fee' => $plan->registration_fee,
            'course_fee' => $plan->local_fee,
            'international_fee' => $plan->international_fee,
        ]);
    }
} 