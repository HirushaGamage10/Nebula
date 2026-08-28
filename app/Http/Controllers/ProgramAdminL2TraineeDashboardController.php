<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\ModuleManagement;
use App\Models\Timetable;
use Carbon\Carbon;

class ProgramAdminL2TraineeDashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        
        return view('dashboards.program_admin_l2_trainee', compact('user'));
    }

    // Get overview metrics
    public function getOverviewMetrics()
    {
        $totalStudents = Student::count();
        $activeIntakes = Intake::count();
        $todayAttendance = Attendance::whereDate('date', Carbon::today())->count();
        $pendingResults = ExamResult::where(function($q) {
            $q->whereNull('marks')->orWhereNull('grade');
        })->count();

        return response()->json([
            'total_students' => $totalStudents,
            'active_intakes' => $activeIntakes,
            'today_attendance' => $todayAttendance,
            'pending_results' => $pendingResults
        ]);
    }

    // Get active semesters
    public function getActiveSemesters()
    {
        $activeSemesters = DB::table('semesters')
            ->select('id as semester_id', 'id', 'name as semester_name', 'name', 'course_id', 'start_date', 'end_date')
            ->get();

        return response()->json($activeSemesters);
    }

    // Get attendance overview
    public function getAttendanceOverview()
    {
        $attendanceData = Attendance::selectRaw('DATE(date) as date, COUNT(*) as count')
            ->whereMonth('date', Carbon::now()->month)
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($attendanceData);
    }

    // Get academic performance
    public function getAcademicPerformance()
    {
        $performanceData = ExamResult::selectRaw('COALESCE(grade, "Unknown") as grade, COUNT(*) as count')
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->get();

        return response()->json($performanceData);
    }

    // Get recent activities
    public function getRecentActivities()
    {
        // Recent attendance entries
        $recentAttendance = Attendance::with(['student', 'module'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($attendance) {
                return [
                    'type' => 'attendance',
                    'description' => 'Attendance marked for ' . ($attendance->student->full_name ?? 'Student #' . $attendance->student_id),
                    'time' => optional($attendance->created_at)->diffForHumans() ?? 'Recently'
                ];
            });

        // Recent exam results
        $recentResults = ExamResult::with(['student', 'module'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($result) {
                return [
                    'type' => 'result',
                    'description' => 'Result added for ' . ($result->student->full_name ?? 'Student #' . $result->student_id),
                    'time' => optional($result->created_at)->diffForHumans() ?? 'Recently'
                ];
            });

        $activities = $recentAttendance->concat($recentResults)->sortByDesc('time')->take(10)->values();

        return response()->json($activities);
    }
}
