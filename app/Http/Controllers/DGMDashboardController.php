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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
            if ($request->input('year') === 'all') {
                $year = 'all';
            } else {
                $year = date('Y');
            }
        } else {
            $year = (int) $year;
        }

        $month = $request->input('month');
        $day = $request->input('date');
        $location = $request->input('location', 'all');
        $course = $request->input('course', 'all');

        // Accept multiple possible parameter names for start/end and use Request::boolean for flags
        $fromYear = $request->input('from_year') ?? $request->input('range_start_year') ?? $request->input('from') ?? null;
        $toYear   = $request->input('to_year')   ?? $request->input('range_end_year')   ?? $request->input('to')   ?? null;

        $compareMode = $request->boolean('compare');
        $rangeMode   = $request->boolean('range');

        // normalize numeric strings to ints when present
        $fromYearInt = $fromYear !== null && is_numeric($fromYear) ? (int)$fromYear : null;
        $toYearInt   = $toYear !== null && is_numeric($toYear)   ? (int)$toYear   : null;

        // determine years list (inclusive)
        if ($rangeMode && $fromYearInt && $toYearInt) {
            $start = min($fromYearInt, $toYearInt);
            $end = max($fromYearInt, $toYearInt);
            $years = range($start, $end);
        } elseif ($compareMode && $fromYearInt && $toYearInt) {
            // compare: include exactly the two years for side-by-side comparison
            $years = [$fromYearInt, $toYearInt];
        } elseif ($year === 'all') {
            $bulkMin = \DB::table('bulk_student_uploads')->min('year');
            $bulkMax = \DB::table('bulk_student_uploads')->max('year');
            $regMin = CourseRegistration::min(DB::raw('YEAR(created_at)'));
            $regMax = CourseRegistration::max(DB::raw('YEAR(created_at)'));

            $candidates = array_filter([
                $bulkMin ? (int)$bulkMin : null,
                $bulkMax ? (int)$bulkMax : null,
                $regMin ? (int)$regMin : null,
                $regMax ? (int)$regMax : null,
            ]);

            if (empty($candidates)) {
                $years = [(int) date('Y')];
            } else {
                $min = min($candidates);
                $max = max($candidates);
                $years = range($min, $max);
            }
        } else {
            $years = [$year ?: (int) date('Y')];
        }

        $locations = $location === 'all' ? ['Welisara', 'Moratuwa', 'Peradeniya'] : [$location];

        $aggregate = [];

        // Resolve possible course name if course is numeric id (bulk table may store names)
        $courseNameForMatch = null;
        if ($course !== 'all' && is_numeric($course)) {
            $courseNameForMatch = Course::where('course_id', $course)->value('course_name');
        }

        // 1) bulk rows
        $bulkQuery = \DB::table('bulk_student_uploads')
            ->whereIn('year', $years)
            ->whereIn('location', $locations);

        if ($course !== 'all') {
            $bulkQuery->where(function ($q) use ($course, $courseNameForMatch) {
                $q->where('course', $course);
                if ($courseNameForMatch) {
                    $q->orWhere('course', $courseNameForMatch);
                }
            });
        }

        $bulkRows = $bulkQuery->get();

        foreach ($bulkRows as $row) {
            $c = $row->course ?? ($course !== 'all' ? $course : 'all');
            if (empty($c)) $c = 'all';
            $key = "{$row->year}|{$row->location}|{$c}";
            if (!isset($aggregate[$key])) {
                $aggregate[$key] = [
                    'year' => (int) $row->year,
                    'institute_location' => $row->location,
                    'course' => $c,
                    'count' => 0
                ];
            }
            $aggregate[$key]['count'] += (int) ($row->student_count ?? 0);
        }

        // 2) registrations
        foreach ($years as $y) {
            foreach ($locations as $loc) {
                $query = Student::where('institute_location', $loc)
                    ->whereHas('courseRegistrations', function ($q) use ($y, $month, $day, $course) {
                        $q->whereYear('created_at', $y);

                        if (!empty($month)) {
                            $q->whereMonth('created_at', $month);
                        }
                        if (!empty($day)) {
                            $q->whereDay('created_at', $day);
                        }

                        if ($course !== 'all') {
                            $q->where('course_id', $course);
                        }
                    });

                $count = $query->distinct()->count('students.student_id');

                $c = $course !== 'all' ? $course : 'all';
                $key = "{$y}|{$loc}|{$c}";

                if (!isset($aggregate[$key])) {
                    $aggregate[$key] = [
                        'year' => (int) $y,
                        'institute_location' => $loc,
                        'course' => $c,
                        'count' => 0
                    ];
                }
                $aggregate[$key]['count'] += (int) $count;
            }
        }

        $data = array_values($aggregate);

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
        $filename = 'student_bulk_template.xlsx';
        $path = 'templates/student_bulk_template.xlsx';

        if (Storage::exists($path)) {
            return Storage::download($path, $filename);
        }

        // Fallback: stream a CSV-compatible template if xlsx missing
        $callback = function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Year', 'Month', 'Day', 'Location', 'Course', 'Student_Count']);
            // include example row
            fputcsv($out, [date('Y'), '', '', 'Welisara', '', 0]);
            fclose($out);
        };

        return response()->streamDownload($callback, 'student_bulk_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function downloadRevenueTemplate()
    {
        $filename = 'revenue_bulk_template.xlsx';
        $path = 'templates/revenue_bulk_template.xlsx';

        if (Storage::exists($path)) {
            return Storage::download($path, $filename);
        }

        $callback = function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Year', 'Month', 'Day', 'Location', 'Course', 'Revenue']);
            fputcsv($out, [date('Y'), '', '', 'Welisara', '', 0.00]);
            fclose($out);
        };

        return response()->streamDownload($callback, 'revenue_bulk_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function bulkStudentUpload(Request $request)
    {
        // allow common Excel/CSV variants and text csv; provide JSON-friendly messages for AJAX
        $rules = [
            'student_excel' => ['required', 'file', 'mimes:xlsx,xls,csv,txt,xlsm', 'max:51200']
        ];
        $messages = [
            'student_excel.required' => 'Please choose a file to upload.',
            'student_excel.file' => 'Uploaded item must be a file.',
            'student_excel.mimes' => 'Allowed file types: xlsx, xls, xlsm, csv, txt.',
            'student_excel.max' => 'File too large (max 50MB).'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('student_excel');
        $inserted = 0;

        try {
            $sheets = Excel::toArray(null, $file);
            if (empty($sheets) || !isset($sheets[0])) {
                throw new \Exception('Uploaded file contains no sheets/rows.');
            }

            $rows = $sheets[0];
            foreach ($rows as $i => $row) {
                if ($i == 0)
                    continue; // skip header
                $year = $row[0] ?? null;
                $location = $row[3] ?? null;
                $count = $row[5] ?? null;
                if (!$year || !$location || !is_numeric($count))
                    continue;

                \DB::table('bulk_student_uploads')->insert([
                    'year' => (int) $year,
                    'month' => $row[1] ?? null,
                    'day' => $row[2] ?? null,
                    'location' => $location,
                    'course' => $row[4] ?? null,
                    'student_count' => (int) $count,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        } catch (\Throwable $e) {
            Log::error('bulkStudentUpload error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Upload failed', 'detail' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'inserted' => $inserted]);
        }

        return back()->with('success', 'Student bulk data uploaded! Inserted: ' . $inserted);
    }

    public function bulkRevenueUpload(Request $request)
    {
        $rules = [
            'revenue_excel' => ['required', 'file', 'mimes:xlsx,xls,csv,txt,xlsm', 'max:51200']
        ];
        $messages = [
            'revenue_excel.required' => 'Please choose a file to upload.',
            'revenue_excel.file' => 'Uploaded item must be a file.',
            'revenue_excel.mimes' => 'Allowed file types: xlsx, xls, xlsm, csv, txt.',
            'revenue_excel.max' => 'File too large (max 50MB).'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('revenue_excel');
        $inserted = 0;

        try {
            $sheets = Excel::toArray(null, $file);
            if (empty($sheets) || !isset($sheets[0])) {
                throw new \Exception('Uploaded file contains no sheets/rows.');
            }

            $rows = $sheets[0];
            foreach ($rows as $i => $row) {
                if ($i == 0)
                    continue;
                $year = $row[0] ?? null;
                $location = $row[3] ?? null;
                $revenue = $row[5] ?? null;
                if (!$year || !$location || !is_numeric($revenue))
                    continue;

                \DB::table('bulk_revenue_uploads')->insert([
                    'year' => (int) $year,
                    'month' => $row[1] ?? null,
                    'day' => $row[2] ?? null,
                    'location' => $location,
                    'course' => $row[4] ?? null,
                    'revenue' => floatval($revenue),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        } catch (\Throwable $e) {
            Log::error('bulkRevenueUpload error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Upload failed', 'detail' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'inserted' => $inserted]);
        }

        return back()->with('success', 'Revenue bulk data uploaded! Inserted: ' . $inserted);
    }

    // New: export stored bulk student uploads as CSV
    public function exportStudentBulkData()
    {
        $rows = \DB::table('bulk_student_uploads')->orderBy('year')->get();
        $filename = 'bulk_students_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Year', 'Month', 'Day', 'Location', 'Course', 'Student_Count', 'Created_At']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->year,
                    $r->month,
                    $r->day,
                    $r->location,
                    $r->course,
                    $r->student_count,
                    $r->created_at
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // New: export stored bulk revenue uploads as CSV
    public function exportRevenueBulkData()
    {
        $rows = \DB::table('bulk_revenue_uploads')->orderBy('year')->get();
        $filename = 'bulk_revenues_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Year', 'Month', 'Day', 'Location', 'Course', 'Revenue', 'Created_At']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->year,
                    $r->month,
                    $r->day,
                    $r->location,
                    $r->course,
                    $r->revenue,
                    $r->created_at
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}