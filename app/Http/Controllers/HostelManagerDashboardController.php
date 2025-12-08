<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceRequest;
use App\Models\Course;
use App\Models\Intake;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HostelManagerDashboardController extends Controller
{
    public function showDashboard()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        
        return view('hostel_manager_dashboard', compact('courses', 'intakes'));
    }

    public function getOverviewMetrics(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $range = $request->range ?? 'month';
        
        $startDate = $this->getDateRange($range, $request);
        $endDate = now();
        
        // Total counts
        $totalPending = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'pending')->count();
            
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthPending = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'pending')
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
            
        $pendingChange = $lastMonthPending > 0 
            ? round((($totalPending - $lastMonthPending) / $lastMonthPending) * 100, 1)
            : 0;
            
        // Monthly counts with date range
        $monthlyQuery = ClearanceRequest::where('clearance_type', 'hostel')
            ->whereBetween('requested_at', [$startDate, $endDate]);
            
        $monthlyApproved = (clone $monthlyQuery)->where('status', 'approved')->count();
        $monthlyRejected = (clone $monthlyQuery)->where('status', 'rejected')->count();
        
        // Last month for comparison
        $lastMonthStart = Carbon::parse($startDate)->subMonth();
        $lastMonthEnd = Carbon::parse($endDate)->subMonth();
        
        $lastMonthApproved = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
            
        $lastMonthRejected = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'rejected')
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
            
        $approvedChange = $lastMonthApproved > 0 
            ? round((($monthlyApproved - $lastMonthApproved) / $lastMonthApproved) * 100, 1)
            : 0;
            
        $rejectedChange = $lastMonthRejected > 0 
            ? round((($monthlyRejected - $lastMonthRejected) / $lastMonthRejected) * 100, 1)
            : 0;
            
        // Trends data for chart
        $trends = $this->getRequestTrends($startDate, $endDate);
        
        // Status distribution
        $distribution = [
            'pending' => $totalPending,
            'approved' => $monthlyApproved,
            'rejected' => $monthlyRejected
        ];

        return response()->json([
            'totalPending' => $totalPending,
            'monthly' => [
                'pending' => $totalPending,
                'approved' => $monthlyApproved,
                'rejected' => $monthlyRejected
            ],
            'change' => [
                'pending' => $pendingChange >= 0 ? "+{$pendingChange}%" : "{$pendingChange}%",
                'approved' => $approvedChange >= 0 ? "+{$approvedChange}%" : "{$approvedChange}%",
                'rejected' => $rejectedChange >= 0 ? "+{$rejectedChange}%" : "{$rejectedChange}%"
            ],
            'trends' => $trends,
            'distribution' => $distribution
        ]);
    }
    
    public function getAnalytics(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        
        // Average processing time (in hours)
        $avgProcessingTime = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'approved')
            ->whereYear('approved_at', $year)
            ->whereMonth('approved_at', $month)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, requested_at, approved_at)) as avg_time')
            ->value('avg_time') ?? 0;
            
        // Clearance rate (approval percentage)
        $totalRequests = ClearanceRequest::where('clearance_type', 'hostel')
            ->whereYear('requested_at', $year)
            ->whereMonth('requested_at', $month)
            ->count();
            
        $approvedRequests = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'approved')
            ->whereYear('requested_at', $year)
            ->whereMonth('requested_at', $month)
            ->count();
            
        $clearanceRate = $totalRequests > 0 
            ? round(($approvedRequests / $totalRequests) * 100, 1)
            : 0;
            
        // Active requests (last 7 days)
        $activeRequests = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'pending')
            ->where('requested_at', '>=', now()->subDays(7))
            ->count();
            
        // Average completion time (in days)
        $avgCompletionTime = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', 'approved')
            ->whereYear('approved_at', $year)
            ->whereMonth('approved_at', $month)
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, requested_at, approved_at)) as avg_days')
            ->value('avg_days') ?? 0;

        return response()->json([
            'avg_processing_time' => round($avgProcessingTime, 1),
            'clearance_rate' => $clearanceRate,
            'active_requests' => $activeRequests,
            'avg_completion_time' => round($avgCompletionTime, 1)
        ]);
    }

    public function getActionList(Request $request)
    {
        $perPage = 10;
        $query = ClearanceRequest::where('clearance_type', 'hostel')
            ->with(['student', 'course', 'intake']);
            
        // Apply filters
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->intake_id) {
            $query->where('intake_id', $request->intake_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->search) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%");
            });
        }
        
        // Apply date range
        if ($request->date_range && $request->date_range !== 'all') {
            $dateRange = $this->getDateRange($request->date_range);
            $query->whereBetween('requested_at', $dateRange);
        }
        
        // Apply sorting
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('requested_at', 'asc');
                break;
            case 'name_asc':
                $query->join('students', 'clearance_requests.student_id', '=', 'students.id')
                      ->orderBy('students.full_name', 'asc');
                break;
            case 'name_desc':
                $query->join('students', 'clearance_requests.student_id', '=', 'students.id')
                      ->orderBy('students.full_name', 'desc');
                break;
            default: // 'newest'
                $query->orderBy('requested_at', 'desc');
        }
        
        $requests = $query->paginate($perPage);
        
        // Add days pending for each request
        $requests->getCollection()->transform(function ($request) {
            $request->days_pending = $request->status === 'pending' 
                ? Carbon::parse($request->requested_at)->diffInDays(now())
                : null;
                
            $request->processing_time = $request->approved_at 
                ? Carbon::parse($request->requested_at)->diffInHours(Carbon::parse($request->approved_at)) . 'h'
                : null;
                
            return $request;
        });

        return response()->json($requests);
    }

    public function getRecentHostelClearances()
    {
        $requests = ClearanceRequest::where('clearance_type', 'hostel')
            ->with(['student', 'course'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($request) {
                $request->processing_time = $request->approved_at 
                    ? Carbon::parse($request->requested_at)->diffInHours(Carbon::parse($request->approved_at)) . 'h'
                    : 'N/A';
                    
                return $request;
            });

        return response()->json($requests);
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

        if ($request->sort === 'newest') {
            $query->orderBy('requested_at', 'desc');
        } else {
            $query->orderBy('requested_at', 'asc');
        }

        $requests = $query->get()->map(function ($request) {
            $request->days_pending = $request->status === 'pending' 
                ? Carbon::parse($request->requested_at)->diffInDays(now())
                : null;
            return $request;
        });

        return response()->json($requests);
    }

    public function searchRequests(Request $request)
    {
        $requests = ClearanceRequest::where('clearance_type', 'hostel')
            ->with(['student', 'course', 'intake'])
            ->whereHas('student', function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%");
            })
            ->get()
            ->map(function ($request) {
                $request->days_pending = $request->status === 'pending' 
                    ? Carbon::parse($request->requested_at)->diffInDays(now())
                    : null;
                return $request;
            });

        return response()->json($requests);
    }

    public function listByStatus(Request $request)
    {
        $requests = ClearanceRequest::where('clearance_type', 'hostel')
            ->where('status', $request->status)
            ->with(['student', 'course', 'intake'])
            ->orderBy('requested_at', 'desc')
            ->paginate(10)
            ->through(function ($request) {
                $request->formatted_date = Carbon::parse($request->requested_at)->format('Y-m-d H:i');
                return $request;
            });

        return response()->json($requests);
    }
    
    private function getDateRange($range, $request = null)
    {
        switch ($range) {
            case 'today':
                return now()->startOfDay();
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            case 'custom':
                return $request && $request->startDate 
                    ? Carbon::parse($request->startDate)->startOfDay()
                    : now()->startOfMonth();
            default:
                return now()->subYear()->startOfDay();
        }
    }
    
    private function getRequestTrends($startDate, $endDate)
    {
        $daysDiff = $startDate->diffInDays($endDate);
        
        if ($daysDiff <= 31) {
            // Daily trends
            $trends = ClearanceRequest::where('clearance_type', 'hostel')
                ->whereBetween('requested_at', [$startDate, $endDate])
                ->selectRaw('DATE(requested_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            $labels = [];
            $data = [];
            
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $count = $trends->firstWhere('date', $dateStr)->count ?? 0;
                
                $labels[] = $currentDate->format('M d');
                $data[] = $count;
                
                $currentDate->addDay();
            }
        } else {
            // Monthly trends
            $trends = ClearanceRequest::where('clearance_type', 'hostel')
                ->whereBetween('requested_at', [$startDate, $endDate])
                ->selectRaw('YEAR(requested_at) as year, MONTH(requested_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
                
            $labels = [];
            $data = [];
            
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $year = $currentDate->year;
                $month = $currentDate->month;
                
                $count = $trends->first(function ($item) use ($year, $month) {
                    return $item->year == $year && $item->month == $month;
                })->count ?? 0;
                
                $labels[] = $currentDate->format('M Y');
                $data[] = $count;
                
                $currentDate->addMonth();
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}