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
        
        return view('dashboards.student_counselor', compact('user'));
    }

    // Get overview metrics
    public function getOverviewMetrics(Request $request)
    {
        $period = $request->input('period', 'week');
        $customDate = $request->input('date');
        $dateRange = $this->getDateRange($period, $customDate);
        $previousRange = $this->getPreviousDateRange($dateRange['start'], $dateRange['end']);

        $totalRegisteredStudents = CourseRegistration::where('status', 'Registered')->count();
        $pendingRegistrations = CourseRegistration::where('status', 'Pending')->count();
        $todayRegistrations = CourseRegistration::whereDate('registration_date', Carbon::today())->count();
        $thisWeekRegistrations = CourseRegistration::whereBetween('registration_date', [
            Carbon::now()->startOfWeek()->toDateString(),
            Carbon::now()->endOfWeek()->toDateString(),
        ])->count();

        $periodRegistrations = CourseRegistration::whereBetween('registration_date', [
            $dateRange['start']->toDateString(),
            $dateRange['end']->toDateString(),
        ])->count();

        $periodPendingRegistrations = CourseRegistration::where('status', 'Pending')
            ->whereBetween('registration_date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString(),
            ])->count();

        $previousPeriodRegistrations = CourseRegistration::whereBetween('registration_date', [
            $previousRange['start']->toDateString(),
            $previousRange['end']->toDateString(),
        ])->count();

        $growthPercentage = 0;
        if ($previousPeriodRegistrations > 0) {
            $growthPercentage = round((($periodRegistrations - $previousPeriodRegistrations) / $previousPeriodRegistrations) * 100, 1);
        } elseif ($periodRegistrations > 0) {
            $growthPercentage = 100;
        }

        return response()->json([
            'total_registered' => $totalRegisteredStudents,
            'pending_registrations' => $pendingRegistrations,
            'today_registrations' => $todayRegistrations,
            'week_registrations' => $thisWeekRegistrations,
            'period_registrations' => $periodRegistrations,
            'period_pending_registrations' => $periodPendingRegistrations,
            'today_growth_percentage' => $growthPercentage,
            'selected_period' => $period,
        ]);
    }

    // Get recent registrations
    public function getRecentRegistrations(Request $request)
    {
        $period = $request->input('period', 'week');
        $customDate = $request->input('date');
        $filter = strtolower($request->input('filter', 'all'));
        $search = trim((string) $request->input('search', ''));
        $dateRange = $this->getDateRange($period, $customDate);

        $query = CourseRegistration::with(['student', 'course', 'intake'])
            ->whereBetween('registration_date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString(),
            ]);

        if ($filter !== 'all') {
            $query->where('status', ucfirst($filter));
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('location', 'like', "%{$search}%")
                    ->orWhere('counselor_name', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($courseQuery) use ($search) {
                        $courseQuery->where('course_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name_with_initials', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('student_id', 'like', "%{$search}%");
                    });
            });
        }

        $recentRegistrations = $query->orderBy('registration_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(25)
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'student_id' => $registration->student_id,
                    'student_name' => $registration->student->name_with_initials ?? $registration->student->full_name ?? 'N/A',
                    'email' => $registration->student->email ?? '',
                    'course_name' => $registration->course->course_name ?? 'N/A',
                    'registration_date' => $registration->registration_date ? Carbon::parse($registration->registration_date)->format('Y-m-d') : 'N/A',
                    'registration_time' => $registration->created_at ? Carbon::parse($registration->created_at)->format('H:i') : '',
                    'status' => $registration->status,
                    'location' => $registration->location ?? 'N/A',
                    'counselor_name' => $registration->counselor_name ?? 'N/A',
                    'marketing_source' => $registration->student->marketing_survey ?? 'Direct'
                ];
            });

        return response()->json($recentRegistrations);
    }

    // Get marketing survey data
    public function getMarketingSurveyData(Request $request)
    {
        $period = $request->input('period', 'week');
        $customDate = $request->input('date');
        $dateRange = $this->getDateRange($period, $customDate);

        $surveyData = Student::join('course_registration', 'students.student_id', '=', 'course_registration.student_id')
            ->select('students.marketing_survey', DB::raw('COUNT(*) as count'))
            ->whereNotNull('students.marketing_survey')
            ->where('students.marketing_survey', '!=', '')
            ->whereBetween('course_registration.registration_date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString(),
            ])
            ->groupBy('students.marketing_survey')
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

    // Get daily registration trend
    public function getDailyRegistrationTrend(Request $request)
    {
        $period = $request->input('period', 'week');
        $customDate = $request->input('date');
        $dateRange = $this->getDateRange($period, $customDate);

        [$groupSql, $labelSql] = match ($period) {
            'quarter' => ['DATE_FORMAT(registration_date, "%Y-%m-01")', 'DATE_FORMAT(registration_date, "%b %Y")'],
            'month' => ['DATE(registration_date)', 'DATE_FORMAT(registration_date, "%d %b")'],
            'today', 'custom' => ['DATE(registration_date)', 'DATE_FORMAT(registration_date, "%d %b")'],
            default => ['DATE(registration_date)', 'DATE_FORMAT(registration_date, "%a %d")'],
        };

        $trendData = CourseRegistration::select(
                DB::raw("{$groupSql} as group_key"),
                DB::raw("{$labelSql} as date"),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('registration_date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString(),
            ])
            ->groupBy('group_key', 'date')
            ->orderBy('group_key', 'asc')
            ->get();

        return response()->json($trendData);
    }

    // Get registrations by location
    public function getRegistrationsByLocation(Request $request)
    {
        $period = $request->input('period', 'week');
        $customDate = $request->input('date');
        $dateRange = $this->getDateRange($period, $customDate);

        $locationData = CourseRegistration::select('location', DB::raw('COUNT(*) as count'))
            ->whereNotNull('location')
            ->whereBetween('registration_date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString(),
            ])
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

    // Get counselor performance data
    public function getCounselorPerformanceData(Request $request)
    {
        $period = $request->input('period', 'week');
        $customDate = $request->input('date');
        $dateRange = $this->getDateRange($period, $customDate);

        $performanceData = CourseRegistration::select('counselor_name', DB::raw('COUNT(*) as student_count'))
            ->whereNotNull('counselor_name')
            ->where('counselor_name', '!=', '')
            ->whereBetween('registration_date', [
                $dateRange['start']->toDateString(),
                $dateRange['end']->toDateString(),
            ])
            ->groupBy('counselor_name')
            ->orderBy('student_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json($performanceData);
    }

    private function getDateRange(string $period = 'week', ?string $customDate = null): array
    {
        return match ($period) {
            'today' => [
                'start' => Carbon::today()->startOfDay(),
                'end' => Carbon::today()->endOfDay(),
            ],
            'month' => [
                'start' => Carbon::now()->startOfMonth()->startOfDay(),
                'end' => Carbon::now()->endOfMonth()->endOfDay(),
            ],
            'quarter' => [
                'start' => Carbon::now()->subMonths(2)->startOfMonth()->startOfDay(),
                'end' => Carbon::now()->endOfDay(),
            ],
            'custom' => [
                'start' => $customDate ? Carbon::parse($customDate)->startOfDay() : Carbon::today()->startOfDay(),
                'end' => $customDate ? Carbon::parse($customDate)->endOfDay() : Carbon::today()->endOfDay(),
            ],
            default => [
                'start' => Carbon::now()->startOfWeek()->startOfDay(),
                'end' => Carbon::now()->endOfWeek()->endOfDay(),
            ],
        };
    }

    private function getPreviousDateRange(Carbon $start, Carbon $end): array
    {
        $days = max(1, $start->copy()->startOfDay()->diffInDays($end->copy()->endOfDay()) + 1);

        return [
            'start' => $start->copy()->subDays($days)->startOfDay(),
            'end' => $start->copy()->subDay()->endOfDay(),
        ];
    }
}