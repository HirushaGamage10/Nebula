<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;

class ProjectTutorDashboardController extends Controller
{
   private function normalizeLocation(?string $location): string
   {
       $location = $location ?? 'Welisara';
       $location = str_replace([
           'Nebula Institute of Technology – ',
           'Nebula Institute of Technology - '
       ], '', $location);

       return trim($location);
   }

   private function applyLocationScope($query, string $location)
   {
       return $query->whereRaw(
           "LOWER(TRIM(REPLACE(REPLACE(location, 'Nebula Institute of Technology – ', ''), 'Nebula Institute of Technology - ', ''))) = ?",
           [strtolower($location)]
       );
   }

   public function index()
{
    // Pending project clearances
    $pendingCount = ClearanceRequest::where('clearance_type', 'project')
        ->where('status', 'pending')
        ->count();

    // Approved this month
    $approvedCount = ClearanceRequest::where('clearance_type', 'project')
        ->where('status', 'approved')
        ->whereMonth('approved_at', now()->month)
        ->whereYear('approved_at', now()->year)
        ->count();

    // Rejected this month
    $rejectedCount = ClearanceRequest::where('clearance_type', 'project')
        ->where('status', 'rejected')
        ->whereMonth('updated_at', now()->month)
        ->whereYear('updated_at', now()->year)
        ->count();

    // Student Review List (Pending only)
    $pendingList = ClearanceRequest::with(['student', 'course', 'intake'])
        ->where('clearance_type', 'project')
        ->where('status', 'pending')
        ->orderBy('requested_at', 'asc')
        ->get();

    // Recent updates
    $recent = ClearanceRequest::with(['student', 'course', 'intake'])
        ->where('clearance_type', 'project')
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

    return view('dashboards.project_tutor_dashboard', compact(
        'pendingCount',
        'approvedCount',
        'rejectedCount',
        'pendingList',
        'recent'
    ));
}

    public function getPendingClearances(Request $request)
    {
        $location = $request->query('location');
        $query = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'project')
            ->where('status', 'pending');

        if ($location && $location !== 'all') {
            $this->applyLocationScope($query, $this->normalizeLocation($location));
        }

        $list = $query->orderBy('requested_at', 'asc')->get();
        return response()->json(['success' => true, 'data' => $list]);
    }

    public function getRecentUpdates(Request $request)
    {
        $location = $request->query('location');
        $query = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'project');

        if ($location && $location !== 'all') {
            $this->applyLocationScope($query, $this->normalizeLocation($location));
        }

        $list = $query->orderBy('updated_at', 'desc')->limit(10)->get();
        return response()->json(['success' => true, 'data' => $list]);
    }

    public function getSummary(Request $request)
    {
        $location = $request->query('location');
        $query = ClearanceRequest::where('clearance_type', 'project');

        if ($location && $location !== 'all') {
            $this->applyLocationScope($query, $this->normalizeLocation($location));
        }

        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $approvedCount = (clone $query)->where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();
        $rejectedCount = (clone $query)->where('status', 'rejected')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return response()->json([
            'success' => true,
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
        ]);
    }

    public function approveProject($id, Request $request)
    {
        $clearanceRequest = ClearanceRequest::findOrFail($id);
        $clearanceRequest->approve(auth()->id(), $request->input('remarks'));
        return response()->json(['success' => true, 'message' => 'Project clearance approved.']);
    }

    public function rejectProject($id, Request $request)
    {
        $clearanceRequest = ClearanceRequest::findOrFail($id);
        $clearanceRequest->reject(auth()->id(), $request->input('remarks'));
        return response()->json(['success' => true, 'message' => 'Project clearance rejected.']);
    }
}
