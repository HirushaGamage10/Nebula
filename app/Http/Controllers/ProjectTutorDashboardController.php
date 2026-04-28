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
}
