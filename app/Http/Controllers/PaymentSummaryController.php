<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;
use App\Models\StudentPaymentPlan;
use App\Models\SltLoanReceivableRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PaymentSummaryController extends Controller
{
    /**
     * 🔹 Enhanced Global Dashboard with Advanced Metrics
     */
    public function index(Request $request)
    {
        // Get all filter parameters from request
        $range = $request->input('range', '10y');
        $paymentMethod = $request->input('payment_method');
        $status = $request->input('status');
        $studentId = $request->input('student_id');
        $location = $request->input('location');
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $breakdownScope = $request->input('breakdown_scope', 'paid');
        
        $startDate = $this->getDateFromRange($range);
        
        return $this->generateAdvancedSummary(null, $startDate, [
            'payment_method' => $paymentMethod,
            'status' => $status,
            'student_id' => $studentId,
            'location' => $location,
            'course_id' => $courseId,
            'intake_id' => $intakeId,
            'start_date' => $startDateInput,
            'end_date' => $endDateInput,
            'breakdown_scope' => $breakdownScope
        ]);
    }

    /**
     * 🔹 Advanced AJAX Filter
     */
    public function filter(Request $request)
    {
        $studentId = $request->input('student_id');
        $range = $request->input('range', '10y');
        $paymentMethod = $request->input('payment_method');
        $status = $request->input('status');
        $location = $request->input('location');
        $courseId = $request->input('course_id');
        $intakeId = $request->input('intake_id');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $breakdownScope = $request->input('breakdown_scope', 'paid');

        $startDate = $this->getDateFromRange($range);

        return $this->generateAdvancedSummary($studentId, $startDate, [
            'payment_method' => $paymentMethod,
            'status' => $status,
            'location' => $location,
            'course_id' => $courseId,
            'intake_id' => $intakeId,
            'start_date' => $startDateInput,
            'end_date' => $endDateInput,
            'breakdown_scope' => $breakdownScope
        ]);
    }

    /**
     * Get courses by location for dependent dashboard filters
     */
    public function getCoursesByLocation(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
        ]);

        $courses = Course::query()
            ->where('location', $request->input('location'))
            ->select('course_id', 'course_name', 'location')
            ->orderBy('course_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    /**
     * Get intakes by location and course for dependent dashboard filters
     */
    public function getIntakesByLocationAndCourse(Request $request)
    {
        $request->validate([
            'location' => 'required|in:Welisara,Moratuwa,Peradeniya',
            'course_id' => 'required|exists:courses,course_id',
        ]);

        $intakes = Intake::query()
            ->where('location', $request->input('location'))
            ->where('course_id', (int) $request->input('course_id'))
            ->select('intake_id', 'batch')
            ->orderBy('batch')
            ->get()
            ->map(function ($intake) {
                return [
                    'intake_id' => $intake->intake_id,
                    'intake_name' => $intake->batch,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $intakes,
        ]);
    }

    /**
     * 🔹 Student-Specific Enhanced Summary
     */
    public function studentSummary($studentId, Request $request)
    {
        $range = $request->input('range', 'all');
        $hasStatus = $this->hasPaymentDetailColumn('status');
        $startDate = $range !== 'all' ? $this->getDateFromRange($range) : null;

        $query = PaymentDetail::query()->where('student_id', $studentId);
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        // Core Metrics
        $totalCollected = $hasStatus
            ? (clone $query)->where('status', 'paid')->sum('total_fee')
            : (clone $query)->sum('total_fee');
        $totalPending = $hasStatus
            ? $this->sumIfColumnExists((clone $query)->where('status', 'pending'), 'remaining_amount')
            : 0;
        $totalLateFee = $this->sumIfColumnExists($query, 'late_fee');
        $totalDiscount = $this->sumIfColumnExists($query, 'registration_fee_discount_applied');
        
        // New Advanced Metrics
        $approvedLateFees = $this->sumIfColumnExists($query, 'approved_late_fee');
        $foreignCurrencyTotal = $this->sumIfColumnExists($query, 'foreign_currency_amount');
        $ssclTaxTotal = $this->sumIfColumnExists($query, 'sscl_tax_amount');
        $bankChargesTotal = $this->sumIfColumnExists($query, 'bank_charges');
        
        // Payment Breakdown
        $paymentByMethod = (clone $query)
            ->select('payment_method', 
                DB::raw('SUM(total_fee) as total'),
                DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $paymentByType = (clone $query)
            ->select(
                DB::raw($this->getPaymentTypeCase()),
                DB::raw('SUM(total_fee) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('type')
            ->get();

        // Status Breakdown
        $paymentByStatus = $hasStatus
            ? (clone $query)
                ->select('status',
                    DB::raw('SUM(total_fee) as total'),
                    DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
            : collect();

        // Monthly Trends
        $monthlyIncome = $hasStatus
            ? (clone $query)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                    DB::raw('SUM(CASE WHEN status = "paid" THEN total_fee ELSE 0 END) as paid'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN total_fee ELSE 0 END) as pending'),
                    DB::raw('COUNT(*) as transaction_count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
            : (clone $query)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                    DB::raw('SUM(total_fee) as paid'),
                    DB::raw('0 as pending'),
                    DB::raw('COUNT(*) as transaction_count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

        // Recent Payments with Details
        $paymentRecords = (clone $query)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        // Payment Method Comparison
        $methodComparison = (clone $query)
            ->select(
                'payment_method',
                DB::raw('AVG(total_fee) as avg_amount'),
                DB::raw('MAX(total_fee) as max_amount'),
                DB::raw('MIN(total_fee) as min_amount')
            )
            ->groupBy('payment_method')
            ->get();

        // Student Info
        $student = Student::where('student_id', $studentId)->first();

        return view('payments.student_summary', compact(
            'studentId', 'student', 'totalCollected', 'totalPending', 'totalLateFee', 
            'totalDiscount', 'approvedLateFees', 'foreignCurrencyTotal', 'ssclTaxTotal',
            'bankChargesTotal', 'paymentByMethod', 'paymentByType', 'paymentByStatus',
            'monthlyIncome', 'paymentRecords', 'methodComparison'
        ));
    }

    /**
     * 🔹 Advanced Analytics Dashboard
     */
    public function analytics(Request $request)
    {
        $range = $request->input('range', '1y');
        $startDate = $this->getDateFromRange($range);

        $query = PaymentDetail::query()->where('created_at', '>=', $startDate);

        // Revenue Analytics
        $revenueByDay = (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_fee ELSE 0 END) as revenue')
            )
            ->where('status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = $revenueByDay->sum('revenue');
        $totalPendingPayments = (clone $query)->where('status', 'pending')->sum('total_fee');
        $averagePaidTransaction = (clone $query)->where('status', 'paid')->avg('total_fee') ?? 0;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        if (Schema::hasTable('slt_loan_receivable_records')) {
            $pendingSltLoanRecoveries = SltLoanReceivableRecord::with(['studentPaymentPlan.student', 'studentPaymentPlan.course'])
                ->whereBetween('payment_effective_date', [$startOfMonth, $endOfMonth])
                ->orderBy('payment_effective_date')
                ->get()
                ->map(function ($record) {
                    $plan = $record->studentPaymentPlan;
                    $student = optional($plan)->student;
                    $course = optional($plan)->course;
                    $registration = CourseRegistration::where('student_id', optional($plan)->student_id)
                        ->where('course_id', optional($plan)->course_id)
                        ->orderByDesc('id')
                        ->first();

                    return [
                        'student_name' => optional($student)->full_name ?? 'N/A',
                        'student_id_value' => optional($student)->id_value ?? null,
                        'course_name' => optional($course)->course_name ?? 'N/A',
                        'intake' => optional($registration?->intake)->batch ?? 'N/A',
                        'loan_amount' => (float) ($plan->slt_loan_amount ?? 0),
                        'installment_amount' => (float) ($record->monthly_receivable_amount ?? 0),
                        'effective_date' => optional($record->payment_effective_date)->format('Y-m-d'),
                        'course_id' => optional($plan)->course_id,
                    ];
                });
        } else {
            $pendingSltLoanRecoveries = StudentPaymentPlan::with(['student', 'course'])
                ->where('slt_loan_applied', 'yes')
                ->whereBetween('slt_receivable_effective_date', [$startOfMonth, $endOfMonth])
                ->orderBy('slt_receivable_effective_date')
                ->get()
                ->map(function ($plan) {
                    $student = optional($plan)->student;
                    $course = optional($plan)->course;
                    $registration = CourseRegistration::where('student_id', optional($plan)->student_id)
                        ->where('course_id', optional($plan)->course_id)
                        ->orderByDesc('id')
                        ->first();

                    $installmentAmount = 0;
                    if ($plan->slt_loan_amount && $plan->slt_loan_years) {
                        $installmentAmount = round($plan->slt_loan_amount / ($plan->slt_loan_years * 12), 2);
                    }

                    return [
                        'student_name' => optional($student)->full_name ?? 'N/A',
                        'student_id_value' => optional($student)->id_value ?? null,
                        'course_name' => optional($course)->course_name ?? 'N/A',
                        'intake' => optional($registration?->intake)->batch ?? 'N/A',
                        'loan_amount' => (float) ($plan->slt_loan_amount ?? 0),
                        'installment_amount' => $installmentAmount,
                        'effective_date' => optional($plan->slt_receivable_effective_date)->format('Y-m-d'),
                        'course_id' => optional($plan)->course_id,
                    ];
                });
        }

        $selectedNewRegistrationCourseId = $request->input('new_registration_course_id');
        $selectedOngoingCourseId = $request->input('ongoing_course_id');

        $totalSltLoanRecoveries = $pendingSltLoanRecoveries->count();
        $totalSltRecoveryAmount = $pendingSltLoanRecoveries->sum('installment_amount');

        $courses = Course::query()
            ->select('course_id', 'course_name')
            ->orderBy('course_name')
            ->get();

        $availableCourses = Course::query()
            ->select('course_id', 'course_name', 'location')
            ->orderBy('course_name')
            ->get();

        $registrationSummary = CourseRegistration::query()
            ->select(
                'course_id',
                DB::raw('COUNT(*) as total_registrations'),
                DB::raw("SUM(CASE WHEN status = 'Registered' THEN 1 ELSE 0 END) as ongoing_courses"),
                DB::raw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_registrations"),
                DB::raw("SUM(CASE WHEN registration_date >= '{$startOfMonth->toDateString()}' THEN 1 ELSE 0 END) as new_registrations")
            )
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $paymentSummary = PaymentDetail::join('course_registration', 'payment_details.course_registration_id', '=', 'course_registration.id')
            ->select(
                'course_registration.course_id',
                DB::raw("SUM(CASE WHEN payment_details.status = 'paid' THEN payment_details.amount ELSE 0 END) as paid_amount"),
                DB::raw("SUM(CASE WHEN payment_details.status = 'pending' THEN payment_details.amount ELSE 0 END) as pending_amount"),
                DB::raw('COUNT(payment_details.id) as payment_count')
            )
            ->groupBy('course_registration.course_id')
            ->get()
            ->keyBy('course_id');

        $courseWiseSummary = $availableCourses->map(function ($course) use ($registrationSummary, $paymentSummary) {
            $courseId = (int) $course->course_id;
            $registrationRow = $registrationSummary->get($courseId);
            $paymentRow = $paymentSummary->get($courseId);

            return [
                'course_id' => $courseId,
                'course_name' => $course->course_name,
                'location' => $course->location ?? 'N/A',
                'total_registrations' => (int) ($registrationRow->total_registrations ?? 0),
                'new_registrations' => (int) ($registrationRow->new_registrations ?? 0),
                'ongoing_courses' => (int) ($registrationRow->ongoing_courses ?? 0),
                'pending_registrations' => (int) ($registrationRow->pending_registrations ?? 0),
                'paid_amount' => (float) ($paymentRow->paid_amount ?? 0),
                'pending_amount' => (float) ($paymentRow->pending_amount ?? 0),
                'payment_count' => (int) ($paymentRow->payment_count ?? 0),
            ];
        })->sortByDesc('paid_amount')->values();

        $newRegistrationsCurrentMonthQuery = CourseRegistration::query()
            ->when($selectedNewRegistrationCourseId, function ($query) use ($selectedNewRegistrationCourseId) {
                $query->where('course_id', $selectedNewRegistrationCourseId);
            })
            ->whereDate('registration_date', '>=', $startOfMonth->toDateString())
            ->whereDate('registration_date', '<=', $endOfMonth->toDateString());

        $newRegistrationsCount = (clone $newRegistrationsCurrentMonthQuery)->count();
        $newRegistrationsAmount = (float) (clone $newRegistrationsCurrentMonthQuery)->sum(DB::raw('COALESCE(registration_fee, 0)'));

        $ongoingCoursesQuery = CourseRegistration::query()
            ->where('status', 'Registered')
            ->when($selectedOngoingCourseId, function ($query) use ($selectedOngoingCourseId) {
                $query->where('course_id', $selectedOngoingCourseId);
            });

        $ongoingCoursesCount = (clone $ongoingCoursesQuery)->count();

        $ongoingCoursesAmount = (float) PaymentDetail::join('course_registration', 'payment_details.course_registration_id', '=', 'course_registration.id')
            ->where('course_registration.status', 'Registered')
            ->when($selectedOngoingCourseId, function ($query) use ($selectedOngoingCourseId) {
                $query->where('course_registration.course_id', $selectedOngoingCourseId);
            })
            ->where('payment_details.status', 'paid')
            ->whereDate('payment_details.created_at', '>=', $startOfMonth->toDateString())
            ->whereDate('payment_details.created_at', '<=', $endOfMonth->toDateString())
            ->sum('payment_details.amount');

        return view('payments.analytics', compact(
            'revenueByDay', 'pendingSltLoanRecoveries', 'totalRevenue',
            'totalPendingPayments', 'averagePaidTransaction',
            'totalSltLoanRecoveries', 'totalSltRecoveryAmount',
            'courseWiseSummary', 'newRegistrationsCount', 'newRegistrationsAmount',
            'ongoingCoursesCount', 'ongoingCoursesAmount', 'courses',
            'selectedNewRegistrationCourseId', 'selectedOngoingCourseId'
        ));
    }

    /**
     * 🔹 Export Report (CSV/PDF)
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        $range = $request->input('range', '1y');
        $startDate = $this->getDateFromRange($range);

        $payments = PaymentDetail::where('created_at', '>=', $startDate)
            ->with(['student', 'registration'])
            ->get();

        if ($format === 'csv') {
            return $this->exportCSV($payments);
        }

        return response()->json(['error' => 'Format not supported'], 400);
    }

    /**
     * 🔹 Comparison Dashboard (Year over Year, Month over Month)
     */
    public function comparison(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        // Year over Year Comparison
        $currentYearData = PaymentDetail::whereYear('created_at', $currentYear)
            ->where('status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_fee) as revenue')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $previousYearData = PaymentDetail::whereYear('created_at', $previousYear)
            ->where('status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_fee) as revenue')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Growth Metrics
        $currentYearTotal = PaymentDetail::whereYear('created_at', $currentYear)
            ->where('status', 'paid')
            ->sum('total_fee');

        $previousYearTotal = PaymentDetail::whereYear('created_at', $previousYear)
            ->where('status', 'paid')
            ->sum('total_fee');

        $growthRate = $previousYearTotal > 0 
            ? (($currentYearTotal - $previousYearTotal) / $previousYearTotal) * 100 
            : 0;

        return view('payments.comparison', compact(
            'currentYearData', 'previousYearData', 'currentYearTotal', 
            'previousYearTotal', 'growthRate', 'currentYear', 'previousYear'
        ));
    }

    /**
     * 🔹 Generate Advanced Summary - FIXED VERSION
     */
    private function generateAdvancedSummary($studentId = null, $startDate = null, $filters = [])
    {
        $paymentTable = $this->getPaymentDetailsTable();
        $dashboardDateExpr = $this->getDashboardDateSqlExpression($paymentTable);
        $query = PaymentDetail::query();
        $hasStatus = $this->hasPaymentDetailColumn('status');
        $breakdownScope = ($filters['breakdown_scope'] ?? 'paid') === 'all' ? 'all' : 'paid';
        $matchingStudentIds = collect();

        // Apply student filter (supports internal student_id and NIC/id_value)
        $studentSearch = trim((string) ($filters['student_id'] ?? $studentId ?? ''));
        if ($studentSearch !== '') {
            $matchingStudentIds = Student::query()
                ->where('id_value', $studentSearch)
                ->orWhere('student_id', $studentSearch)
                ->pluck('student_id')
                ->unique()
                ->values();

            if ($matchingStudentIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn($paymentTable . '.student_id', $matchingStudentIds->all());
            }
        }

        // Apply date range filter
        $startDateInput = !empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDateInput = !empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;

        if ($startDateInput || $endDateInput) {
            if ($startDateInput) {
                $query->whereRaw("DATE({$dashboardDateExpr}) >= ?", [$startDateInput]);
            }

            if ($endDateInput) {
                $query->whereRaw("DATE({$dashboardDateExpr}) <= ?", [$endDateInput]);
            }
        } elseif ($startDate) {
            $query->whereRaw("DATE({$dashboardDateExpr}) >= ?", [$startDate->toDateString()]);
        }

        // Apply location filter via registration context
        if (!empty($filters['location'])) {
            $query->whereHas('registration', function ($q) use ($filters) {
                $q->where('location', $filters['location']);
            });
        }

        // Apply course filter via registration context
        if (!empty($filters['course_id'])) {
            $query->whereHas('registration', function ($q) use ($filters) {
                $q->where('course_id', (int) $filters['course_id']);
            });
        }

        // Apply intake filter via registration context
        if (!empty($filters['intake_id'])) {
            $query->whereHas('registration', function ($q) use ($filters) {
                $q->where('intake_id', (int) $filters['intake_id']);
            });
        }

        // Apply payment method filter
        if (!empty($filters['payment_method'])) {
            $query->where($paymentTable . '.payment_method', $filters['payment_method']);
        }

        // Keep a copy before status filtering so methods/types can use their own scope.
        $queryBeforeStatusFilter = clone $query;

        // Apply status filter
        if (!empty($filters['status']) && $hasStatus) {
            $query->where($paymentTable . '.status', $filters['status']);
        }

        // Core KPIs
        $totalCollected = $hasStatus
            ? (clone $query)->where($paymentTable . '.status', 'paid')->sum($paymentTable . '.total_fee')
            : (clone $query)->sum($paymentTable . '.total_fee');
        $totalPending = $hasStatus
            ? $this->sumIfColumnExists((clone $query)->where($paymentTable . '.status', 'pending'), 'remaining_amount')
            : 0;
        $totalLateFee = $this->sumIfColumnExists($query, 'late_fee');
        $totalDiscount = $this->sumIfColumnExists($query, 'registration_fee_discount_applied');
        
        // Advanced KPIs
        $totalTransactions = (clone $query)->count();
        $averageTransaction = $totalTransactions > 0 ? $totalCollected / $totalTransactions : 0;
        $ssclTaxTotal = $this->sumIfColumnExists($query, 'sscl_tax_amount');
        $bankChargesTotal = $this->sumIfColumnExists($query, 'bank_charges');

        // Payment Breakdowns
        $methodTypeQuery = clone $queryBeforeStatusFilter;
        if ($hasStatus && $breakdownScope === 'paid') {
            $methodTypeQuery->where($paymentTable . '.status', 'paid');
        }

        $paymentByMethod = (clone $methodTypeQuery)
            ->select($paymentTable . '.payment_method', 
                DB::raw("SUM({$paymentTable}.total_fee) as total"),
                DB::raw('COUNT(*) as count'))
            ->groupBy($paymentTable . '.payment_method')
            ->get();

        $paymentByType = (clone $methodTypeQuery)
            ->select(
                DB::raw($this->getPaymentTypeCase()),
                DB::raw("SUM({$paymentTable}.total_fee) as total"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('type')
            ->get();

        $paymentByStatus = $hasStatus
            ? (clone $query)
                ->select($paymentTable . '.status',
                    DB::raw("SUM({$paymentTable}.total_fee) as total"),
                    DB::raw('COUNT(*) as count'))
                ->groupBy($paymentTable . '.status')
                ->get()
            : collect();

        // Time-based Analytics
        $monthlyIncome = $hasStatus
            ? (clone $query)
                ->select(
                    DB::raw("DATE_FORMAT({$dashboardDateExpr}, '%Y-%m') as month"),
                    DB::raw("SUM(CASE WHEN {$paymentTable}.status = 'paid' THEN {$paymentTable}.total_fee ELSE 0 END) as paid"),
                    DB::raw("SUM(CASE WHEN {$paymentTable}.status = 'pending' THEN {$paymentTable}.total_fee ELSE 0 END) as pending")
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
            : (clone $query)
                ->select(
                    DB::raw("DATE_FORMAT({$dashboardDateExpr}, '%Y-%m') as month"),
                    DB::raw("SUM({$paymentTable}.total_fee) as paid"),
                    DB::raw('0 as pending')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

        $weeklyTrend = (clone $query)
            ->select(
                DB::raw("YEARWEEK({$dashboardDateExpr}) as week"),
                DB::raw("SUM({$paymentTable}.total_fee) as total")
            )
            ->when($hasStatus, function ($q) {
                return $q->where($this->getPaymentDetailsTable() . '.status', 'paid');
            })
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(12)
            ->get();

        $districtAnalytics = Student::query()
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(district), ''), 'Unknown') as district"),
                DB::raw('COUNT(student_id) as student_count')
            )
            ->groupBy('district')
            ->orderByDesc('student_count')
            ->orderBy('district')
            ->get();

        $selectedLocation = !empty($filters['location']) ? $filters['location'] : null;
        $selectedCourseId = !empty($filters['course_id']) ? (int) $filters['course_id'] : null;
        $selectedIntakeId = !empty($filters['intake_id']) ? (int) $filters['intake_id'] : null;
        $selectedNewRegistrationCourseId = !empty($filters['new_registration_course_id']) ? (int) $filters['new_registration_course_id'] : null;
        $selectedOngoingCourseId = !empty($filters['ongoing_course_id']) ? (int) $filters['ongoing_course_id'] : null;
        $currentMonthStart = now()->startOfMonth()->toDateString();
        $currentMonthEnd = now()->toDateString();

        $registrationQuery = CourseRegistration::query();

        if ($studentSearch !== '') {
            if (isset($matchingStudentIds) && $matchingStudentIds->isNotEmpty()) {
                $registrationQuery->whereIn('student_id', $matchingStudentIds->all());
            } else {
                $registrationQuery->whereRaw('1 = 0');
            }
        }

        if ($selectedLocation) {
            $registrationQuery->where('location', $selectedLocation);
        }

        if ($selectedCourseId) {
            $registrationQuery->where('course_id', $selectedCourseId);
        }

        if ($selectedIntakeId) {
            $registrationQuery->where('intake_id', $selectedIntakeId);
        }

        if ($startDateInput || $endDateInput) {
            if ($startDateInput) {
                $registrationQuery->whereDate('registration_date', '>=', $startDateInput);
            }

            if ($endDateInput) {
                $registrationQuery->whereDate('registration_date', '<=', $endDateInput);
            }
        } elseif ($startDate) {
            $registrationQuery->whereDate('registration_date', '>=', $startDate->toDateString());
        }

        $availableCourses = Course::query()
            ->select('course_id', 'course_name', 'location')
            ->when($selectedLocation, function ($q) use ($selectedLocation) {
                $q->where('location', $selectedLocation);
            })
            ->orderBy('course_name')
            ->get();

        $registrationSummary = (clone $registrationQuery)
            ->select(
                'course_id',
                DB::raw('COUNT(*) as total_registrations'),
                DB::raw("SUM(CASE WHEN status = 'Registered' THEN 1 ELSE 0 END) as ongoing_courses"),
                DB::raw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_registrations"),
                DB::raw("SUM(CASE WHEN registration_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_registrations")
            )
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $paymentSummary = PaymentDetail::join('course_registration', 'payment_details.course_registration_id', '=', 'course_registration.id')
            ->where('course_registration.location', $selectedLocation ?? auth()->user()->user_location ?? 'Welisara')
            ->when($selectedCourseId, function ($query) use ($selectedCourseId) {
                $query->where('course_registration.course_id', $selectedCourseId);
            })
            ->when($selectedIntakeId, function ($query) use ($selectedIntakeId) {
                $query->where('course_registration.intake_id', $selectedIntakeId);
            })
            ->when($studentSearch !== '', function ($query) use ($matchingStudentIds) {
                if (isset($matchingStudentIds) && $matchingStudentIds->isNotEmpty()) {
                    $query->whereIn('course_registration.student_id', $matchingStudentIds->all());
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->when($startDateInput || $endDateInput || $startDate, function ($query) use ($dashboardDateExpr, $startDateInput, $endDateInput, $startDate) {
                if ($startDateInput) {
                    $query->whereRaw("DATE({$dashboardDateExpr}) >= ?", [$startDateInput]);
                } elseif ($startDate) {
                    $query->whereRaw("DATE({$dashboardDateExpr}) >= ?", [$startDate->toDateString()]);
                }

                if ($endDateInput) {
                    $query->whereRaw("DATE({$dashboardDateExpr}) <= ?", [$endDateInput]);
                }
            })
            ->select(
                'course_registration.course_id',
                DB::raw("SUM(CASE WHEN payment_details.status = 'paid' THEN payment_details.amount ELSE 0 END) as paid_amount"),
                DB::raw("SUM(CASE WHEN payment_details.status = 'pending' THEN payment_details.amount ELSE 0 END) as pending_amount"),
                DB::raw('COUNT(payment_details.id) as payment_count')
            )
            ->groupBy('course_registration.course_id')
            ->get()
            ->keyBy('course_id');

        $courseWiseSummary = $availableCourses->map(function ($course) use ($registrationSummary, $paymentSummary) {
            $courseId = (int) $course->course_id;
            $registrationRow = $registrationSummary->get($courseId);
            $paymentRow = $paymentSummary->get($courseId);

            return [
                'course_id' => $courseId,
                'course_name' => $course->course_name,
                'location' => $course->location ?? 'N/A',
                'total_registrations' => (int) ($registrationRow->total_registrations ?? 0),
                'new_registrations' => (int) ($registrationRow->new_registrations ?? 0),
                'ongoing_courses' => (int) ($registrationRow->ongoing_courses ?? 0),
                'pending_registrations' => (int) ($registrationRow->pending_registrations ?? 0),
                'paid_amount' => (float) ($paymentRow->paid_amount ?? 0),
                'pending_amount' => (float) ($paymentRow->pending_amount ?? 0),
                'payment_count' => (int) ($paymentRow->payment_count ?? 0),
            ];
        })->sortByDesc('paid_amount')->values();

        $newRegistrationsCurrentMonthQuery = CourseRegistration::query()
            ->when($selectedLocation, function ($query) use ($selectedLocation) {
                $query->where('location', $selectedLocation);
            })
            ->when($selectedNewRegistrationCourseId, function ($query) use ($selectedNewRegistrationCourseId) {
                $query->where('course_id', $selectedNewRegistrationCourseId);
            })
            ->whereDate('registration_date', '>=', $currentMonthStart)
            ->whereDate('registration_date', '<=', $currentMonthEnd);

        $newRegistrationsCount = (clone $newRegistrationsCurrentMonthQuery)->count();
        $newRegistrationsAmount = (float) (clone $newRegistrationsCurrentMonthQuery)->sum(DB::raw('COALESCE(registration_fee, 0)'));

        $ongoingRegistrationsQuery = CourseRegistration::query()
            ->where('status', 'Registered')
            ->when($selectedLocation, function ($query) use ($selectedLocation) {
                $query->where('location', $selectedLocation);
            })
            ->when($selectedOngoingCourseId, function ($query) use ($selectedOngoingCourseId) {
                $query->where('course_id', $selectedOngoingCourseId);
            });

        $ongoingCoursesCount = (clone $ongoingRegistrationsQuery)->count();

        $ongoingCoursesAmount = (float) PaymentDetail::join('course_registration', 'payment_details.course_registration_id', '=', 'course_registration.id')
            ->where('course_registration.status', 'Registered')
            ->when($selectedLocation, function ($query) use ($selectedLocation) {
                $query->where('course_registration.location', $selectedLocation);
            })
            ->when($selectedOngoingCourseId, function ($query) use ($selectedOngoingCourseId) {
                $query->where('course_registration.course_id', $selectedOngoingCourseId);
            })
            ->whereRaw("DATE({$dashboardDateExpr}) >= ?", [$currentMonthStart])
            ->whereRaw("DATE({$dashboardDateExpr}) <= ?", [$currentMonthEnd])
            ->where('payment_details.status', 'paid')
            ->sum('payment_details.amount');

        $newRegistrations = (clone $newRegistrationsCurrentMonthQuery)
            ->with(['student', 'course'])
            ->orderBy('registration_date', 'desc')
            ->limit(10)
            ->get();

        if (request()->ajax()) {
            return response()->json([
                'totalCollected' => $totalCollected,
                'totalPending' => $totalPending,
                'totalLateFee' => $totalLateFee,
                'totalDiscount' => $totalDiscount,
                'totalTransactions' => $totalTransactions,
                'averageTransaction' => $averageTransaction,
                'ssclTaxTotal' => $ssclTaxTotal,
                'bankChargesTotal' => $bankChargesTotal,
                'paymentByMethod' => $paymentByMethod,
                'paymentByType' => $paymentByType,
                'paymentByStatus' => $paymentByStatus,
                'breakdownScope' => $breakdownScope,
                'monthlyIncome' => $monthlyIncome,
                'weeklyTrend' => $weeklyTrend,
                'districtAnalytics' => $districtAnalytics,
                'courseWiseSummary' => $courseWiseSummary,
                'newRegistrations' => $newRegistrations,
                'newRegistrationsCount' => $newRegistrationsCount,
                'newRegistrationsAmount' => $newRegistrationsAmount,
                'ongoingCoursesCount' => $ongoingCoursesCount,
                'ongoingCoursesAmount' => $ongoingCoursesAmount,
            ]);
        }

        $courses = Course::query()
            ->select('course_id', 'course_name')
            ->when($selectedLocation, function ($q) use ($selectedLocation) {
                $q->where('location', $selectedLocation);
            })
            ->orderBy('course_name')
            ->get();

        $intakes = Intake::query()
            ->select('intake_id', 'batch')
            ->when($selectedLocation, function ($q) use ($selectedLocation) {
                $q->where('location', $selectedLocation);
            })
            ->when($selectedCourseId, function ($q) use ($selectedCourseId) {
                $q->where('course_id', $selectedCourseId);
            })
            ->orderBy('batch')
            ->get();

        return view('payments.summary', compact(
            'totalCollected', 'totalPending', 'totalLateFee', 'totalDiscount',
            'totalTransactions', 'averageTransaction', 'ssclTaxTotal', 'bankChargesTotal',
            'paymentByMethod', 'paymentByType', 'paymentByStatus', 'breakdownScope', 'monthlyIncome',
            'weeklyTrend', 'districtAnalytics', 'courses', 'intakes', 'courseWiseSummary',
            'newRegistrations', 'newRegistrationsCount', 'newRegistrationsAmount',
            'ongoingCoursesCount', 'ongoingCoursesAmount'
        ));
    }

    /**
     * Helper: Get date from range string
     */
    private function getDateFromRange($range)
    {
        return match ($range) {
            '10y' => Carbon::now()->subYears(10),
            '5y' => Carbon::now()->subYears(5),
            '2y' => Carbon::now()->subYears(2),
            '1y' => Carbon::now()->subYear(),
            '6m' => Carbon::now()->subMonths(6),
            '3m' => Carbon::now()->subMonths(3),
            '1m' => Carbon::now()->subMonth(),
            '1w' => Carbon::now()->subWeek(),
            default => Carbon::now()->subYear(),
        };
    }

    /**
     * Helper: Payment type case statement
     */
    private function getPaymentTypeCase()
    {
        $table = $this->getPaymentDetailsTable();
        $hasInstallmentType = Schema::hasColumn($table, 'installment_type');
        $hasMiscCategory = Schema::hasColumn($table, 'misc_category');

        if (!$hasInstallmentType && !$hasMiscCategory) {
            return "'Unknown' as type";
        }

        if ($hasInstallmentType && !$hasMiscCategory) {
            return "CASE
                WHEN installment_type = '' THEN 'Unknown'
                WHEN installment_type IS NULL THEN 'Unknown'
                ELSE
                    CASE
                        WHEN installment_type = 'course_fee' THEN 'Course Fee'
                        WHEN installment_type = 'franchise_fee' THEN 'Franchise Fee'
                        WHEN installment_type = 'registration_fee' THEN 'Registration Fee'
                        ELSE installment_type
                    END
            END as type";
        }

        if (!$hasInstallmentType && $hasMiscCategory) {
            return "CASE
                WHEN misc_category IS NOT NULL THEN 'Miscellaneous'
                ELSE 'Unknown'
            END as type";
        }

        return "CASE 
            WHEN installment_type IS NULL AND misc_category IS NOT NULL THEN 'Miscellaneous'
            WHEN installment_type = '' THEN 'Unknown'
            WHEN installment_type IS NULL THEN 'Unknown'
            ELSE 
                CASE 
                    WHEN installment_type = 'course_fee' THEN 'Course Fee'
                    WHEN installment_type = 'franchise_fee' THEN 'Franchise Fee'
                    WHEN installment_type = 'registration_fee' THEN 'Registration Fee'
                    ELSE installment_type
                END
        END as type";
    }

    /**
     * Helper: Sum a column only if it exists in the payments table
     */
    private function sumIfColumnExists($query, string $column)
    {
        $table = $this->getPaymentDetailsTable();

        if (!Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (clone $query)->sum($column);
    }

    private function getPaymentDetailsTable(): string
    {
        return (new PaymentDetail())->getTable();
    }

    private function hasPaymentDetailColumn(string $column): bool
    {
        return Schema::hasColumn($this->getPaymentDetailsTable(), $column);
    }

    private function getDashboardDateSqlExpression(string $table): string
    {
        $dateColumns = [];

        if ($this->hasPaymentDetailColumn('payment_effective_date')) {
            $dateColumns[] = "{$table}.payment_effective_date";
        }

        if ($this->hasPaymentDetailColumn('payment_date')) {
            $dateColumns[] = "{$table}.payment_date";
        }

        if ($this->hasPaymentDetailColumn('due_date')) {
            $dateColumns[] = "{$table}.due_date";
        }

        // Final fallback so old rows still participate in reports.
        $dateColumns[] = "{$table}.created_at";

        return 'COALESCE(' . implode(', ', $dateColumns) . ')';
    }

    /**
     * 🔹 Live Payment Feed (for real-time updates)
     */
    public function liveFeed(Request $request)
    {
        $lastId = $request->input('last_id', 0);
        
        $payments = PaymentDetail::where('id', '>', $lastId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return response()->json([
            'payments' => $payments,
            'last_id' => $payments->max('id') ?? $lastId,
            'count' => $payments->count()
        ]);
    }

    /**
     * Helper: Export to CSV
     */
    private function exportCSV($payments)
    {
        $filename = 'payment_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Student ID', 'Type', 'Method', 'Amount', 'Status', 
                'Late Fee', 'Discount', 'Date'
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->student_id,
                    $payment->installment_type ?? 'Misc',
                    $payment->payment_method,
                    $payment->total_fee,
                    $payment->status,
                    $payment->late_fee,
                    $payment->registration_fee_discount_applied,
                    $payment->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}