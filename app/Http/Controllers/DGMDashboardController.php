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
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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

        $bulk = \DB::table('bulk_student_uploads')
            ->whereIn('year', $years)
            ->whereIn('location', $locations)
            ->when($course !== 'all', fn($q) => $q->where('course', $course))
            ->get();

        foreach ($bulk as $row) {
            $data[] = [
                'year' => $row->year,
                'institute_location' => $row->location,
                'course' => $row->course,
                'count' => $row->student_count
            ];
        }

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
    public function getRevenueByYearCourse(Request $request)
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

        // Get all locations
        $locations = $location === 'all'
            ? ['Welisara', 'Moratuwa', 'Peradeniya']
            : [$location];

        // Get all courses (or filter by selected course)
        $courses = $course === 'all'
            ? \App\Models\Course::pluck('course_id', 'course_name')->toArray()
            : [\App\Models\Course::where('course_id', $course)->value('course_name') => $course];

        $data = [];
        foreach ($years as $y) {
            foreach ($locations as $loc) {
                foreach ($courses as $courseName => $courseId) {
                    $query = \App\Models\PaymentDetail::whereYear('created_at', $y)
                        ->whereHas('student', function ($q) use ($loc) {
                            $q->where('institute_location', $loc);
                        })
                        ->whereHas('registration.course', function ($q) use ($courseId) {
                            $q->where('course_id', $courseId);
                        });

                    if ($month)
                        $query->whereMonth('created_at', $month);
                    if ($day)
                        $query->whereDay('created_at', $day);

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
                        'location' => $loc,
                        'course_name' => $courseName,
                        'revenue' => $revenue
                    ];
                }
            }
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
     * Get outstanding data by year and location
     */
    public function getOutstandingByYearCourse(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $day = $request->input('date');
        $location = $request->input('location', 'all');
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

        // Get all locations
        $locations = $location === 'all'
            ? ['Welisara', 'Moratuwa', 'Peradeniya']
            : [$location];

        $data = [];
        foreach ($years as $y) {
            foreach ($locations as $loc) {
                $query = \App\Models\PaymentDetail::whereYear('created_at', $y)
                    ->whereHas('student', function ($q) use ($loc) {
                        $q->where('institute_location', $loc);
                    });

                if ($month)
                    $query->whereMonth('created_at', $month);
                if ($day)
                    $query->whereDay('created_at', $day);

                $payments = $query->get();
                $outstanding = 0;
                foreach ($payments as $payment) {
                    $total = floatval($payment->total_amount ?? 0);
                    $paid = 0;
                    if ($payment->partial_payments && is_array($payment->partial_payments)) {
                        foreach ($payment->partial_payments as $partial) {
                            $paid += floatval($partial['amount'] ?? 0);
                        }
                    }
                    $outstanding += ($total - $paid);
                }

                $data[] = [
                    'year' => $y,
                    'location' => $loc,
                    'outstanding' => $outstanding
                ];
            }
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

    public function getMarketingData(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Get counts for each marketing_survey type for the current year
        $data = \App\Models\Student::select('marketing_survey', DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', $year)
            ->whereNotNull('marketing_survey')
            ->groupBy('marketing_survey')
            ->get();

        // Format for chart.js
        $labels = $data->pluck('marketing_survey')->toArray();
        $counts = $data->pluck('count')->toArray();

        return response()->json([
            'labels' => $labels,
            'counts' => $counts,
        ]);
    }

    public function downloadStudentTemplate()
    {
        $headers = ['Year', 'Month', 'Day', 'Location', 'Course', 'Student_Count'];
        $filename = 'student_bulk_template.xlsx';
        // Generate Excel file dynamically or serve a static file
        // For demo, serve a static file in storage/app/templates/
        return Storage::download('templates/student_bulk_template.xlsx', $filename);
    }

    public function downloadRevenueTemplate()
    {
        $headers = ['Year', 'Month', 'Day', 'Location', 'Course', 'Revenue'];
        $filename = 'revenue_bulk_template.xlsx';
        return Storage::download('templates/revenue_bulk_template.xlsx', $filename);
    }

    public function bulkStudentUpload(Request $request)
    {
        $file = $request->file('student_excel');
        // Parse Excel (use Laravel Excel or PHPSpreadsheet)
        $rows = Excel::toArray([], $file)[0];
        foreach ($rows as $i => $row) {
            if ($i == 0)
                continue; // skip header
            \DB::table('bulk_student_uploads')->insert([
                'year' => $row[0],
                'month' => $row[1] ?? null,
                'day' => $row[2] ?? null,
                'location' => $row[3],
                'course' => $row[4] ?? null,
                'student_count' => $row[5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'Student bulk data uploaded!');
    }

    public function bulkRevenueUpload(Request $request)
    {
        $file = $request->file('revenue_excel');
        $rows = Excel::toArray([], $file)[0];
        foreach ($rows as $i => $row) {
            if ($i == 0)
                continue;
            \DB::table('bulk_revenue_uploads')->insert([
                'year' => $row[0],
                'month' => $row[1] ?? null,
                'day' => $row[2] ?? null,
                'location' => $row[3],
                'course' => $row[4] ?? null,
                'revenue' => $row[5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'Revenue bulk data uploaded!');
    }
}