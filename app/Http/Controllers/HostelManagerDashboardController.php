<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use Carbon\Carbon;

class HostelManagerDashboardController extends Controller
{
    public function showDashboard()
    {
        return view('hostel_manager_dashboard');
    }

    public function getOverviewMetrics(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        return response()->json([

            // Total counts (not filtered)
            'totalPending' => ClearanceRequest::where('clearance_type', 'hostel')
                ->where('status', 'pending')->count(),

            'approvedToday' => ClearanceRequest::where('clearance_type', 'hostel')
                ->where('status', 'approved')
                ->whereDate('approved_at', now())
                ->count(),

            'totalRejected' => ClearanceRequest::where('clearance_type', 'hostel')
                ->where('status', 'rejected')->count(),

            // Monthly counts
            'monthly' => [
                'pending' => ClearanceRequest::where('clearance_type', 'hostel')
                    ->where('status', 'pending')
                    ->whereYear('requested_at', $year)
                    ->whereMonth('requested_at', $month)
                    ->count(),

                'approved' => ClearanceRequest::where('clearance_type', 'hostel')
                    ->where('status', 'approved')
                    ->whereYear('approved_at', $year)
                    ->whereMonth('approved_at', $month)
                    ->count(),

                'rejected' => ClearanceRequest::where('clearance_type', 'hostel')
                    ->where('status', 'rejected')
                    ->whereYear('updated_at', $year)
                    ->whereMonth('updated_at', $month)
                    ->count()
            ]
        ]);
    }


    public function getActionList()
    {
        return response()->json(
            ClearanceRequest::where('clearance_type', 'hostel')
                ->with(['student', 'course', 'intake'])
                ->orderBy('requested_at', 'desc')
                ->get()
        );
    }

    public function getRecentHostelClearances()
    {
        return response()->json(
            ClearanceRequest::where('clearance_type', 'hostel')
                ->with(['student', 'course'])
                ->latest()
                ->take(10)
                ->get()
        );
    }

    public function filterRequests(Request $request)
    {
        $query = ClearanceRequest::where('clearance_type', 'hostel')
            ->with(['student', 'course', 'intake']);

        if ($request->course_id) $query->where('course_id', $request->course_id);
        if ($request->intake_id) $query->where('intake_id', $request->intake_id);
        if ($request->status) $query->where('status', $request->status);
        if ($request->year) $query->whereYear('requested_at', $request->year);
        if ($request->month) $query->whereMonth('requested_at', $request->month);

        if ($request->sort === 'newest') $query->orderBy('requested_at', 'desc');
        else $query->orderBy('requested_at', 'asc');

        return response()->json($query->get());
    }

    public function searchRequests(Request $request)
    {
        return response()->json(
            ClearanceRequest::where('clearance_type', 'hostel')
                ->with(['student', 'course', 'intake'])
                ->whereHas('student', function($q) use ($request) {
                    $q->where('full_name', 'like', "%{$request->search}%")
                      ->orWhere('student_id', 'like', "%{$request->search}%");
                })
                ->get()
        );
    }

    public function listByStatus(Request $request)
    {
        return response()->json(
            ClearanceRequest::where('clearance_type', 'hostel')
                ->where('status', $request->status)
                ->with(['student', 'course', 'intake'])
                ->orderBy('requested_at', 'desc')
                ->paginate(10)
        );
    }
}
