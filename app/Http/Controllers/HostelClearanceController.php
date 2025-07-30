<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Students;
use App\Models\ClearanceRequest;
use App\Models\Course;
use App\Models\Intake;
use Illuminate\Http\Request;

class HostelClearanceController extends Controller
{
     public function index()
    {
        return view('hostel_clearance'); // Make sure this blade file exists
    }
    public function showHostelClearanceFormManagement()
    {
        // Get pending hostel clearance requests
        $pendingRequests = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_HOSTEL)
            ->where('status', ClearanceRequest::STATUS_PENDING)
            ->with(['student', 'course', 'intake'])
            ->orderBy('requested_at', 'desc')
            ->get();

        // Get approved/rejected requests for history
        $processedRequests = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_HOSTEL)
            ->whereIn('status', [ClearanceRequest::STATUS_APPROVED, ClearanceRequest::STATUS_REJECTED])
            ->with(['student', 'course', 'intake', 'approvedBy'])
            ->orderBy('approved_at', 'desc')
            ->limit(50)
            ->get();

        return view('hostel_clearance', compact('pendingRequests', 'processedRequests'));
    }

    public function store(Request $request)
    {
        try {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
          //  'name_of_the_book' => 'nullable|string|max:255',
           // 'fine_amount' => 'nullable|numeric',
            'payment_date' => 'required|date',
            //'date_received' => 'nullable|date',
            'is_cleared' => 'required|boolean',
        ]);


        Hostel::create($validated);

        return redirect()->back()->with('success', 'Student details saved successfully!');
    } catch (\Exception $e) {
        // Handle any unexpected errors and redirect back with an error message
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getStudentDetails(Request $request)
    {
        $studentId = $request->get('student_id');

        // Fetch the student by student_id
        $student = Students::where('student_id', $studentId)->first();

        if ($student) {
            return response()->json([
                'success' => true,
                'name' => $student->name_with_initials, // Adjust this field based on the database
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Student not found.',
        ]);
    }

    public function updateClearance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255', // Ensure the student_id is provided
            'payment_date' => 'required|date', // Ensure the date_received is provided and valid
            'is_cleared' => 'required|boolean',
        ]);

        // Find the record for the student
        $record = Hostel::where('student_id', $validated['student_id'])
        ->where('payment_date', $validated['payment_date'])
            ->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Record not found for the specified student and date.');
        }

        // Update the date_received field
        $record->update([
            'payment_date' => $validated['payment_date'],
            'is_cleared' => $validated['is_cleared']
    ]);

        return redirect()->back()->with('success', 'Received date and and clearance status updated successfully!');
    }

    
    public function search(Request $request)
    {
        $studentId = $request->get('student_id');
        $records = Hostel::where('student_id', $studentId)->get();
        return view('hostel_clearance', compact('records', 'studentId'));
    }

    /**
     * Approve a clearance request
     */
    public function approveClearance(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:clearance_requests,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $clearanceRequest = ClearanceRequest::findOrFail($request->request_id);
            
            if ($clearanceRequest->clearance_type !== ClearanceRequest::TYPE_HOSTEL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid clearance type for this controller.'
                ], 400);
            }

            $clearanceRequest->approve(auth()->id(), $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Clearance request approved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve clearance request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a clearance request
     */
    public function rejectClearance(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:clearance_requests,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $clearanceRequest = ClearanceRequest::findOrFail($request->request_id);
            
            if ($clearanceRequest->clearance_type !== ClearanceRequest::TYPE_HOSTEL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid clearance type for this controller.'
                ], 400);
            }

            $clearanceRequest->reject(auth()->id(), $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Clearance request rejected successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject clearance request: ' . $e->getMessage()
            ], 500);
        }
    }





    
}
