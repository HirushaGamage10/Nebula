<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\CourseRegistration;
use App\Models\PaymentPlan;
use App\Models\Discount;
use Illuminate\Support\Facades\Log;

class PaymentDiscountController extends Controller
{
    // Show the payment discount page
    public function showPage()
    {
        return view('payment_discount');
    }

    // Fetch courses by location (AJAX)
    public function getCoursesByLocation(Request $request)
    {
        $location = $request->input('location');
        $courses = Course::where('location', $location)->get(['course_id', 'course_name', 'local_fee', 'registration_fee']);
        return response()->json(['courses' => $courses]);
    }

    // Fetch intakes by course (AJAX)
    public function getIntakesByCourse(Request $request)
    {
        $courseId = $request->input('course_id');
        $intakes = Intake::where('course_id', $courseId)->get(['intake_id', 'batch']);
        return response()->json(['intakes' => $intakes]);
    }

    // Fetch payment plan for a course/intake (AJAX)
    public function getPaymentPlan(Request $request)
    {
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        // Example: fetch payment plan for the course/intake
        $plans = PaymentPlan::where('course_id', $courseId)
            ->where('intake_id', $intakeId)
            ->orderBy('installment_no')
            ->get(['installment_no', 'type', 'amount', 'due_date']);
        return response()->json(['payment_plan' => $plans]);
    }

    // Save SLT loan data (AJAX)
    public function saveSltLoan(Request $request)
    {
        // Save logic here (e.g., to a new SltLoan model/table)
        // $request->input('location'), ...
        return response()->json(['success' => true, 'message' => 'SLT loan data saved.']);
    }

    // Save discount data (AJAX)
    public function saveDiscount(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:percentage,amount',
                'value' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);

            $discount = Discount::create([
                'name' => $request->name,
                'type' => $request->type,
                'value' => $request->value,
                'status' => 'active',
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Discount saved successfully.',
                'discount' => $discount
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving discount: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error saving discount: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get all discounts (AJAX)
    public function getDiscounts()
    {
        try {
            $discounts = Discount::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'discounts' => $discounts
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching discounts: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error fetching discounts: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update discount (AJAX)
    public function updateDiscount(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:discounts,id',
                'name' => 'required|string|max:255',
                'type' => 'required|in:percentage,amount',
                'value' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);

            $discount = Discount::findOrFail($request->id);
            $discount->update([
                'name' => $request->name,
                'type' => $request->type,
                'value' => $request->value,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Discount updated successfully.',
                'discount' => $discount
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating discount: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error updating discount: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete discount (AJAX)
    public function deleteDiscount(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:discounts,id'
            ]);

            $discount = Discount::findOrFail($request->id);
            $discount->update(['status' => 'inactive']);

            return response()->json([
                'success' => true, 
                'message' => 'Discount deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting discount: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting discount: ' . $e->getMessage()
            ], 500);
        }
    }
} 