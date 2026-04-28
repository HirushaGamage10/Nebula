<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;

class BursarDashboardController extends Controller
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
        // Total pending payment or bursary clearances
        $pendingCount = ClearanceRequest::where('clearance_type', 'payment')
            ->where('status', 'pending')
            ->count();

        $approvedCount = ClearanceRequest::where('clearance_type', 'payment')
            ->where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        $rejectedCount = ClearanceRequest::where('clearance_type', 'payment')
            ->where('status', 'rejected')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        // List of pending student financial clearances
        $pendingList = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'payment')
            ->where('status', 'pending')
            ->orderBy('requested_at', 'asc')
            ->get();

        // Recently updated payment clearances
        $recent = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'payment')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.bursar_dashboard', compact(
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingList',
            'recent'
        ));
    }
}
