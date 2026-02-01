<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Intake;
use Carbon\Carbon;

class StudentCounselorTraineeDashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        
        return view('dashboards.student_counselor_trainee', compact('user'));
    }

    // Get overview metrics
    public function getOverviewMetrics()
    {
        $totalRegistrations = CourseRegistration::count();
        $thisMonthRegistrations = CourseRegistration::whereMonth('registration_date', Carbon::now()->month)
            ->whereYear('registration_date', Carbon::now()->year)
            ->count();
        $todayRegistrations = CourseRegistration::whereDate('registration_date', Carbon::today())->count();
        $activeStudents = Student::where('status', 'active')->count();

        // Calculate growth
        $lastMonthRegistrations = CourseRegistration::whereMonth('registration_date', Carbon::now()->subMonth()->month)
            ->whereYear('registration_date', Carbon::now()->subMonth()->year)
            ->count();
        
        $growthPercentage = 0;
        if ($lastMonthRegistrations > 0) {
            $growthPercentage = (($thisMonthRegistrations - $lastMonthRegistrations) / $lastMonthRegistrations) * 100;
        }

        return response()->json([
            'total_registrations' => $totalRegistrations,
            'this_month' => $thisMonthRegistrations,
            'today' => $todayRegistrations,
            'active_students' => $activeStudents,
            'growth_percentage' => round($growthPercentage, 2)
        ]);
    }

    // Get recent registrations
    public function getRecentRegistrations()
    {
        $registrations = CourseRegistration::with(['student', 'course', 'intake'])
            ->latest('registration_date')
            ->take(10)
            ->get()
            ->map(function($reg) {
                return [
                    'student_name' => $reg->student->full_name ?? 'N/A',
                    'course_name' => $reg->course->course_name ?? 'N/A',
                    'intake' => $reg->intake->batch ?? 'N/A',
                    'registration_date' => $reg->registration_date,
                    'status' => $reg->status
                ];
            });

        return response()->json($registrations);
    }

    // Get marketing survey data
    public function getMarketingSurveyData()
    {
        $surveyData = CourseRegistration::selectRaw('marketing_channel, COUNT(*) as count')
            ->whereNotNull('marketing_channel')
            ->groupBy('marketing_channel')
            ->get()
            ->map(function($item) {
                return [
                    'channel' => $item->marketing_channel,
                    'count' => $item->count
                ];
            });

        return response()->json($surveyData);
    }

    // Get daily registration trend
    public function getDailyRegistrationTrend()
    {
        $trendData = CourseRegistration::selectRaw('DATE(registration_date) as date, COUNT(*) as count')
            ->whereMonth('registration_date', Carbon::now()->month)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($trendData);
    }

    // Get registrations by location
    public function getRegistrationsByLocation()
    {
        $locationData = CourseRegistration::join('intakes', 'course_registrations.intake_id', '=', 'intakes.intake_id')
            ->selectRaw('intakes.location, COUNT(*) as count')
            ->groupBy('intakes.location')
            ->get();

        return response()->json($locationData);
    }

    // Get registrations by course
    public function getRegistrationsByCourse()
    {
        $courseData = CourseRegistration::with('course')
            ->selectRaw('course_id, COUNT(*) as count')
            ->groupBy('course_id')
            ->orderByDesc('count')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'course_name' => $item->course->course_name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        return response()->json($courseData);
    }
}
