<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use App\Models\Course;
use App\Models\Intake;

class PaymentClearanceController extends Controller
{
    public function index()
    {
        // Get pending payment clearance requests
        $pendingRequests = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_PAYMENT)
            ->where('status', ClearanceRequest::STATUS_PENDING)
            ->with(['student', 'course', 'intake'])
            ->orderBy('requested_at', 'desc')
            ->get();

        // Get approved/rejected requests for history
        $processedRequests = ClearanceRequest::where('clearance_type', ClearanceRequest::TYPE_PAYMENT)
            ->whereIn('status', [ClearanceRequest::STATUS_APPROVED, ClearanceRequest::STATUS_REJECTED])
            ->with(['student', 'course', 'intake', 'approvedBy'])
            ->orderBy('approved_at', 'desc')
            ->limit(50)
            ->get();

        return view('payment_clearance', compact('pendingRequests', 'processedRequests'));
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
            
            if ($clearanceRequest->clearance_type !== ClearanceRequest::TYPE_PAYMENT) {
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
            
            if ($clearanceRequest->clearance_type !== ClearanceRequest::TYPE_PAYMENT) {
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