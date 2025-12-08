<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;

class LibrarianDashboardController extends Controller
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

        // Pending library clearances
        $pendingCount = ClearanceRequest::where('clearance_type', 'library')
            ->where('location', $location)
            ->where('status', 'pending')
            ->count();

        // Students who need library clearance
        $pendingList = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'library')
            ->where('location', $location)
            ->where('status', 'pending')
            ->orderBy('requested_at', 'asc')
            ->get();

        // Recent clearance updates
        $recent = ClearanceRequest::with(['student', 'course', 'intake'])
            ->where('clearance_type', 'library')
            ->where('location', $location)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.librarian_dashboard', compact(
            'pendingCount',
            'pendingList',
            'recent'
        ));
    }
}
