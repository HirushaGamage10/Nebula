@extends('inc.app')

@section('title', 'Student Payment Summary')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary mb-0">Student Payment Summary</h3>
                <a href="{{ route('payment.summary') }}" class="btn btn-outline-secondary btn-sm">
                    ← Back to Dashboard
                </a>
            </div>
            <p class="text-muted mb-4">
                Showing payments for <strong>Student ID:</strong> {{ $studentId }}
            </p>

            {{-- KPI Section --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="card border-0 shadow-sm p-3 text-center h-100"><h6 class="text-muted mb-1">Total Collected</h6><h4 class="fw-bold text-success">LKR {{ number_format($totalCollected, 2) }}</h4></div></div>
                <div class="col-md-3"><div class="card border-0 shadow-sm p-3 text-center h-100"><h6 class="text-muted mb-1">Pending Payments</h6><h4 class="fw-bold text-warning">LKR {{ number_format($totalPending, 2) }}</h4></div></div>
                <div class="col-md-3"><div class="card border-0 shadow-sm p-3 text-center h-100"><h6 class="text-muted mb-1">Late Fees</h6><h4 class="fw-bold text-danger">LKR {{ number_format($totalLateFee, 2) }}</h4></div></div>
                <div class="col-md-3"><div class="card border-0 shadow-sm p-3 text-center h-100"><h6 class="text-muted mb-1">Total Discounts</h6><h4 class="fw-bold text-info">LKR {{ number_format($totalDiscount, 2) }}</h4></div></div>
            </div>

            {{-- Charts Section --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-3 h-100">
                        <h6 class="fw-bold text-secondary mb-3">Payment by Method</h6>
                        <canvas id="methodChart" height="200"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-3 h-100">
                        <h6 class="fw-bold text-secondary mb-3">Payment by Type</h6>
                        <canvas id="typeChart" height="200"></canvas>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm p-3">
                        <h6 class="fw-bold text-secondary mb-3">Monthly Collection Trend</h6>
                        <canvas id="monthlyChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            {{-- Detailed Payments Table --}}
            <hr class="my-4">
            <h5 class="text-info mb-3">Recent Payments</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount (LKR)</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentRecords as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                {{-- ✅ Show Miscellaneous if installment_type is null but misc_category is set --}}
                                <td>
                                    @if(is_null($p->installment_type) && !is_null($p->misc_category))
                                        Miscellaneous ({{ ucfirst($p->misc_category) }})
                                    @else
                                        {{ ucfirst($p->installment_type ?? 'Unknown') }}
                                    @endif
                                </td>

                                <td>{{ ucfirst($p->payment_method ?? '-') }}</td>
                                <td>{{ number_format($p->total_fee, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $p->status == 'paid' ? 'success' : ($p->status == 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($p->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const paymentByMethod = @json($paymentByMethod);
    const paymentByType   = @json($paymentByType);
    const monthlyIncome   = @json($monthlyIncome);

    // ---- Payment by Method (Pie) ----
    new Chart(document.getElementById('methodChart'), {
        type: 'pie',
        data: {
            labels: paymentByMethod.map(p => p.payment_method ?? 'N/A'),
            datasets: [{
                data: paymentByMethod.map(p => p.total),
                backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b']
            }]
        }
    });

    // ---- Payment by Type (Doughnut) ----
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            // ✅ Use the alias `type` from controller + prettify Misc
            labels: paymentByType.map(p => p.type),

            datasets: [{
                data: paymentByType.map(p => p.total),
                backgroundColor: ['#36b9cc','#f6c23e','#e74a3b','#858796','#20c997']
            }]
        },
        options: {
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // ---- Monthly Collection (Line) ----
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyIncome.map(p => p.month),
            datasets: [{
                label: 'Monthly Income (LKR)',
                data: monthlyIncome.map(p => p.total),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection
