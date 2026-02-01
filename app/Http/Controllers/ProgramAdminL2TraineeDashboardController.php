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
        $totalStudents = Student::where('status', 'active')->count();
        $activeIntakes = Intake::where('status', 'active')->count();
        $todayAttendance = Attendance::whereDate('date', Carbon::today())->count();
        $pendingResults = ExamResult::where('status', 'pending')->count();

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
            ->where('status', 'open')
            ->select('semester_id', 'semester_name', 'course_id', 'start_date', 'end_date')
            ->get();

        return response()->json($activeSemesters);
    }

    // Get attendance overview
    public function getAttendanceOverview()
    {
        $attendanceData = Attendance::selectRaw('DATE(date) as date, COUNT(*) as count')
            ->whereMonth('date', Carbon::now()->month)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($attendanceData);
    }

    // Get academic performance
    public function getAcademicPerformance()
    {
        $performanceData = ExamResult::selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->get();

        return response()->json($performanceData);
    }

    // Get recent activities
    public function getRecentActivities()
    {
        $activities = [];

        // Recent attendance entries
        $recentAttendance = Attendance::with(['student', 'module'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($attendance) {
                return [
                    'type' => 'attendance',
                    'description' => 'Attendance marked for ' . $attendance->student->full_name,
                    'time' => $attendance->created_at->diffForHumans()
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
                    'description' => 'Result added for ' . $result->student->full_name,
                    'time' => $result->created_at->diffForHumans()
                ];
            });

        $activities = $recentAttendance->concat($recentResults)->sortByDesc('time')->take(10);

        return response()->json($activities);
    }
}
