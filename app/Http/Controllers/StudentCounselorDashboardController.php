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

class StudentCounselorDashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        
        return view('student_counselor_dashboard', compact('user'));
    }

    // Get overview metrics
    public function getOverviewMetrics()
    {
        $totalRegisteredStudents = CourseRegistration::where('status', 'Registered')->count();
        $pendingRegistrations = CourseRegistration::where('status', 'Pending')->count();
        $todayRegistrations = CourseRegistration::whereDate('registration_date', Carbon::today())->count();
        $thisWeekRegistrations = CourseRegistration::whereBetween('registration_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        return response()->json([
            'total_registered' => $totalRegisteredStudents,
            'pending_registrations' => $pendingRegistrations,
            'today_registrations' => $todayRegistrations,
            'week_registrations' => $thisWeekRegistrations
        ]);
    }

    // Get recent registrations
    public function getRecentRegistrations()
    {
        $recentRegistrations = CourseRegistration::with(['student', 'course', 'intake'])
            ->orderBy('registration_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'student_name' => $registration->student->full_name ?? 'N/A',
                    'course_name' => $registration->course->course_name ?? 'N/A',
                    'registration_date' => Carbon::parse($registration->registration_date)->format('Y-m-d'),
                    'status' => $registration->status,
                    'location' => $registration->location,
                    'counselor_name' => $registration->counselor_name ?? 'N/A'
                ];
            });

        return response()->json($recentRegistrations);
    }

    // Get marketing survey data
    public function getMarketingSurveyData()
    {
        $surveyData = Student::select('marketing_survey', DB::raw('COUNT(*) as count'))
            ->whereNotNull('marketing_survey')
            ->where('marketing_survey', '!=', '')
            ->groupBy('marketing_survey')
            ->get()
            ->map(function ($item) {
                // Handle multiple sources separated by comma
                $sources = array_map('trim', explode(',', $item->marketing_survey));
                return [
                    'sources' => $sources,
                    'count' => $item->count
                ];
            });

        // Flatten the data to count individual sources
        $flattenedData = [];
        foreach ($surveyData as $item) {
            foreach ($item['sources'] as $source) {
                if (isset($flattenedData[$source])) {
                    $flattenedData[$source] += $item['count'];
                } else {
                    $flattenedData[$source] = $item['count'];
                }
            }
        }

        // Convert to array format for chart
        $chartData = [];
        foreach ($flattenedData as $source => $count) {
            $chartData[] = [
                'source' => $source,
                'count' => $count
            ];
        }

        // Sort by count descending
        usort($chartData, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        return response()->json($chartData);
    }

    // Get daily registration trend (last 30 days)
    public function getDailyRegistrationTrend()
    {
        $last30Days = CourseRegistration::select(
                DB::raw('DATE(registration_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('registration_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($last30Days);
    }

    // Get registrations by location
    public function getRegistrationsByLocation()
    {
        $locationData = CourseRegistration::select('location', DB::raw('COUNT(*) as count'))
            ->whereNotNull('location')
            ->groupBy('location')
            ->get();

        return response()->json($locationData);
    }

    // Get registrations by course
    public function getRegistrationsByCourse()
    {
        $courseData = CourseRegistration::with('course')
            ->select('course_id', DB::raw('COUNT(*) as count'))
            ->groupBy('course_id')
            ->get()
            ->map(function ($item) {
                return [
                    'course_name' => $item->course->course_name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        return response()->json($courseData);
    }

    // Get SLT employee vs non-employee registrations
    public function getSltEmployeeData()
    {
        $sltEmployees = CourseRegistration::where('slt_employee', 1)->count();
        $nonEmployees = CourseRegistration::where('slt_employee', 0)->count();

        return response()->json([
            'slt_employees' => $sltEmployees,
            'non_employees' => $nonEmployees
        ]);
    }

    // Get foundation program enrollment
    public function getFoundationProgramData()
    {
        $withFoundation = Student::where('foundation_program', 1)->count();
        $withoutFoundation = Student::where('foundation_program', 0)->count();

        return response()->json([
            'with_foundation' => $withFoundation,
            'without_foundation' => $withoutFoundation
        ]);
    }
}