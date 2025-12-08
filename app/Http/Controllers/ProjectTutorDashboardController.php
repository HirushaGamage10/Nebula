<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;

class ProjectTutorDashboardController extends Controller
{
   public function index()
{
    $location = auth()->user()->user_location ?? 'Welisara';

    // Normalize location
    $location = str_replace([
        'Nebula Institute of Technology – ',
        'Nebula Institute of Technology - '
    ], '', $location);

    $location = trim($location);

    // Pending project clearances
    $pendingCount = ClearanceRequest::where('clearance_type', 'project')
        ->where('location', $location)
        ->where('status', 'pending')
        ->count();

    // Approved this month
    $approvedCount = ClearanceRequest::where('clearance_type', 'project')
        ->where('location', $location)
        ->where('status', 'approved')
        ->whereMonth('approved_at', now()->month)
        ->count();

    // Rejected this month
    $rejectedCount = ClearanceRequest::where('clearance_type', 'project')
        ->where('location', $location)
        ->where('status', 'rejected')
        ->whereMonth('updated_at', now()->month)
        ->count();

    // Student Review List (Pending only)
    $pendingList = ClearanceRequest::with(['student', 'course', 'intake'])
        ->where('clearance_type', 'project')
        ->where('location', $location)
        ->where('status', 'pending')
        ->orderBy('requested_at', 'asc')
        ->get();

    // Recent updates
    $recent = ClearanceRequest::with(['student', 'course', 'intake'])
        ->where('clearance_type', 'project')
        ->where('location', $location)
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
