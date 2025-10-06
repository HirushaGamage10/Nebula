<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentSummaryController extends Controller
{
    /**
     * 🔹 Global Summary (Main Dashboard)
     */
    public function index()
    {
        return $this->generateSummary();
    }

    /**
     * 🔹 AJAX Filter (Student ID + Date Range)
     */
    public function filter(Request $request)
    {
        $studentId = $request->input('student_id');
        $range = $request->input('range');

        // Date filtering logic
        $startDate = match ($range) {
            '10y' => Carbon::now()->subYears(10),
            '5y'  => Carbon::now()->subYears(5),
            '2y'  => Carbon::now()->subYears(2),
            '1y'  => Carbon::now()->subYear(),
            '6m'  => Carbon::now()->subMonths(6),
            '3m'  => Carbon::now()->subMonths(3),
            '1m'  => Carbon::now()->subMonth(),
            default => Carbon::now()->subYears(10),
        };

        return $this->generateSummary($studentId, $startDate);
    }

    /**
     * 🔹 Student-Specific Summary
     */
    public function studentSummary($studentId)
    {
        $request = request();
        $range = $request->input('range');

        // Optional range if user applies filters in the URL
        $startDate = match ($range) {
            '10y' => Carbon::now()->subYears(10),
            '5y'  => Carbon::now()->subYears(5),
            '2y'  => Carbon::now()->subYears(2),
            '1y'  => Carbon::now()->subYear(),
            '6m'  => Carbon::now()->subMonths(6),
            '3m'  => Carbon::now()->subMonths(3),
            '1m'  => Carbon::now()->subMonth(),
            default => null,
        };

        // Query payments for that student
        $query = PaymentDetail::query()->where('student_id', $studentId);
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $totalCollected = (clone $query)->where('status', 'paid')->sum('total_fee');
        $totalPending   = (clone $query)->where('status', 'pending')->sum('remaining_amount');
        $totalLateFee   = (clone $query)->sum('late_fee');
        $totalDiscount  = (clone $query)->sum('registration_fee_discount_applied');

        $paymentByMethod = (clone $query)
            ->select('payment_method', DB::raw('SUM(total_fee) as total'))
            ->groupBy('payment_method')
            ->get();

        // ✅ Detect Miscellaneous if installment_type is NULL but misc_category exists
        $paymentByType = (clone $query)
    ->select(
        DB::raw("
            CASE 
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
            END as type
        "),
        DB::raw('SUM(total_fee) as total')
    )
    ->groupBy('type')
    ->get();


        $monthlyIncome = (clone $query)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(total_fee) as total'))
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $paymentRecords = (clone $query)
            ->orderByDesc('created_at')
            ->take(50)
            ->get();

        return view('payment.student_summary', compact(
            'studentId', 'totalCollected', 'totalPending', 'totalLateFee', 'totalDiscount',
            'paymentByMethod', 'paymentByType', 'monthlyIncome', 'paymentRecords'
        ));
    }

    /**
     * 🔹 Generate Summary (Global or Filtered)
     */
    private function generateSummary($studentId = null, $startDate = null)
    {
        $query = PaymentDetail::query();

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $totalCollected = (clone $query)->where('status', 'paid')->sum('total_fee');
        $totalPending   = (clone $query)->where('status', 'pending')->sum('remaining_amount');
        $totalLateFee   = (clone $query)->sum('late_fee');
        $totalDiscount  = (clone $query)->sum('registration_fee_discount_applied');

        $paymentByMethod = (clone $query)
            ->select('payment_method', DB::raw('SUM(total_fee) as total'))
            ->groupBy('payment_method')
            ->get();

        // ✅ Add Miscellaneous detection here too
        $paymentByType = (clone $query)
    ->select(
        DB::raw("
            CASE 
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
            END as type
        "),
        DB::raw('SUM(total_fee) as total')
    )
    ->groupBy('type')
    ->get();


        $monthlyIncome = (clone $query)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(total_fee) as total'))
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topStudents = (clone $query)
            ->select('student_id', DB::raw('SUM(total_fee) as total'))
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // ✅ Handle AJAX calls (for live dashboard filters)
        if (request()->ajax()) {
            return response()->json([
                'totalCollected' => $totalCollected,
                'totalPending' => $totalPending,
                'totalLateFee' => $totalLateFee,
                'totalDiscount' => $totalDiscount,
                'paymentByMethod' => $paymentByMethod,
                'paymentByType' => $paymentByType,
                'monthlyIncome' => $monthlyIncome,
                'topStudents' => $topStudents,
            ]);
        }

        return view('payment.summary', compact(
            'totalCollected', 'totalPending', 'totalLateFee', 'totalDiscount',
            'paymentByMethod', 'paymentByType', 'monthlyIncome', 'topStudents'
        ));
    }
}
