<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\PaymentDetail;
use App\Models\Course;
use App\Models\Intake;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DGMDashboardController extends Controller
{
    public function showDashboard()
    {
        return view('dgmdashboard');
    }

    /**
     * Get overview metrics for the dashboard
     */
    public function getOverviewMetrics(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $location = $request->input('location', 'all');
        $course = $request->input('course', 'all');
        $month = $request->input('month');
        $day = $request->input('day');

        // Build date filter
        $dateFilter = $this->buildDateFilter($year, $month, $day);

        // Total Students
        $studentsQuery = Student::query();
        if ($location !== 'all') {
            $studentsQuery->where('institute_location', $location);
        }
        $totalStudents = $studentsQuery->count();

        // Yearly Revenue
        $revenueQuery = PaymentDetail::whereYear('created_at', $year);

        if ($location !== 'all') {
            $revenueQuery->whereHas('student', function ($q) use ($location) {
                $q->where('institute_location', $location);
            });
        }

        if ($course !== 'all') {
            $revenueQuery->whereHas('registration.course', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }

        if ($month) {
            $revenueQuery->whereMonth('created_at', $month);
        }

        if ($day) {
            $revenueQuery->whereDay('created_at', $day);
        }


        // Calculate revenue from partial_payments JSON
        $payments = $revenueQuery->get();
        $yearlyRevenue = 0;
        foreach ($payments as $payment) {
            if ($payment->partial_payments && is_array($payment->partial_payments)) {
                foreach ($payment->partial_payments as $partial) {
                    $yearlyRevenue += floatval($partial['amount'] ?? 0);
                }
            }
        }

        // Outstanding Amount - Sum of total_fee minus revenue
        $outstandingQuery = PaymentDetail::whereYear('created_at', $year);

        if ($location !== 'all') {
            $outstandingQuery->whereHas('student', function ($q) use ($location) {
                $q->where('institute_location', $location);
            });
        }

        if ($course !== 'all') {
            $outstandingQuery->whereHas('registration.course', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }

        $outstanding = $outstandingQuery->sum('remaining_amount');

        // Previous year revenue (partial payments)
        $prevYearPayments = PaymentDetail::whereYear('created_at', $year - 1)->get();
        $prevYearRevenue = 0;
        foreach ($prevYearPayments as $payment) {
            if ($payment->partial_payments && is_array($payment->partial_payments)) {
                foreach ($payment->partial_payments as $partial) {
                    $prevYearRevenue += floatval($partial['amount'] ?? 0);
                }
            }
        }
        $revenueChange = $prevYearRevenue > 0
            ? round((($yearlyRevenue - $prevYearRevenue) / $prevYearRevenue) * 100, 1)
            : 0;

        // Location-wise revenue/outstanding for current and previous year
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        $locationSummary = [];
        foreach ($locations as $loc) {
            // Current year
            $currPayments = PaymentDetail::whereYear('created_at', $year)
                ->whereHas('student', fn($q) => $q->where('institute_location', $loc))
                ->get();
            $currRevenue = 0;
            $currOutstanding = 0;
            foreach ($currPayments as $payment) {
                if ($payment->partial_payments && is_array($payment->partial_payments)) {
                    foreach ($payment->partial_payments as $partial) {
                        $currRevenue += floatval($partial['amount'] ?? 0);
                    }
                }
                $currOutstanding += floatval($payment->remaining_amount ?? 0);
            }

            // Previous year
            $prevPayments = PaymentDetail::whereYear('created_at', $year - 1)
                ->whereHas('student', fn($q) => $q->where('institute_location', $loc))
                ->get();
            $prevRevenue = 0;
            foreach ($prevPayments as $payment) {
                if ($payment->partial_payments && is_array($payment->partial_payments)) {
                    foreach ($payment->partial_payments as $partial) {
                        $prevRevenue += floatval($partial['amount'] ?? 0);
                    }
                }
            }

            $growth = $prevRevenue > 0 ? round((($currRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

            $locationSummary[] = [
                'location' => $loc,
                'current_year' => number_format($currRevenue, 2),
                'previous_year' => number_format($prevRevenue, 2),
                'growth' => $growth,
                'outstanding' => number_format($currOutstanding, 2),
                // 'franchise' => null // Add franchise logic if needed
            ];
        }

        return response()->json([
            'totalStudents' => $totalStudents,
            'yearlyRevenue' => number_format($yearlyRevenue, 2),
            'outstanding' => number_format($outstanding, 2),
            'revenueChange' => $revenueChange >= 0 ? "+{$revenueChange}%" : "{$revenueChange}%",
            'outstandingRatio' => $yearlyRevenue > 0 ? round(($outstanding / ($yearlyRevenue + $outstanding)) * 100) : 0,
            'locationSummary' => $locationSummary
        ]);



    }

    /**
     * Get students data by location and course
     */
    public function getStudentsData(Request $request)
    {
        $year = $request->input('year');
        if (empty($year) || !is_numeric($year)) {
            $year = date('Y');
        }
        $month = $request->input('month');
        $day = $request->input('date');
        $location = $request->input('location', 'all');
        $course = $request->input('course', 'all');
        $fromYear = $request->input('from_year');
        $toYear = $request->input('to_year');

        // If compare/range, use year range
        if ($fromYear && $toYear) {
            $years = range($fromYear, $toYear);
        } else {
            $years = $year ? [$year] : [date('Y')];
        }

        $locations = $location === 'all' ? ['Welisara', 'Moratuwa', 'Peradeniya'] : [$location];

        $data = [];
        foreach ($years as $y) {
            foreach ($locations as $loc) {
                // Count distinct students by their course registrations
                $query = Student::where('institute_location', $loc)
                    ->whereHas('courseRegistrations', function ($q) use ($y, $month, $day, $course) {
                        // Filter by course registration date
                        $q->whereYear('created_at', $y);

                        if (!empty($month)) {
                            $q->whereMonth('created_at', $month);
                        }
                        if (!empty($day)) {
                            $q->whereDay('created_at', $day);
                        }

                        // Filter by specific course if selected
                        if ($course !== 'all') {
                            $q->where('course_id', $course);
                        }
                    });

                // Use distinct to avoid counting same student multiple times
                $count = $query->distinct()->count('students.student_id');

                $data[] = [
                    'year' => $y,
                    'institute_location' => $loc,
                    'course' => $course,
                    'count' => $count
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Get revenue data by year and location
     */
    public function getRevenueData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('date');
        $location = $request->input('location', 'all');
        $course = $request->input('course', 'all');
        $fromYear = $request->input('from_year');
        $toYear = $request->input('to_year');
        $range = $request->input('range');
        $rangeStart = $request->input('range_start_year');
        $rangeEnd = $request->input('range_end_year');

        // Determine years to fetch
        if ($range && $rangeStart && $rangeEnd) {
            $years = range($rangeStart, $rangeEnd);
        } elseif ($fromYear && $toYear) {
            $years = range($fromYear, $toYear);
        } elseif ($year) {
            $years = [$year];
        } else {
            $years = [date('Y')];
        }

        $data = [];
        foreach ($years as $y) {
            $query = PaymentDetail::whereYear('created_at', $y);

            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            if ($day) {
                $query->whereDay('created_at', $day);
            }
            if ($location !== 'all') {
                $query->whereHas('student', function ($q) use ($location) {
                    $q->where('institute_location', $location);
                });
            }
            if ($course !== 'all') {
                $query->whereHas('registration.course', function ($q) use ($course) {
                    $q->where('course_id', $course);
                });
            }

            // Calculate revenue from partial_payments
            $payments = $query->get();
            $revenue = 0;
            foreach ($payments as $payment) {
                if ($payment->partial_payments && is_array($payment->partial_payments)) {
                    foreach ($payment->partial_payments as $partial) {
                        $revenue += floatval($partial['amount'] ?? 0);
                    }
                }
            }

            $data[] = [
                'year' => $y,
                'revenue' => $revenue
            ];
        }

        return response()->json($data);
    }

    /**
     * Get students by location breakdown
     */
    public function getStudentsByLocation(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $data = Student::select('institute_location', DB::raw('count(*) as count'))
            ->whereYear('created_at', $year)
            ->groupBy('institute_location')
            ->get();

        return response()->json($data);
    }

    /**
     * Get future revenue projections
     */
    public function getFutureProjections(Request $request)
    {
        $currentYear = date('Y');
        $quarters = [];

        // Get last 4 quarters actual data
        for ($i = 3; $i >= 0; $i--) {
            $quarter = Carbon::now()->subQuarters($i);
            $revenue = PaymentDetail::where('status', 'paid')
                ->whereBetween('created_at', [
                    $quarter->copy()->firstOfQuarter(),
                    $quarter->copy()->lastOfQuarter()
                ])
                ->sum('amount');

            $quarters[] = [
                'label' => $quarter->format('Y Q') . $quarter->quarter,
                'revenue' => $revenue,
                'type' => 'actual'
            ];
        }

        // Calculate average growth rate
        $revenues = array_column($quarters, 'revenue');
        $avgGrowth = count($revenues) > 1
            ? ($revenues[count($revenues) - 1] - $revenues[0]) / count($revenues)
            : 0;

        // Project next 4 quarters
        $lastRevenue = end($revenues);
        for ($i = 1; $i <= 4; $i++) {
            $projectedRevenue = $lastRevenue + ($avgGrowth * $i);
            $conservativeRevenue = $projectedRevenue * 0.85;

            $quarter = Carbon::now()->addQuarters($i);
            $quarters[] = [
                'label' => $quarter->format('Y Q') . $quarter->quarter,
                'revenue' => $projectedRevenue,
                'conservative' => $conservativeRevenue,
                'type' => 'projected'
            ];
        }

        return response()->json($quarters);
    }

    /**
     * Get revenue by location
     */
    public function getRevenueByLocation(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $course = $request->input('course', 'all');

        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        $data = [];

        foreach ($locations as $location) {
            $query = PaymentDetail::whereYear('created_at', $year)
                ->whereHas('student', function ($q) use ($location) {
                    $q->where('institute_location', $location);
                });

            if ($course !== 'all') {
                $query->whereHas('registration.course', function ($q) use ($course) {
                    $q->where('course_id', $course);
                });
            }

            // Calculate revenue from partial_payments
            $payments = $query->get();
            $revenue = 0;
            foreach ($payments as $payment) {
                if ($payment->partial_payments && is_array($payment->partial_payments)) {
                    foreach ($payment->partial_payments as $partial) {
                        $revenue += floatval($partial['amount'] ?? 0);
                    }
                }
            }

            $data[] = [
                'location' => $location,
                'revenue' => $revenue
            ];
        }

        return response()->json($data);
    }

    /**
     * Helper method to build date filter
     */
    private function buildDateFilter($year, $month = null, $day = null)
    {
        $date = Carbon::create($year, $month ?: 1, $day ?: 1);

        if ($day) {
            return [
                'start' => $date->startOfDay(),
                'end' => $date->endOfDay()
            ];
        } elseif ($month) {
            return [
                'start' => $date->startOfMonth(),
                'end' => $date->endOfMonth()
            ];
        } else {
            return [
                'start' => $date->startOfYear(),
                'end' => $date->endOfYear()
            ];
        }
    }
}