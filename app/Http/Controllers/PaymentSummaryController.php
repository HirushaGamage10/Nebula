<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentSummaryController extends Controller
{
    /**
     * 🔹 Enhanced Global Dashboard with Advanced Metrics
     */
    public function index(Request $request)
    {
        $range = $request->input('range', '1y');
        $startDate = $this->getDateFromRange($range);
        
        return $this->generateAdvancedSummary(null, $startDate);
    }

    /**
     * 🔹 Advanced AJAX Filter
     */
    public function filter(Request $request)
    {
        $studentId = $request->input('student_id');
        $range = $request->input('range', '1y');
        $paymentMethod = $request->input('payment_method');
        $paymentType = $request->input('payment_type');
        $status = $request->input('status');

        $startDate = $this->getDateFromRange($range);

        return $this->generateAdvancedSummary($studentId, $startDate, [
            'payment_method' => $paymentMethod,
            'payment_type' => $paymentType,
            'status' => $status
        ]);
    }

    /**
     * 🔹 Student-Specific Enhanced Summary
     */
    public function studentSummary($studentId, Request $request)
    {
        $range = $request->input('range', 'all');
        $startDate = $range !== 'all' ? $this->getDateFromRange($range) : null;

        $query = PaymentDetail::query()->where('student_id', $studentId);
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        // Core Metrics
        $totalCollected = (clone $query)->where('status', 'paid')->sum('total_fee');
        $totalPending = (clone $query)->where('status', 'pending')->sum('remaining_amount');
        $totalLateFee = (clone $query)->sum('late_fee');
        $totalDiscount = (clone $query)->sum('registration_fee_discount_applied');
        
        // New Advanced Metrics
        $approvedLateFees = (clone $query)->sum('approved_late_fee');
        $foreignCurrencyTotal = (clone $query)->sum('foreign_currency_amount');
        $ssclTaxTotal = (clone $query)->sum('sscl_tax_amount');
        $bankChargesTotal = (clone $query)->sum('bank_charges');
        
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
        $paymentByStatus = (clone $query)
            ->select('status', 
                DB::raw('SUM(total_fee) as total'),
                DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly Trends
        $monthlyIncome = (clone $query)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_fee ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN total_fee ELSE 0 END) as pending'),
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

        return view('payment.student_summary', compact(
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

        // Payment Success Rate
        $successRate = (clone $query)
            ->select(
                DB::raw('COUNT(CASE WHEN status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('(COUNT(CASE WHEN status = "paid" THEN 1 END) / COUNT(*) * 100) as success_rate')
            )
            ->first();

        // Late Fee Analysis
        $lateFeeAnalysis = (clone $query)
            ->select(
                DB::raw('SUM(late_fee) as total_late_fees'),
                DB::raw('SUM(approved_late_fee) as total_approved'),
                DB::raw('COUNT(CASE WHEN late_fee > 0 THEN 1 END) as late_payment_count')
            )
            ->first();

        // Currency Breakdown
        $currencyBreakdown = (clone $query)
            ->whereNotNull('foreign_currency_code')
            ->select(
                'foreign_currency_code',
                DB::raw('SUM(foreign_currency_amount) as total_foreign'),
                DB::raw('SUM(total_fee) as total_lkr'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('foreign_currency_code')
            ->get();

        // Top Performing Courses
        $topCourses = (clone $query)
            ->whereNotNull('course_registration_id')
            ->select(
                'course_registration_id',
                DB::raw('SUM(total_fee) as revenue'),
                DB::raw('COUNT(DISTINCT student_id) as student_count')
            )
            ->groupBy('course_registration_id')
            ->orderByDesc('revenue')
            ->take(10)
            ->get();

        // Payment Method Performance
        $methodPerformance = (clone $query)
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_fee) as total_revenue'),
                DB::raw('AVG(total_fee) as avg_transaction'),
                DB::raw('COUNT(CASE WHEN status = "paid" THEN 1 END) as success_count')
            )
            ->groupBy('payment_method')
            ->get();

        return view('payment.analytics', compact(
            'revenueByDay', 'successRate', 'lateFeeAnalysis', 
            'currencyBreakdown', 'topCourses', 'methodPerformance'
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

        // Add PDF export logic here if needed
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

        return view('payment.comparison', compact(
            'currentYearData', 'previousYearData', 'currentYearTotal', 
            'previousYearTotal', 'growthRate', 'currentYear', 'previousYear'
        ));
    }

    /**
     * 🔹 Generate Advanced Summary
     */
    private function generateAdvancedSummary($studentId = null, $startDate = null, $filters = [])
    {
        $query = PaymentDetail::query();

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        // Apply additional filters
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Core KPIs
        $totalCollected = (clone $query)->where('status', 'paid')->sum('total_fee');
        $totalPending = (clone $query)->where('status', 'pending')->sum('remaining_amount');
        $totalLateFee = (clone $query)->sum('late_fee');
        $totalDiscount = (clone $query)->sum('registration_fee_discount_applied');
        
        // Advanced KPIs
        $totalTransactions = (clone $query)->count();
        $averageTransaction = $totalTransactions > 0 ? $totalCollected / $totalTransactions : 0;
        $ssclTaxTotal = (clone $query)->sum('sscl_tax_amount');
        $bankChargesTotal = (clone $query)->sum('bank_charges');

        // Payment Breakdowns
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

        $paymentByStatus = (clone $query)
            ->select('status', 
                DB::raw('SUM(total_fee) as total'),
                DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Time-based Analytics
        $monthlyIncome = (clone $query)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(CASE WHEN status = "paid" THEN total_fee ELSE 0 END) as paid'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN total_fee ELSE 0 END) as pending')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $weeklyTrend = (clone $query)
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('SUM(total_fee) as total')
            )
            ->where('status', 'paid')
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->take(12)
            ->get();

        // Top Students
        $topStudents = (clone $query)
            ->select('student_id', 
                DB::raw('SUM(total_fee) as total'),
                DB::raw('COUNT(*) as payment_count'))
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Recent Activity
        $recentPayments = (clone $query)
            ->orderByDesc('created_at')
            ->take(15)
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
                'monthlyIncome' => $monthlyIncome,
                'weeklyTrend' => $weeklyTrend,
                'topStudents' => $topStudents,
                'recentPayments' => $recentPayments,
            ]);
        }

        return view('payment.summary', compact(
            'totalCollected', 'totalPending', 'totalLateFee', 'totalDiscount',
            'totalTransactions', 'averageTransaction', 'ssclTaxTotal', 'bankChargesTotal',
            'paymentByMethod', 'paymentByType', 'paymentByStatus', 'monthlyIncome',
            'weeklyTrend', 'topStudents', 'recentPayments'
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