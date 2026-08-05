<?php

namespace Tests\Unit;

use App\Http\Controllers\DGMDashboardController;
use App\Models\PaymentDetail;
use Carbon\Carbon;
use Tests\TestCase;

class DGMDashboardPaymentAggregationTest extends TestCase
{
    public function test_payment_contribution_uses_effective_date_and_json_partial_payments(): void
    {
        $controller = new DGMDashboardController();
        $payment = new PaymentDetail();
        $payment->created_at = Carbon::parse('2024-01-10');
        $payment->payment_effective_date = Carbon::parse('2024-02-15');
        $payment->total_fee = 1000;
        $payment->amount = 1000;
        $payment->partial_payments = json_encode([
            ['amount' => 250, 'date' => '2024-02-10'],
            ['amount' => 750, 'date' => '2024-03-05'],
        ]);

        $method = new \ReflectionMethod(DGMDashboardController::class, 'getPaymentContributionForPeriod');
        $method->setAccessible(true);

        $start = Carbon::parse('2024-02-01')->startOfDay();
        $end = Carbon::parse('2024-02-29')->endOfDay();

        $result = $method->invoke($controller, $payment, $start, $end);

        $this->assertSame(250.0, round($result, 2));
    }
}
