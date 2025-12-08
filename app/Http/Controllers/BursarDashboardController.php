<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;

class BursarDashboardController extends Controller
{
    public function index()
    {
        $location = auth()->user()->user_location ?? 'Welisara';

        // Normalize location format
        $location = str_replace([
            'Nebula Institute of Technology – ',
            'Nebula Institute of Technology - '
        ], '', $location);

        $location = trim($location);

        // Total pending payment or bursary clearances
        $pendingCount = ClearanceRequest::where('clearance_type', 'payment')
            ->where('location', $location)
            ->where('status', 'pending')
            ->count();

        // List of pending student financial clearances
        $pendingList = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'payment')
            ->where('location', $location)
            ->where('status', 'pending')
            ->orderBy('requested_at', 'asc')
            ->get();

        // Recently updated payment clearances
        $recent = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'payment')
            ->where('location', $location)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.bursar_dashboard', compact(
            'pendingCount',
            'pendingList',
            'recent'
        ));
    }
}
