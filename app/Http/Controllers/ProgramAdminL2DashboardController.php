<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\Intake;
use App\Models\Course;
use App\Models\Semester;
use App\Models\SemesterRegistration;
use App\Models\ClearanceRequest;
use App\Models\ExamResult;
use App\Models\Attendance;
use App\Models\PaymentDetail;
use App\Models\Module;

class ProgramAdminL2DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function showDashboard()
    {
        return view('dashboards.program_admin_l2');
    }

    /**
     * Get overview metrics (KPIs)
     */
    public function getOverviewMetrics(Request $request)
    {
        $location = auth()->user()->user_location ?? 'Welisara';
        $location = str_replace('Nebula Institute of Technology – ', '', $location);
        $location = str_replace('Nebula Institute of Technology - ', '', $location);
        
        try {
            // Total Active Students (based on course registration status 'Registered')
            $totalActiveStudents = CourseRegistration::where('location', $location)
                ->where('status', 'Registered')
                ->distinct('student_id')
                ->count('student_id');
            
            // Active Batches (Intakes with registered students)
            $activeBatches = Intake::where('location', $location)
                ->whereHas('courseRegistrations', function ($query) {
                    $query->where('status', 'Registered');
                })
                ->count();
            
            // Pending Registration Approvals (course registrations pending approval)
            $pendingApprovals = CourseRegistration::where('location', $location)
                ->where('status', 'Pending')
                ->count();
            
            // Student Count by Batch/Intake
            $studentCountByBatch = Intake::where('location', $location)
                ->withCount(['courseRegistrations' => function ($query) {
                    $query->where('status', 'Registered');
                }])
                ->orderBy('course_registrations_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($intake) {
                    return [
                        'batch' => $intake->batch,
                        'course_name' => $intake->course_name,
                        'count' => $intake->course_registrations_count
                    ];
                });
            
            // Today's new registrations
            $todayRegistrations = CourseRegistration::where('location', $location)
                ->whereDate('registration_date', Carbon::today())
                ->where('status', 'Registered')
                ->count();
            
            // Yesterday's registrations for growth calculation
            $yesterdayRegistrations = CourseRegistration::where('location', $location)
                ->whereDate('registration_date', Carbon::yesterday())
                ->where('status', 'Registered')
                ->count();
            
            // Calculate growth percentage
            $growthPercentage = 0;
            if ($yesterdayRegistrations > 0) {
                $growthPercentage = (($todayRegistrations - $yesterdayRegistrations) / $yesterdayRegistrations) * 100;
            }
            
            // Pending clearance requests
            $pendingClearances = ClearanceRequest::where('location', $location)
                ->where('status', 'pending')
                ->count();
            
            // Students needing special approval
            $specialApprovalNeeded = CourseRegistration::where('location', $location)
                ->where('status', 'Special approval required')
                ->count();
            
            // Average attendance rate
            $avgAttendance = Attendance::where('location', $location)
                ->selectRaw('AVG(status) as avg_attendance')
                ->first();
            $avgAttendanceRate = $avgAttendance ? round($avgAttendance->avg_attendance * 100, 1) : 0;
            
            // Pass rate from exam results
            $totalExamResults = ExamResult::where('location', $location)->count();
            $passResults = ExamResult::where('location', $location)
                ->where(function ($query) {
                    $query->where('grade', 'A')
                        ->orWhere('grade', 'B')
                        ->orWhere('grade', 'C')
                        ->orWhere('grade', 'D')
                        ->orWhere(function ($q) {
                            $q->whereNotNull('marks')
                                ->where('marks', '>=', 40);
                        });
                })
                ->count();
            $passRate = $totalExamResults > 0 ? round(($passResults / $totalExamResults) * 100, 1) : 0;
            
            // Semester registrations this month
            $monthSemesterReg = SemesterRegistration::where('location', $location)
                ->whereMonth('registration_date', Carbon::now()->month)
                ->whereYear('registration_date', Carbon::now()->year)
                ->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_active_students' => $totalActiveStudents,
                    'active_batches' => $activeBatches,
                    'pending_approvals' => $pendingApprovals,
                    'today_registrations' => $todayRegistrations,
                    'growth_percentage' => round($growthPercentage, 2),
                    'pending_clearances' => $pendingClearances,
                    'special_approval_needed' => $specialApprovalNeeded,
                    'avg_attendance_rate' => $avgAttendanceRate,
                    'pass_rate' => $passRate,
                    'month_semester_reg' => $monthSemesterReg,
                    'student_count_by_batch' => $studentCountByBatch,
                    'location' => $location
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending approval registrations
     */
    public function getPendingApprovals(Request $request)
    {
        $location = auth()->user()->user_location ?? 'Welisara';
        $location = str_replace('Nebula Institute of Technology – ', '', $location);
        $location = str_replace('Nebula Institute of Technology - ', '', $location);
        
        try {
            $pendingApprovals = CourseRegistration::where('location', $location)
                ->where('status', 'Pending')
                ->with(['student', 'course', 'intake'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($registration) {
                    return [
                        'id' => $registration->id,
                        'student_id' => $registration->student_id,
                        'student_name' => $registration->student->full_name ?? 'N/A',
                        'course_name' => $registration->course->course_name ?? $registration->course_id,
                        'batch' => $registration->intake->batch ?? 'N/A',
                        'registration_date' => $registration->registration_date,
                        'registration_fee' => $registration->registration_fee,
                        'counselor_name' => $registration->counselor_name,
                        'remarks' => $registration->remarks,
                        'created_at' => $registration->created_at->format('Y-m-d H:i:s')
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $pendingApprovals
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pending approvals'
            ], 500);
        }
    }

    /**
     * Get active semester data
     */
    public function getActiveSemesters(Request $request)
    {
        $location = auth()->user()->user_location ?? 'Welisara';
        $location = str_replace('Nebula Institute of Technology – ', '', $location);
        $location = str_replace('Nebula Institute of Technology - ', '', $location);
        
        try {
            $activeSemesters = Semester::whereHas('course', function ($query) use ($location) {
                    $query->where('location', $location);
                })
                ->where('status', 'active')
                ->with(['course', 'semesterRegistrations' => function ($query) {
                    $query->where('status', 'registered');
                }])
                ->get()
                ->map(function ($semester) {
                    return [
                        'id' => $semester->id,
                        'name' => $semester->name,
                        'course_name' => $semester->course->course_name ?? 'N/A',
                        'start_date' => $semester->start_date,
                        'end_date' => $semester->end_date,
                        'registered_count' => $semester->semesterRegistrations->count(),
                        'status' => $semester->status
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $activeSemesters
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load active semesters'
            ], 500);
        }
    }

    /**
     * Get modules for a selected course
     */
    public function getModulesByCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,course_id',
        ]);

        try {
            $courseId = (int) $request->get('course_id');

            $modules = Module::query()
                ->whereHas('courses', function ($query) use ($courseId) {
                    $query->where('courses.course_id', $courseId);
                })
                ->select('module_id', 'module_name', 'module_code')
                ->orderBy('module_name')
                ->get()
                ->map(function ($module) {
                    return [
                        'module_id' => $module->module_id,
                        'module_name' => $module->module_name,
                        'module_code' => $module->module_code,
                        'display_name' => trim(($module->module_code ? $module->module_code . ' - ' : '') . $module->module_name),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $modules,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load modules',
            ], 500);
        }
    }

    /**
     * Get student academic performance
     */
    public function getAcademicPerformance(Request $request)
    {
        $request->validate([
            'location' => 'nullable|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'nullable|exists:courses,course_id',
            'intake_id' => 'nullable|exists:intakes,intake_id',
        ]);

        $defaultLocation = auth()->user()->user_location ?? 'Welisara';
        $defaultLocation = str_replace('Nebula Institute of Technology – ', '', $defaultLocation);
        $defaultLocation = str_replace('Nebula Institute of Technology - ', '', $defaultLocation);

        $location = $request->filled('location') ? $request->location : $defaultLocation;
        $courseId = $request->get('course_id');
        $intakeId = $request->get('intake_id');
        
        try {
            $baseQuery = ExamResult::where('location', $location)
                ->when($courseId, function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->when($intakeId, function ($query) use ($intakeId) {
                    $query->where('intake_id', $intakeId);
                });

            // Get exam results with grades
            $performanceData = (clone $baseQuery)
                ->select('grade', DB::raw('COUNT(*) as count'))
                ->whereNotNull('grade')
                ->groupBy('grade')
                ->get();
            
            // Get course-wise pass rates
            $coursePerformance = (clone $baseQuery)
                ->select('course_id', 
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN marks >= 40 OR grade IN ("A", "B", "C", "D") THEN 1 ELSE 0 END) as passed')
                )
                ->groupBy('course_id')
                ->with('course')
                ->get()
                ->map(function ($item) {
                    $passRate = $item->total > 0 ? round(($item->passed / $item->total) * 100, 1) : 0;
                    return [
                        'course_name' => $item->course->course_name ?? 'N/A',
                        'pass_rate' => $passRate,
                        'total' => $item->total,
                        'passed' => $item->passed
                    ];
                })
                ->sortByDesc('pass_rate')
                ->values();
            
            // Get repeat students (failed in exam)
            $repeatStudents = (clone $baseQuery)
                ->where(function ($query) {
                    $query->where('marks', '<', 40)
                        ->orWhere('grade', 'F')
                        ->orWhere('grade', 'NA')
                        ->orWhere('remarks', 'like', '%repeat%');
                })
                ->distinct('student_id')
                ->count('student_id');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'grade_distribution' => $performanceData,
                    'course_performance' => $coursePerformance,
                    'repeat_students' => $repeatStudents,
                    'filters' => [
                        'location' => $location,
                        'course_id' => $courseId,
                        'intake_id' => $intakeId,
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load academic performance data'
            ], 500);
        }
    }

    /**
     * Get attendance overview
     */
    public function getAttendanceOverview(Request $request)
    {
        $request->validate([
            'location' => 'nullable|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'nullable|exists:courses,course_id',
            'intake_id' => 'nullable|exists:intakes,intake_id',
            'module_id' => 'nullable|exists:modules,module_id',
            'period' => 'nullable|in:today,week,month,quarter',
        ]);

        $defaultLocation = auth()->user()->user_location ?? 'Welisara';
        $defaultLocation = str_replace('Nebula Institute of Technology – ', '', $defaultLocation);
        $defaultLocation = str_replace('Nebula Institute of Technology - ', '', $defaultLocation);

        $location = $request->filled('location') ? $request->location : $defaultLocation;
        $courseId = $request->get('course_id');
        $intakeId = $request->get('intake_id');
        $moduleId = $request->get('module_id');
        
        $period = $request->get('period', 'month');
        $endDate = Carbon::now();
        
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'quarter':
                $startDate = Carbon::now()->subMonths(3);
                break;
            default:
                $startDate = Carbon::now()->subMonth();
        }
        
        try {
            $baseQuery = Attendance::where('location', $location)
                ->whereBetween('date', [$startDate, $endDate])
                ->when($courseId, function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->when($intakeId, function ($query) use ($intakeId) {
                    $query->where('intake_id', $intakeId);
                })
                ->when($moduleId, function ($query) use ($moduleId) {
                    $query->where('module_id', $moduleId);
                });

            // Daily attendance rate
            $dailyAttendance = (clone $baseQuery)
                ->select(
                    DB::raw('DATE(date) as attendance_date'),
                    DB::raw('AVG(status) * 100 as attendance_rate'),
                    DB::raw('COUNT(*) as total_records')
                )
                ->groupBy(DB::raw('DATE(date)'))
                ->orderBy('attendance_date', 'asc')
                ->get();
            
            // Course-wise attendance
            $courseAttendance = (clone $baseQuery)
                ->select('course_id',
                    DB::raw('AVG(status) * 100 as attendance_rate'),
                    DB::raw('COUNT(*) as total_records')
                )
                ->groupBy('course_id')
                ->with('course')
                ->get()
                ->map(function ($item) {
                    return [
                        'course_name' => $item->course->course_name ?? 'N/A',
                        'attendance_rate' => round($item->attendance_rate, 1),
                        'total_records' => $item->total_records
                    ];
                })
                ->sortByDesc('attendance_rate')
                ->values();
            
            // Overall statistics
            $overallStats = (clone $baseQuery)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(status) as present'),
                    DB::raw('AVG(status) * 100 as overall_rate')
                )
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'daily_attendance' => $dailyAttendance,
                    'course_attendance' => $courseAttendance,
                    'overall_stats' => $overallStats,
                    'period' => $period,
                    'filters' => [
                        'location' => $location,
                        'course_id' => $courseId,
                        'intake_id' => $intakeId,
                        'module_id' => $moduleId,
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attendance data'
            ], 500);
        }
    }

    /**
     * Get clearance status
     */
    public function getClearanceStatus(Request $request)
    {
        $location = auth()->user()->user_location ?? 'Welisara';
        $location = str_replace('Nebula Institute of Technology – ', '', $location);
        $location = str_replace('Nebula Institute of Technology - ', '', $location);
        
        try {
            $clearanceStats = ClearanceRequest::where('location', $location)
                ->select(
                    'clearance_type',
                    'status',
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('clearance_type', 'status')
                ->get();
            
            // Group by clearance type
            $clearanceByType = [];
            foreach ($clearanceStats as $stat) {
                if (!isset($clearanceByType[$stat->clearance_type])) {
                    $clearanceByType[$stat->clearance_type] = [
                        'pending' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'total' => 0
                    ];
                }
                $clearanceByType[$stat->clearance_type][$stat->status] = $stat->count;
                $clearanceByType[$stat->clearance_type]['total'] += $stat->count;
            }
            
            // Recent clearance requests
            $recentRequests = ClearanceRequest::where('location', $location)
                ->with(['student', 'course', 'intake'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'student_name' => $request->student->full_name ?? 'N/A',
                        'clearance_type' => $request->clearance_type,
                        'course_name' => $request->course->course_name ?? 'N/A',
                        'batch' => $request->intake->batch ?? 'N/A',
                        'status' => $request->status,
                        'requested_at' => $request->created_at->format('Y-m-d H:i:s'),
                        'remarks' => $request->remarks
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'clearance_by_type' => $clearanceByType,
                    'recent_requests' => $recentRequests
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load clearance data'
            ], 500);
        }
    }

    /**
     * Get payment overview
     */
    public function getPaymentOverview(Request $request)
{
    $location = auth()->user()->user_location ?? 'Welisara';
    $location = str_replace('Nebula Institute of Technology – ', '', $location);
    $location = str_replace('Nebula Institute of Technology - ', '', $location);

    try {
        // Total revenue from payments (through course registration location)
        $totalRevenue = PaymentDetail::whereHas('registration', function ($query) use ($location) {
                $query->where('location', $location);
            })
            ->where('status', 'paid')
            ->sum('amount');

        // Pending payments count
        $pendingPayments = PaymentDetail::whereHas('registration', function ($query) use ($location) {
                $query->where('location', $location);
            })
            ->where('status', 'pending')
            ->count();

        // Monthly revenue for last 12 months
        $monthlyRevenue = PaymentDetail::whereHas('registration', function ($query) use ($location) {
                $query->where('location', $location);
            })
            ->where('status', 'paid')
            ->selectRaw('DATE_FORMAT(payment_effective_date, "%b %Y") as month, SUM(amount) as revenue')
            ->groupByRaw('DATE_FORMAT(payment_effective_date, "%b %Y")')
            ->orderByRaw('payment_effective_date DESC')
            ->limit(12)
            ->get()
            ->reverse()
            ->map(function ($item) {
                return [
                    'month' => $item->month ?? 'N/A',
                    'revenue' => (float) ($item->revenue ?? 0)
                ];
            })
            ->values()
            ->toArray();

        // Payment statistics by status
        $paymentStats = PaymentDetail::whereHas('registration', function ($query) use ($location) {
                $query->where('location', $location);
            })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status ?? 'unknown',
                    'count' => (int) ($item->count ?? 0)
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => (float) ($totalRevenue ?? 0),
                'pending_payments' => (int) ($pendingPayments ?? 0),
                'monthly_revenue' => $monthlyRevenue ?? [],
                'payment_stats' => $paymentStats ?? [],
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Payment Overview Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to load payment overview',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Approve registration
     */
    public function approveRegistration(Request $request, $id)
    {
        try {
            $registration = CourseRegistration::findOrFail($id);
            
            // Check if user has permission for this location
            $userLocation = auth()->user()->user_location ?? 'Welisara';
            $userLocation = str_replace('Nebula Institute of Technology – ', '', $userLocation);
            $userLocation = str_replace('Nebula Institute of Technology - ', '', $userLocation);
            
            if ($registration->location !== $userLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to approve registration from this location'
                ], 403);
            }
            
            $registration->status = 'Registered';
            $registration->approval_status = 'Approved by manager';
            $registration->updated_at = now();
            $registration->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Registration approved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve registration'
            ], 500);
        }
    }

    /**
     * Reject registration
     */
    public function rejectRegistration(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        try {
            $registration = CourseRegistration::findOrFail($id);
            
            // Check if user has permission for this location
            $userLocation = auth()->user()->user_location ?? 'Welisara';
            $userLocation = str_replace('Nebula Institute of Technology – ', '', $userLocation);
            $userLocation = str_replace('Nebula Institute of Technology - ', '', $userLocation);
            
            if ($registration->location !== $userLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to reject registration from this location'
                ], 403);
            }
            
            $registration->status = 'Not eligible';
            $registration->approval_status = 'Rejected';
            $registration->remarks = $request->reason;
            $registration->updated_at = now();
            $registration->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Registration rejected successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject registration'
            ], 500);
        }
    }
}