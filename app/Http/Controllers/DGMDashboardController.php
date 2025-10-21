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
        $paymentBaseQuery = PaymentDetail::query();

        if ($location !== 'all') {
            $paymentBaseQuery->whereHas('student', function ($q) use ($location) {
                $q->where('institute_location', $location);
            });
        }

        if ($course !== 'all') {
            $paymentBaseQuery->whereHas('registration.course', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }

        // If month/day filters are provided, keep them to help narrow the set of PaymentDetail rows
        if ($month) {
            $paymentBaseQuery->whereMonth('created_at', $month);
        }
        if ($day) {
            $paymentBaseQuery->whereDay('created_at', $day);
        }

        // Fetch payments (we will inspect their partial_payments JSON dates)
        $payments = $paymentBaseQuery->get();

        // Helper: sum partial payments whose partial entry date falls in $targetYear.
        $sumPartialsForYear = function ($paymentsCollection, $targetYear) {
            $sum = 0;
            foreach ($paymentsCollection as $p) {
                if ($p->partial_payments && is_array($p->partial_payments)) {
                    foreach ($p->partial_payments as $partial) {
                        // Expect partial to have a date field (e.g. 'date' or 'payment_date').
                        // Try common keys and fallback to payment created_at if missing.
                        $partialDate = null;
                        if (!empty($partial['date'])) {
                            $partialDate = $partial['date'];
                        } elseif (!empty($partial['payment_date'])) {
                            $partialDate = $partial['payment_date'];
                        } elseif (!empty($partial['paid_at'])) {
                            $partialDate = $partial['paid_at'];
                        }

                        if ($partialDate) {
                            try {
                                $dt = Carbon::parse($partialDate);
                                if ((int) $dt->format('Y') === (int) $targetYear) {
                                    $sum += floatval($partial['amount'] ?? 0);
                                }
                            } catch (\Exception $ex) {
                                // If parsing fails, skip this partial entry
                                continue;
                            }
                        } else {
                            // If partial has no date, fallback: include it if parent payment created_at year matches
                            if ($p->created_at && (int) $p->created_at->format('Y') === (int) $targetYear) {
                                $sum += floatval($partial['amount'] ?? 0);
                            }
                        }
                    }
                } else {
                    // If there are no partials, maybe the amount field is direct - include by payment created_at year
                    if ($p->created_at && (int) $p->created_at->format('Y') === (int) $targetYear) {
                        $sum += floatval($p->amount ?? 0);
                    }
                }
            }
            return $sum;
        };

        // Yearly Revenue (sum of partials whose partial date is in $year)
        $yearlyRevenue = $sumPartialsForYear($payments, $year);

        // Outstanding Amount - sum of remaining_amount for payments related to selected filters.
        // Keep original logic (outstanding typically stored on the payment record)
        $outstandingQuery = PaymentDetail::query();
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

        // Previous year revenue (sum partials by partial date year = year-1)
        $prevYearRevenue = $sumPartialsForYear($payments, $year - 1);

        $revenueChange = $prevYearRevenue > 0
            ? round((($yearlyRevenue - $prevYearRevenue) / $prevYearRevenue) * 100, 1)
            : 0;

        // Location-wise revenue/outstanding for current and previous year
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        $locationSummary = [];
        foreach ($locations as $loc) {
            // Fetch payments for this location (do not restrict by created_at year)
            $locPayments = PaymentDetail::whereHas('student', fn($q) => $q->where('institute_location', $loc))
                ->when($course !== 'all', fn($q) => $q->whereHas('registration.course', fn($qq) => $qq->where('course_id', $course)))
                ->get();

            // Current year revenue by partial dates
            $currRevenue = $sumPartialsForYear($locPayments, $year);

            // Current outstanding: sum remaining_amount for payments (no partial-date filter)
            $currOutstanding = $locPayments->reduce(function ($carry, $p) {
                return $carry + floatval($p->remaining_amount ?? 0);
            }, 0);

            // Previous year revenue (based on partial dates)
            $prevRevenue = $sumPartialsForYear($locPayments, $year - 1);

            $growth = $prevRevenue > 0 ? round((($currRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

            $locationSummary[] = [
                'location' => $loc,
                'current_year' => number_format($currRevenue, 2),
                'previous_year' => number_format($prevRevenue, 2),
                'growth' => $growth,
                'outstanding' => number_format($currOutstanding, 2),
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

        // determine mode flags
        $compareMode = filter_var($request->input('compare'), FILTER_VALIDATE_BOOLEAN);
        $rangeMode   = filter_var($request->input('range'), FILTER_VALIDATE_BOOLEAN);

        // If compare -> use exactly the two selected years
        // If range -> use full inclusive range between fromYear and toYear
        // Otherwise use single selected year
        if ($compareMode && $fromYear && $toYear) {
            $years = [(int)$fromYear, (int)$toYear];
        } elseif ($rangeMode && $fromYear && $toYear) {
            $start = (int) min($fromYear, $toYear);
            $end   = (int) max($fromYear, $toYear);
            $years = range($start, $end);
        } else {
            $years = $year ? [(int)$year] : [(int) date('Y')];
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
            // build period for this year (respecting month/day from incoming request)
            $base = Carbon::create($y, $month ?: 1, $day ?: 1);
            if ($day) {
                $periodStart = $base->copy()->startOfDay();
                $periodEnd = $base->copy()->endOfDay();
            } elseif ($month) {
                $periodStart = $base->copy()->startOfMonth();
                $periodEnd = $base->copy()->endOfMonth();
            } else {
                $periodStart = $base->copy()->startOfYear();
                $periodEnd = $base->copy()->endOfYear();
            }

            foreach ($locations as $loc) {
                foreach ($courses as $courseName => $courseId) {
                    // fetch payments for location+course (do NOT restrict by payment created_at here;
                    // we'll inspect partial_payments dates)
                    $paymentsQuery = \App\Models\PaymentDetail::whereHas('student', function ($q) use ($loc) {
                        $q->where('institute_location', $loc);
                    })->whereHas('registration.course', function ($q) use ($courseId) {
                        $q->where('course_id', $courseId);
                    });

                    $payments = $paymentsQuery->get();

                    $revenue = 0.0;
                    foreach ($payments as $payment) {
                        if (!empty($payment->partial_payments) && is_array($payment->partial_payments)) {
                            foreach ($payment->partial_payments as $partial) {
                                $partialDate = $partial['date'] ?? $partial['payment_date'] ?? $partial['paid_at'] ?? null;
                                if ($partialDate) {
                                    try {
                                        $dt = Carbon::parse($partialDate);
                                    } catch (\Exception $ex) {
                                        continue;
                                    }
                                    if ($dt->between($periodStart, $periodEnd)) {
                                        $revenue += floatval($partial['amount'] ?? 0);
                                    }
                                } else {
                                    // fallback: if partial has no date, treat parent payment created_at as its date
                                    if ($payment->created_at && $payment->created_at->between($periodStart, $periodEnd)) {
                                        $revenue += floatval($partial['amount'] ?? 0);
                                    }
                                }
                            }
                        } else {
                            // no partials: include whole payment if payment created_at falls inside period
                            if ($payment->created_at && $payment->created_at->between($periodStart, $periodEnd)) {
                                $revenue += floatval($payment->amount ?? $payment->total_amount ?? 0);
                            }
                        }
                    }

                    $data[] = [
                        'year' => (int) $y,
                        'location' => $loc,
                        'course_name' => $courseName,
                        'revenue' => round($revenue, 2)
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

                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                if ($day) {
                    $query->whereDay('created_at', $day);
                }

                $payments = $query->get();

                $outstanding = 0.0;
                foreach ($payments as $payment) {
                    // Prefer stored remaining_amount when available
                    if (isset($payment->remaining_amount) && $payment->remaining_amount !== null) {
                        $rem = floatval($payment->remaining_amount);
                    } else {
                        // Fallback: compute as total (or amount) minus sum of all partial payments (ignore partial dates)
                        $total = floatval($payment->total_amount ?? $payment->amount ?? 0);
                        $paid = 0.0;
                        if (!empty($payment->partial_payments) && is_array($payment->partial_payments)) {
                            foreach ($payment->partial_payments as $partial) {
                                $paid += floatval($partial['amount'] ?? 0);
                            }
                        }
                        $rem = $total - $paid;
                    }

                    // Avoid negative outstanding values
                    $outstanding += max(0, $rem);
                }

                $data[] = [
                    'year' => $y,
                    'location' => $loc,
                    'outstanding' => round($outstanding, 2)
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