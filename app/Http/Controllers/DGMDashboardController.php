<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\PaymentDetail;
use App\Models\PaymentInstallment;
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

        // Calculate Revenue from both sources for current year
        // 1. From bulk_revenue_uploads
        $bulkRevenueQuery = DB::table('bulk_revenue_uploads')
            ->where('year', $year);

        if ($location !== 'all') {
            $bulkRevenueQuery->where('location', $location);
        }
        if ($course !== 'all') {
            $bulkRevenueQuery->where('course', $course);
        }
        if ($month) {
            $bulkRevenueQuery->where('month', $month);
        }
        if ($day) {
            $bulkRevenueQuery->where('day', $day);
        }

        $bulkRevenue = $bulkRevenueQuery->sum('revenue');

        // 2. From payment_details (partial payments)
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

        $payments = $paymentBaseQuery->get();
        $partialPaymentsRevenue = 0;

        foreach ($payments as $payment) {
            if (!empty($payment->partial_payments) && is_array($payment->partial_payments)) {
                foreach ($payment->partial_payments as $partial) {
                    $paymentDate = Carbon::parse($partial['date'] ?? $partial['payment_date'] ?? $partial['paid_at'] ?? $payment->created_at);
                    if ($paymentDate->year == $year) {
                        if (!$month || $paymentDate->month == $month) {
                            if (!$day || $paymentDate->day == $day) {
                                $partialPaymentsRevenue += floatval($partial['amount'] ?? 0);
                            }
                        }
                    }
                }
            }
        }

        // Total current year revenue
        $yearlyRevenue = $bulkRevenue + $partialPaymentsRevenue;

        // Calculate Previous Year Revenue
        $prevYear = $year - 1;

        // 1. Previous year bulk revenue
        $prevBulkRevenue = DB::table('bulk_revenue_uploads')
            ->where('year', $prevYear)
            ->when($location !== 'all', fn($q) => $q->where('location', $location))
            ->when($course !== 'all', fn($q) => $q->where('course', $course))
            ->when($month, fn($q) => $q->where('month', $month))
            ->when($day, fn($q) => $q->where('day', $day))
            ->sum('revenue');

        // 2. Previous year partial payments
        $prevPartialPaymentsRevenue = 0;
        foreach ($payments as $payment) {
            if (!empty($payment->partial_payments) && is_array($payment->partial_payments)) {
                foreach ($payment->partial_payments as $partial) {
                    $paymentDate = Carbon::parse($partial['date'] ?? $partial['payment_date'] ?? $partial['paid_at'] ?? $payment->created_at);
                    if ($paymentDate->year == $prevYear) {
                        if (!$month || $paymentDate->month == $month) {
                            if (!$day || $paymentDate->day == $day) {
                                $prevPartialPaymentsRevenue += floatval($partial['amount'] ?? 0);
                            }
                        }
                    }
                }
            }
        }

        $prevYearRevenue = $prevBulkRevenue + $prevPartialPaymentsRevenue;

        // Calculate revenue change percentage
        $revenueChange = $prevYearRevenue > 0
            ? round((($yearlyRevenue - $prevYearRevenue) / $prevYearRevenue) * 100, 1)
            : 0;

        // Outstanding Amount calculations remain the same
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

        $outstandingCurrentYear = 0.0;
        try {
            // total scheduled for this year (sum of installment amounts whose due_date is in the target year)
            $pendingCurrentYear = PaymentInstallment::when($year, fn($q) => $q->whereYear('due_date', $year))
                ->sum('final_amount');

            $outstandingCurrentYear = $pendingCurrentYear - $partialPaymentsRevenue;

            // sum of partial payments that actually happened in the same year

        } catch (\Throwable $ex) {
            Log::warning('Could not compute outstandingCurrentYear: ' . $ex->getMessage());
            $outstandingCurrentYear = 0.0;
        }

        // Location Summary with both revenue sources
        $locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
        $locationSummary = [];

        foreach ($locations as $loc) {
            // Current year bulk revenue
            $currBulkRev = DB::table('bulk_revenue_uploads')
                ->where('year', $year)
                ->where('location', $loc)
                ->when($course !== 'all', fn($q) => $q->where('course', $course))
                ->sum('revenue');

            // Current year partial payments
            $currPartialRev = 0;
            $locPayments = PaymentDetail::whereHas('student', fn($q) => $q->where('institute_location', $loc))
                ->when($course !== 'all', fn($q) => $q->whereHas('registration.course', fn($qq) => $qq->where('course_id', $course)))
                ->get();

            foreach ($locPayments as $p) {
                if (!empty($p->partial_payments) && is_array($p->partial_payments)) {
                    foreach ($p->partial_payments as $partial) {
                        $paymentDate = Carbon::parse($partial['date'] ?? $partial['payment_date'] ?? $partial['paid_at'] ?? $p->created_at);
                        if ($paymentDate->year == $year) {
                            if (!$month || $paymentDate->month == $month) {
                                if (!$day || $paymentDate->day == $day) {
                                    $currPartialRev += floatval($partial['amount'] ?? 0);
                                }
                            }
                        }
                    }
                }
            }

            // Previous year calculations
            $prevBulkRev = DB::table('bulk_revenue_uploads')
                ->where('year', $prevYear)
                ->where('location', $loc)
                ->when($course !== 'all', fn($q) => $q->where('course', $course))
                ->sum('revenue');

            $prevPartialRev = 0;
            foreach ($locPayments as $p) {
                if (!empty($p->partial_payments) && is_array($p->partial_payments)) {
                    foreach ($p->partial_payments as $partial) {
                        $paymentDate = Carbon::parse($partial['date'] ?? $partial['payment_date'] ?? $partial['paid_at'] ?? $p->created_at);
                        if ($paymentDate->year == $prevYear) {
                            if (!$month || $paymentDate->month == $month) {
                                if (!$day || $paymentDate->day == $day) {
                                    $prevPartialRev += floatval($partial['amount'] ?? 0);
                                }
                            }
                        }
                    }
                }
            }

            $currTotal = $currBulkRev + $currPartialRev;
            $prevTotal = $prevBulkRev + $prevPartialRev;
            $growth = $prevTotal > 0 ? round((($currTotal - $prevTotal) / $prevTotal) * 100, 1) : 0;

            $locationSummary[] = [
                'location' => $loc,
                'current_year' => number_format($currTotal, 2),
                'previous_year' => number_format($prevTotal, 2),
                'growth' => $growth,
                'outstanding' => number_format($locPayments->sum('remaining_amount'), 2),
            ];
        }

        return response()->json([
            'totalStudents' => $totalStudents,
            'yearlyRevenue' => number_format($yearlyRevenue, 2),
            'outstanding' => number_format($outstanding, 2),
            'outstandingCurrentYear' => number_format($outstandingCurrentYear, 2),
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

        $locationsArray = [];
        if ($location !== 'all' && !empty($location)) {
            $locationsArray = array_filter(array_map('trim', explode(',', $location)));
        }

        // Accept multiple possible parameter names for start/end and use Request::boolean for flags
        $fromYear = $request->input('from_year') ?? $request->input('range_start_year') ?? $request->input('from') ?? null;
        $toYear = $request->input('to_year') ?? $request->input('range_end_year') ?? $request->input('to') ?? null;

        $compareMode = $request->boolean('compare');
        $rangeMode = $request->boolean('range');

        // normalize numeric strings to ints when present
        $fromYearInt = $fromYear !== null && is_numeric($fromYear) ? (int) $fromYear : null;
        $toYearInt = $toYear !== null && is_numeric($toYear) ? (int) $toYear : null;

        $coursesSelected = [];
        $courseIds = [];
        $courseNames = [];

        if ($course !== 'all' && !empty($course)) {
            $coursesSelected = array_values(array_filter(array_map('trim', explode(',', $course))));
            foreach ($coursesSelected as $c) {
                if (is_numeric($c)) {
                    $courseIds[] = (int) $c;
                } else {
                    $courseNames[] = $c;
                }
            }

            // Resolve any numeric ids to names and merge
            if (!empty($courseIds)) {
                $resolved = Course::whereIn('course_id', $courseIds)->pluck('course_name', 'course_id')->toArray();
                foreach ($resolved as $id => $name) {
                    if (!in_array($name, $courseNames, true)) {
                        $courseNames[] = $name;
                    }
                }
            }
        }

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
                $bulkMin ? (int) $bulkMin : null,
                $bulkMax ? (int) $bulkMax : null,
                $regMin ? (int) $regMin : null,
                $regMax ? (int) $regMax : null,
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

        $locations = empty($locationsArray) ? ['Welisara', 'Moratuwa', 'Peradeniya'] : $locationsArray;

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
            // Support multi-select: match stored id or stored name
            $bulkQuery->where(function ($q) use ($course, $courseNameForMatch, $courseIds, $courseNames) {
                // if we have numeric ids in the filter, match those
                if (!empty($courseIds)) {
                    $q->whereIn('course', $courseIds);
                }
                // if we have name filters, match those too
                if (!empty($courseNames)) {
                    $q->orWhereIn('course', $courseNames);
                }
                // keep backwards compatibility with single-course string value
                $q->orWhere('course', $course);
                if ($courseNameForMatch) {
                    $q->orWhere('course', $courseNameForMatch);
                }
            });
        }

        $bulkRows = $bulkQuery->get();

        foreach ($bulkRows as $row) {
            $c = $row->course ?? ($course !== 'all' ? $course : 'all');
            if (empty($c))
                $c = 'all';
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
                // build list of courses to iterate (id + name)
                $courseLoop = [];

                if ($course === 'all') {
                    $allCourses = Course::select('course_id', 'course_name')->get();
                    foreach ($allCourses as $cObj) {
                        $courseLoop[] = ['id' => $cObj->course_id, 'name' => $cObj->course_name];
                    }
                } else {
                    // prefer numeric ids if provided
                    if (!empty($courseIds)) {
                        $rows = Course::whereIn('course_id', $courseIds)->get();
                        foreach ($rows as $r) {
                            $courseLoop[] = ['id' => $r->course_id, 'name' => $r->course_name];
                        }
                    }
                    // also accept course names from multi-select
                    if (!empty($courseNames)) {
                        $rows = Course::whereIn('course_name', $courseNames)->get();
                        foreach ($rows as $r) {
                            $exists = false;
                            foreach ($courseLoop as $cl) {
                                if ($cl['id'] == $r->course_id) {
                                    $exists = true;
                                    break;
                                }
                            }
                            if (!$exists) {
                                $courseLoop[] = ['id' => $r->course_id, 'name' => $r->course_name];
                            }
                        }
                    }
                    // fallback: if nothing resolved, attempt to treat $course as single id/name
                    if (empty($courseLoop)) {
                        $singleRows = Course::where('course_id', $course)->orWhere('course_name', $course)->get();
                        foreach ($singleRows as $r) {
                            $courseLoop[] = ['id' => $r->course_id, 'name' => $r->course_name];
                        }
                    }
                }

                // iterate each course and count registrations matching year/month/day
                foreach ($courseLoop as $cInfo) {
                    $courseId = $cInfo['id'];
                    $courseName = $cInfo['name'];

                    $regQuery = Student::where('institute_location', $loc)
                        ->whereHas('courseRegistrations', function ($q) use ($y, $month, $day, $courseId) {
                            $q->where('course_id', $courseId)
                                ->whereYear('created_at', $y);
                            if (!empty($month)) {
                                $q->whereMonth('created_at', $month);
                            }
                            if (!empty($day)) {
                                $q->whereDay('created_at', $day);
                            }
                        });

                    $count = $regQuery->distinct()->count('students.student_id');

                    $key = "{$y}|{$loc}|{$courseName}";
                    if (!isset($aggregate[$key])) {
                        $aggregate[$key] = [
                            'year' => (int) $y,
                            'institute_location' => $loc,
                            'course_name' => $courseName,
                            'count' => 0
                        ];
                    }
                    $aggregate[$key]['count'] += (int) $count;
                }
            }
        }

        $data = array_values($aggregate);

        // Normalize keys for frontend: ensure 'course_name' and 'institute_location' exist and are readable
        $courseIdToName = Course::pluck('course_name', 'course_id')->toArray();
        foreach ($data as &$item) {
            // normalize course value (bulk uses 'course', registrations used numeric id or 'course')
            $rawCourse = $item['course'] ?? $item['course_name'] ?? null;
            if ($rawCourse === null || $rawCourse === '') {
                $courseNameOut = 'all';
            } elseif (is_numeric($rawCourse)) {
                $courseNameOut = $courseIdToName[intval($rawCourse)] ?? (string) $rawCourse;
            } else {
                $courseNameOut = (string) $rawCourse;
            }
            $item['course_name'] = $courseNameOut;

            // ensure frontend key exists for location
            if (!isset($item['institute_location']) && isset($item['location'])) {
                $item['institute_location'] = $item['location'];
            }
        }
        unset($item);

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

        $locationsArray = [];
        if ($location !== 'all' && !empty($location)) {
            $locationsArray = array_filter(array_map('trim', explode(',', $location)));
        }

        // Accept multiple possible names for from/to and range flags
        $fromYear = $request->input('from_year') ?? $request->input('range_start_year') ?? $request->input('from');
        $toYear = $request->input('to_year') ?? $request->input('range_end_year') ?? $request->input('to');

        $compareMode = $request->boolean('compare');
        $rangeMode = $request->boolean('range');

        $courseIds = [];
        if ($course !== 'all' && !empty($course)) {
            $courseIds = array_filter(explode(',', $course));
            $courseIds = array_map('intval', $courseIds);
        }

        // normalize years
        $fromInt = is_numeric($fromYear) ? (int) $fromYear : null;
        $toInt = is_numeric($toYear) ? (int) $toYear : null;

        if ($request->filled('range_start_year') && $request->filled('range_end_year')) {
            $start = min((int) $request->input('range_start_year'), (int) $request->input('range_end_year'));
            $end = max((int) $request->input('range_start_year'), (int) $request->input('range_end_year'));
            $years = range($start, $end);
        } elseif ($request->filled('from_year') && $request->filled('to_year')) {
            if ($compareMode) {
                $years = [(int) $request->input('from_year'), (int) $request->input('to_year')];
            } else {
                $start = min((int) $request->input('from_year'), (int) $request->input('to_year'));
                $end = max((int) $request->input('from_year'), (int) $request->input('to_year'));
                $years = range($start, $end);
            }
        } elseif ($rangeMode && $fromInt && $toInt) {
            $start = min($fromInt, $toInt);
            $end = max($fromInt, $toInt);
            $years = range($start, $end);
        } elseif ($compareMode && $fromInt && $toInt) {
            $years = [$fromInt, $toInt];
        } elseif (!empty($year) && is_numeric($year)) {
            $years = [(int) $year];
        } else {
            $years = [date('Y')];
        }

        $locations = empty($locationsArray) ? ['Welisara', 'Moratuwa', 'Peradeniya'] : $locationsArray;

        // Build courses list to iterate (key => id) where key is course_name, value is course_id
        if ($course === 'all') {
            $courses = Course::pluck('course_id', 'course_name')->toArray();
        } else {
            // If multiple courses passed, fetch all of them
            $coursesQuery = Course::query();
            if (!empty($courseIds)) {
                $coursesQuery->whereIn('course_id', $courseIds);
            } else {
                $coursesQuery->where('course_id', $course);
            }
            $courses = $coursesQuery->pluck('course_id', 'course_name')->toArray();
        }

        $aggregate = [];

        // Pre-resolve numeric course id -> name mapping for bulk matching
        $courseIdToName = Course::pluck('course_name', 'course_id')->toArray();

        foreach ($years as $y) {
            // Build period bounds for this year
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
                // --- 1) Bulk revenue rows for this year/location (and optional month/day/course) ---
                $bulkQ = DB::table('bulk_revenue_uploads')
                    ->where('year', $y)
                    ->where('location', $loc);

                if ($month) {
                    // incoming month may be "01" or "1" — cast to int for comparison
                    $bulkQ->where('month', intval($month));
                }
                if ($day) {
                    $bulkQ->where('day', intval($day));
                }

                // If frontend requested specific course, match either stored id or stored name
                if ($course !== 'all') {
                    $bulkQ->where(function ($q) use ($course, $courseIdToName) {
                        $q->where('course', $course);
                        // if stored bulk uses course name and we have a mapping, match that too
                        $name = $courseIdToName[$course] ?? null;
                        if ($name)
                            $q->orWhere('course', $name);
                    });
                }

                $bulkRows = $bulkQ->get();

                foreach ($bulkRows as $r) {
                    // Normalize course name for output:
                    $bulkCourseRaw = $r->course;
                    $courseNameOut = null;

                    // If bulk stored course is numeric id -> map to name
                    if (is_numeric($bulkCourseRaw)) {
                        $courseNameOut = $courseIdToName[intval($bulkCourseRaw)] ?? (string) $bulkCourseRaw;
                    } elseif ($bulkCourseRaw) {
                        // if it's a name, keep it
                        $courseNameOut = (string) $bulkCourseRaw;
                    } else {
                        // if no course in bulk row and frontend asked for a specific course, use that name
                        if ($course !== 'all') {
                            $courseNameOut = Course::where('course_id', $course)->value('course_name') ?? (string) $course;
                        } else {
                            $courseNameOut = 'all';
                        }
                    }

                    // If frontend filtered by course but courseNameOut doesn't match the requested course name, skip
                    if ($course !== 'all') {
                        $requestedCourseName = Course::where('course_id', $course)->value('course_name') ?? (string) $course;
                        if ($courseNameOut !== $requestedCourseName && (string) $r->course !== (string) $course) {
                            // not matching either id or name
                            continue;
                        }
                    }

                    $key = "{$y}|{$loc}|{$courseNameOut}";

                    if (!isset($aggregate[$key])) {
                        $aggregate[$key] = [
                            'year' => (int) $y,
                            'location' => $loc,
                            'course_name' => $courseNameOut,
                            'revenue' => 0.0
                        ];
                    }

                    $aggregate[$key]['revenue'] += floatval($r->revenue ?? 0);
                }

                // --- 2) PaymentDetail partials for this year/location/course ---
                foreach ($courses as $courseName => $courseId) {
                    // If a specific course filter was provided, this loop will only contain that course
                    $paymentQ = PaymentDetail::whereHas('student', function ($q) use ($loc) {
                        $q->where('institute_location', $loc);
                    });

                    // If course filter provided, restrict by registration/course
                    if ($course !== 'all') {
                        $paymentQ->whereHas('registration', function ($q) use ($courseId) {
                            $q->where('course_id', $courseId);
                        });
                    } else {
                        // when course = all, but we are iterating courses list we still want payments for that course id
                        $paymentQ->whereHas('registration', function ($q) use ($courseId) {
                            $q->where('course_id', $courseId);
                        });
                    }

                    // We don't restrict payment created_at here because partial_payments have their own dates.
                    $payments = $paymentQ->get();

                    foreach ($payments as $p) {
                        // if partial_payments array exists, iterate and match by partial date
                        if (!empty($p->partial_payments) && is_array($p->partial_payments)) {
                            foreach ($p->partial_payments as $partial) {
                                $partialDateRaw = $partial['date'] ?? $partial['payment_date'] ?? $partial['paid_at'] ?? null;
                                if (!$partialDateRaw) {
                                    // fallback to parent created_at
                                    $partialDate = $p->created_at;
                                } else {
                                    try {
                                        $partialDate = Carbon::parse($partialDateRaw);
                                    } catch (\Exception $ex) {
                                        // skip unparsable dates
                                        continue;
                                    }
                                }

                                if ($partialDate->between($periodStart, $periodEnd)) {
                                    $key = "{$y}|{$loc}|{$courseName}";
                                    if (!isset($aggregate[$key])) {
                                        $aggregate[$key] = [
                                            'year' => (int) $y,
                                            'location' => $loc,
                                            'course_name' => $courseName,
                                            'revenue' => 0.0
                                        ];
                                    }
                                    $aggregate[$key]['revenue'] += floatval($partial['amount'] ?? 0);
                                }
                            }
                        } else {
                            // no partials: treat whole payment as single entry at created_at
                            if ($p->created_at && $p->created_at->between($periodStart, $periodEnd)) {
                                $key = "{$y}|{$loc}|{$courseName}";
                                if (!isset($aggregate[$key])) {
                                    $aggregate[$key] = [
                                        'year' => (int) $y,
                                        'location' => $loc,
                                        'course_name' => $courseName,
                                        'revenue' => 0.0
                                    ];
                                }
                                // amount field fallback: amount / total_amount / 0
                                $amount = floatval($p->amount ?? $p->total_amount ?? 0);
                                $aggregate[$key]['revenue'] += $amount;
                            }
                        }
                    } // end payments loop
                } // end courses loop

            } // end locations
        } // end years

        // Normalize output: ensure revenue rounded, and include entries for combinations with zero if needed
        $result = array_values(array_map(function ($item) {
            $item['revenue'] = round(floatval($item['revenue'] ?? 0), 2);
            return $item;
        }, $aggregate));

        return response()->json($result);
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

        // new: accept course filter (comma separated ids or names)
        $course = $request->input('course', 'all');
        $courseIds = [];
        if ($course !== 'all' && !empty($course)) {
            $parts = array_filter(array_map('trim', explode(',', $course)));
            foreach ($parts as $p) {
                if (is_numeric($p)) {
                    $courseIds[] = (int) $p;
                } else {
                    // attempt to resolve name -> id
                    $id = Course::where('course_name', $p)->value('course_id');
                    if ($id)
                        $courseIds[] = (int) $id;
                }
            }
        }

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

        // If range_start provided but not range_end (e.g. "future"), cap a sensible end
        if ($range && $rangeStart && empty($rangeEnd)) {
            $start = (int) $rangeStart;
            $end = $start + 20; // configurable horizon
            $years = range($start, $end);
        }

        // Get all locations
        $locations = $location === 'all'
            ? ['Welisara', 'Moratuwa', 'Peradeniya']
            : array_map('trim', explode(',', $location));

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

                // apply course filter if provided (restrict payments to those having registration for these course ids)
                if (!empty($courseIds)) {
                    $query->whereHas('registration', function ($q) use ($courseIds) {
                        $q->whereIn('course_id', $courseIds);
                    });
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