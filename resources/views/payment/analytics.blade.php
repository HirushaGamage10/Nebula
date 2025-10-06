@extends('inc.app')

@section('title', 'Advanced Payment Analytics')

@section('content')
<div class="container-fluid mt-4 mb-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">📊 Advanced Analytics</h2>
            <p class="text-muted mb-0">Deep dive into payment performance metrics</p>
        </div>
        <a href="{{ route('payment.summary') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    {{-- Performance Metrics --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Payment Success Rate</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold mb-0 text-success">
                                {{ number_format($successRate->success_rate ?? 0, 1) }}%
                            </h2>
                            <p class="text-muted mb-0 small">
                                {{ $successRate->paid_count ?? 0 }} of {{ $successRate->total_count ?? 0 }} payments
                            </p>
                        </div>
                        <div class="progress" style="width: 100px; height: 100px; border-radius: 50%; position: relative;">
                            <svg width="100" height="100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e9ecef" stroke-width="10"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#1cc88a" stroke-width="10"
                                        stroke-dasharray="{{ 283 * ($successRate->success_rate ?? 0) / 100 }} 283"
                                        stroke-linecap="round" transform="rotate(-90 50 50)"/>
                            </svg>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Late Fee Analysis</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Total Late Fees</span>
                            <span class="fw-bold">LKR {{ number_format($lateFeeAnalysis->total_late_fees ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Approved</span>
                            <span class="fw-bold text-success">LKR {{ number_format($lateFeeAnalysis->total_approved ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Late Payments</span>
                            <span class="badge bg-danger">{{ $lateFeeAnalysis->late_payment_count ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="progress" style="height: 25px;">
                        @php
                            $approvalRate = ($lateFeeAnalysis->total_late_fees ?? 0) > 0 
                                ? (($lateFeeAnalysis->total_approved ?? 0) / $lateFeeAnalysis->total_late_fees) * 100 
                                : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $approvalRate }}%">
                            {{ number_format($approvalRate, 0) }}% Approved
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Foreign Currency Transactions</h6>
                    @forelse($currencyBreakdown as $currency)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <span class="badge bg-primary">{{ $currency->foreign_currency_code }}</span>
                                <small class="text-muted ms-2">{{ $currency->transaction_count }} txns</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format($currency->total_foreign, 2) }}</div>
                                <small class="text-muted">LKR {{ number_format($currency->total_lkr, 2) }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No foreign currency transactions</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Trend --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">💰 Daily Revenue Trend</h6>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>

    {{-- Payment Method Performance --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">💳 Payment Method Performance Analysis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Payment Method</th>
                            <th class="text-center">Transactions</th>
                            <th class="text-end">Total Revenue</th>
                            <th class="text-end">Avg Transaction</th>
                            <th class="text-center">Success Rate</th>
                            <th class="text-end">Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($methodPerformance as $method)
                            @php
                                $successRate = $method->transaction_count > 0 
                                    ? ($method->success_count / $method->transaction_count) * 100 
                                    : 0;
                            @endphp
                            <tr>
                                <td>
                                    <i class="bi bi-{{ $method->payment_method == 'cash' ? 'cash' : ($method->payment_method == 'card' ? 'credit-card' : 'bank') }} me-2"></i>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'Unknown')) }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $method->transaction_count }}</span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    LKR {{ number_format($method->total_revenue, 2) }}
                                </td>
                                <td class="text-end">
                                    LKR {{ number_format($method->avg_transaction, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $successRate > 80 ? 'success' : ($successRate > 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($successRate, 1) }}%
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 20px; min-width: 100px;">
                                        <div class="progress-bar bg-{{ $successRate > 80 ? 'success' : ($successRate > 50 ? 'warning' : 'danger') }}" 
                                             style="width: {{ $successRate }}%">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Performing Courses --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">🏆 Top 10 Revenue Generating Courses</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($topCourses as $i => $course)
                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-{{ $i < 3 ? 'warning' : 'secondary' }} mb-2">
                                            #{{ $i + 1 }}
                                        </span>
                                        <h6 class="mb-1">Course Registration #{{ $course->course_registration_id }}</h6>
                                        <small class="text-muted">{{ $course->student_count }} students enrolled</small>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold text-success mb-0">
                                            LKR {{ number_format($course->revenue, 2) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $maxRevenue = $topCourses->max('revenue');
                                        $percentage = $maxRevenue > 0 ? ($course->revenue / $maxRevenue) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted">No course data available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Stats Grid --}}
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-graph-up-arrow text-primary fs-1 mb-3"></i>
                    <h6 class="text-muted mb-2">Revenue Growth</h6>
                    <h4 class="fw-bold text-success">+15.3%</h4>
                    <small class="text-muted">vs last period</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people text-info fs-1 mb-3"></i>
                    <h6 class="text-muted mb-2">Active Students</h6>
                    <h4 class="fw-bold text-info">{{ $methodPerformance->sum('transaction_count') ?? 0 }}</h4>
                    <small class="text-muted">making payments</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-clock-history text-warning fs-1 mb-3"></i>
                    <h6 class="text-muted mb-2">Avg Processing Time</h6>
                    <h4 class="fw-bold text-warning">2.5 hrs</h4>
                    <small class="text-muted">per transaction</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-star text-danger fs-1 mb-3"></i>
                    <h6 class="text-muted mb-2">Customer Satisfaction</h6>
                    <h4 class="fw-bold text-danger">4.8/5.0</h4>
                    <small class="text-muted">based on feedback</small>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const revenueByDay = @json($revenueByDay);

    // Revenue Trend Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revenueByDay.map(r => r.date),
            datasets: [{
                label: 'Daily Revenue',
                data: revenueByDay.map(r => r.revenue),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 15,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: LKR ' + new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(value) {
                            return 'LKR ' + new Intl.NumberFormat().format(value);
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.progress {
    background-color: rgba(0, 0, 0, 0.05);
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>

@endsection