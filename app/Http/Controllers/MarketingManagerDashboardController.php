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

class MarketingManagerDashboardController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        
        return view('marketing_manager_dashboard', compact('user'));
    }

    // Get overview metrics
    public function getOverviewMetrics()
    {
        $totalRegisteredStudents = CourseRegistration::where('status', 'Registered')->count();
        $totalStudents = Student::where('status', 'active')->count();
        $thisMonthRegistrations = CourseRegistration::whereMonth('registration_date', Carbon::now()->month)
            ->whereYear('registration_date', Carbon::now()->year)
            ->count();
        $lastMonthRegistrations = CourseRegistration::whereMonth('registration_date', Carbon::now()->subMonth()->month)
            ->whereYear('registration_date', Carbon::now()->subMonth()->year)
            ->count();

        // Calculate growth percentage
        $growthPercentage = 0;
        if ($lastMonthRegistrations > 0) {
            $growthPercentage = (($thisMonthRegistrations - $lastMonthRegistrations) / $lastMonthRegistrations) * 100;
        }

        return response()->json([
            'total_registered' => $totalRegisteredStudents,
            'total_students' => $totalStudents,
            'this_month_registrations' => $thisMonthRegistrations,
            'last_month_registrations' => $lastMonthRegistrations,
            'growth_percentage' => round($growthPercentage, 2)
        ]);
    }

    // Get recent registrations
    public function getRecentRegistrations()
    {
        $recentRegistrations = CourseRegistration::with(['student', 'course', 'intake'])
            ->orderBy('registration_date', 'desc')
            ->take(15)
            ->get()
            ->map(function ($registration) {
                $student = $registration->student;
                return [
                    'id' => $registration->id,
                    'student_name' => $student->full_name ?? 'N/A',
                    'course_name' => $registration->course->course_name ?? 'N/A',
                    'registration_date' => Carbon::parse($registration->registration_date)->format('Y-m-d'),
                    'status' => $registration->status,
                    'location' => $registration->location,
                    'marketing_source' => $student->marketing_survey ?? 'N/A'
                ];
            });

        return response()->json($recentRegistrations);
    }

    // Get marketing survey analysis
    public function getMarketingSurveyAnalysis()
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
        $totalCount = array_sum($flattenedData);
        
        foreach ($flattenedData as $source => $count) {
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100, 2) : 0;
            $chartData[] = [
                'source' => $source,
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        // Sort by count descending
        usort($chartData, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        return response()->json($chartData);
    }

    // Get monthly registration trend (last 12 months)
    public function getMonthlyRegistrationTrend()
    {
        $last12Months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = CourseRegistration::whereMonth('registration_date', $date->month)
                ->whereYear('registration_date', $date->year)
                ->count();
            
            $last12Months[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        return response()->json($last12Months);
    }

    // Get registrations by location
    public function getRegistrationsByLocation()
    {
        $locationData = CourseRegistration::select('location', DB::raw('COUNT(*) as count'))
            ->whereNotNull('location')
            ->groupBy('location')
            ->get()
            ->map(function ($item) {
                return [
                    'location' => $item->location,
                    'count' => $item->count
                ];
            });

        return response()->json($locationData);
    }

    // Get top performing courses
    public function getTopPerformingCourses()
    {
        $courseData = CourseRegistration::with('course')
            ->select('course_id', DB::raw('COUNT(*) as count'))
            ->groupBy('course_id')
            ->orderByDesc('count')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'course_name' => $item->course->course_name ?? 'Unknown',
                    'registrations' => $item->count
                ];
            });

        return response()->json($courseData);
    }

    // Get conversion funnel data
    public function getConversionFunnelData()
    {
        $totalStudents = Student::count();
        $registeredStudents = CourseRegistration::distinct('student_id')->count('student_id');
        $completedPayments = CourseRegistration::where('registration_fee', '>', 0)->count();
        $approvedRegistrations = CourseRegistration::where('status', 'Registered')->count();

        return response()->json([
            'total_inquiries' => $totalStudents,
            'registrations' => $registeredStudents,
            'payments' => $completedPayments,
            'approved' => $approvedRegistrations
        ]);
    }

    // Get marketing ROI by source
    public function getMarketingROIBySource()
    {
        // Get students with marketing survey data and their registrations
        $sourcePerformance = Student::select('students.marketing_survey', 
                DB::raw('COUNT(DISTINCT students.student_id) as student_count'),
                DB::raw('COUNT(course_registration.id) as registration_count'))
            ->leftJoin('course_registration', 'students.student_id', '=', 'course_registration.student_id')
            ->whereNotNull('students.marketing_survey')
            ->where('students.marketing_survey', '!=', '')
            ->groupBy('students.marketing_survey')
            ->get()
            ->map(function ($item) {
                $sources = array_map('trim', explode(',', $item->marketing_survey));
                return [
                    'sources' => $sources,
                    'student_count' => $item->student_count,
                    'registration_count' => $item->registration_count,
                    'conversion_rate' => $item->student_count > 0 ? 
                        round(($item->registration_count / $item->student_count) * 100, 2) : 0
                ];
            });

        // Flatten and aggregate
        $flattenedData = [];
        foreach ($sourcePerformance as $item) {
            foreach ($item['sources'] as $source) {
                if (!isset($flattenedData[$source])) {
                    $flattenedData[$source] = [
                        'source' => $source,
                        'students' => 0,
                        'registrations' => 0
                    ];
                }
                $flattenedData[$source]['students'] += $item['student_count'];
                $flattenedData[$source]['registrations'] += $item['registration_count'];
            }
        }

        // Calculate conversion rates
        $result = array_map(function ($data) {
            $data['conversion_rate'] = $data['students'] > 0 ? 
                round(($data['registrations'] / $data['students']) * 100, 2) : 0;
            return $data;
        }, $flattenedData);

        return response()->json(array_values($result));
    }

    // Get demographic insights
    public function getDemographicInsights()
    {
        $genderDistribution = Student::select('gender', DB::raw('COUNT(*) as count'))
            ->groupBy('gender')
            ->get();

        $ageGroups = Student::select(
                DB::raw('CASE 
                    WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) < 20 THEN "Under 20"
                    WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 20 AND 25 THEN "20-25"
                    WHEN TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN 26 AND 30 THEN "26-30"
                    ELSE "Over 30"
                END as age_group'),
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('birthday')
            ->groupBy('age_group')
            ->get();

        return response()->json([
            'gender_distribution' => $genderDistribution,
            'age_groups' => $ageGroups
        ]);
    }
}