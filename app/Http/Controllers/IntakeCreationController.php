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
        $courses = Course::orderBy('course_name', 'asc')->pluck('course_name', 'course_name');
        $intakes = Intake::with('registrations')->orderBy('start_date', 'desc')->get();

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
                'course_name' => 'required|string|max:255',
                'batch' => 'required|string|max:255',
                'batch_size' => 'required|integer|min:1',
                'intake_mode' => ['required', Rule::in(['Physical', 'Online', 'Hybrid'])],
                'intake_type' => ['required', Rule::in(['Fulltime', 'Parttime'])],
                'registration_fee' => 'required|numeric|min:0',
                'course_fee' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'course_registration_id_start' => 'nullable|string',
                'course_registration_id_pattern' => 'nullable|string',
            ]);

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
            Log::error('Error storing intake data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the intake.',
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