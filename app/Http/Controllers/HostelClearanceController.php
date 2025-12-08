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
        return view('hostel_clearance');
    }

    public function showHostelClearanceFormManagement(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        $perPage = 10;
        
        // Initialize queries
        $pendingRequestsQuery = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_HOSTEL)
            ->where('status', ClearanceRequest::STATUS_PENDING)
            ->with(['student', 'course', 'intake'])
            ->orderBy('requested_at', 'desc');

        $approvedRequestsQuery = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_HOSTEL)
            ->where('status', ClearanceRequest::STATUS_APPROVED)
            ->with(['student', 'course', 'intake', 'approvedBy'])
            ->orderBy('approved_at', 'desc');

        $rejectedRequestsQuery = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_HOSTEL)
            ->where('status', ClearanceRequest::STATUS_REJECTED)
            ->with(['student', 'course', 'intake', 'approvedBy'])
            ->orderBy('approved_at', 'desc');

        // Apply search if provided
        $search = $request->get('search');
        if ($search) {
            $pendingRequestsQuery->whereHas('student', function($query) use ($search) {
                $query->where('student_id', 'LIKE', "%{$search}%")
                    ->orWhere('name_with_initials', 'LIKE', "%{$search}%");
            });
            
            $approvedRequestsQuery->whereHas('student', function($query) use ($search) {
                $query->where('student_id', 'LIKE', "%{$search}%")
                    ->orWhere('name_with_initials', 'LIKE', "%{$search}%");
            });
            
            $rejectedRequestsQuery->whereHas('student', function($query) use ($search) {
                $query->where('student_id', 'LIKE', "%{$search}%")
                    ->orWhere('name_with_initials', 'LIKE', "%{$search}%");
            });
        }

        // Get paginated results
        $pendingRequests = $pendingRequestsQuery->paginate($perPage, ['*'], 'pending_page');
        $approvedRequests = $approvedRequestsQuery->paginate($perPage, ['*'], 'approved_page');
        $rejectedRequests = $rejectedRequestsQuery->paginate($perPage, ['*'], 'rejected_page');

        return view('hostel_clearance', compact(
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'tab',
            'search'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|string|max:255',
                'student_name' => 'required|string|max:255',
                'payment_date' => 'required|date',
                'is_cleared' => 'required|boolean',
            ]);

            Hostel::create($validated);

            return redirect()->back()->with('success', 'Student details saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getStudentDetails(Request $request)
    {
        $studentId = $request->get('student_id');
        $student = Students::where('student_id', $studentId)->first();

        if ($student) {
            return response()->json([
                'success' => true,
                'name' => $student->name_with_initials,
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
            'student_id' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'is_cleared' => 'required|boolean',
        ]);

        $record = Hostel::where('student_id', $validated['student_id'])
            ->where('payment_date', $validated['payment_date'])
            ->first();

        if (!$record) {
            return redirect()->back()->with('error', 'Record not found for the specified student and date.');
        }

        $record->update([
            'payment_date' => $validated['payment_date'],
            'is_cleared' => $validated['is_cleared']
        ]);

        return redirect()->back()->with('success', 'Received date and clearance status updated successfully!');
    }

    public function search(Request $request)
    {
        $studentId = $request->get('student_id');
        $records = Hostel::where('student_id', $studentId)->get();
        return view('hostel_clearance', compact('records', 'studentId'));
    }

    public function approveClearance(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:clearance_requests,id',
            'remarks' => 'nullable|string|max:500',
            'clearance_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            $clearanceRequest = ClearanceRequest::findOrFail($request->request_id);
            
            if ($clearanceRequest->clearance_type !== ClearanceRequest::TYPE_HOSTEL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid clearance type for this controller.'
                ], 400);
            }

            $filePath = null;
            if ($request->hasFile('clearance_slip')) {
                $file = $request->file('clearance_slip');
                $fileName = 'hostel_clearance_' . $clearanceRequest->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('clearance_slips', $fileName, 'public');
            }

            $clearanceRequest->approve(auth()->id(), $request->remarks, $filePath);

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

    public function rejectClearance(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:clearance_requests,id',
            'remarks' => 'nullable|string|max:500',
            'clearance_slip' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            $clearanceRequest = ClearanceRequest::findOrFail($request->request_id);
            
            if ($clearanceRequest->clearance_type !== ClearanceRequest::TYPE_HOSTEL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid clearance type for this controller.'
                ], 400);
            }

            $filePath = null;
            if ($request->hasFile('clearance_slip')) {
                $file = $request->file('clearance_slip');
                $fileName = 'hostel_clearance_' . $clearanceRequest->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('clearance_slips', $fileName, 'public');
            }

            $clearanceRequest->reject(auth()->id(), $request->remarks, $filePath);

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