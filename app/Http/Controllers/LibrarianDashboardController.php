<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;

class LibrarianDashboardController extends Controller
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
        // Pending library clearances
        $pendingCount = ClearanceRequest::where('clearance_type', 'library')
            ->where('status', 'pending')
            ->count();

        $approvedCount = ClearanceRequest::where('clearance_type', 'library')
            ->where('status', 'approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        $rejectedCount = ClearanceRequest::where('clearance_type', 'library')
            ->where('status', 'rejected')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        // Students who need library clearance
        $pendingList = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'library')
            ->where('status', 'pending')
            ->orderBy('requested_at', 'asc')
            ->get();

        // Recent clearance updates
        $recent = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'library')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.librarian_dashboard', compact(
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingList',
            'recent'
        ));
    }
}
