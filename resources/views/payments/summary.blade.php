@extends('inc.app')

@section('title', 'Payment Dashboard - Advanced Analytics')

@section('content')
<div id="payment-summary" class="container-fluid mt-4 mb-5">
    {{-- Header with Actions --}}
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 text-white">
            <div>
                <h2 class="mb-1">💰 Payment Analytics Dashboard</h2>
                <p class="text-white-50 mb-0">Real-time insights and comprehensive reports</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('payment.analytics') }}" class="btn btn-outline-light">
                    <i class="bi bi-graph-up"></i> Advanced Analytics
                </a>
                <a href="{{ route('payment.comparison') }}" class="btn btn-outline-light">
                    <i class="bi bi-bar-chart"></i> Comparison
                </a>
                <button class="btn btn-light btn-export-data">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Time Range</label>
                    <select class="form-select" id="rangeFilter" name="range">
                        <option value="1w">Last Week</option>
                        <option value="1m">Last Month</option>
                        <option value="3m">Last 3 Months</option>
                        <option value="6m">Last 6 Months</option>
                        <option value="1y" selected>Last Year</option>
                        <option value="2y">Last 2 Years</option>
                        <option value="5y">Last 5 Years</option>
                        <option value="10y">All Time</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Payment Method</label>
                    <select class="form-select" id="methodFilter" name="payment_method">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select class="form-select" id="statusFilter" name="status">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Student ID</label>
                    <input type="text" class="form-control" id="studentFilter" name="student_id" placeholder="Enter Student ID">
                </div>
            </div>
            <div class="mt-3 text-end">
                <button class="btn btn-sm btn-primary btn-apply-filters">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <button class="btn btn-sm btn-outline-secondary btn-reset-filters">
                    <i class="bi bi-x-circle"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Primary KPIs --}}
    <div class="row g-3 mb-4" id="kpiSection">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Total Collected</p>
                            <h3 class="fw-bold mb-0">LKR {{ number_format($totalCollected, 2) }}</h3>
                            <small class="opacity-75">{{ $totalTransactions ?? 0 }} transactions</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Pending Payments</p>
                            <h3 class="fw-bold mb-0">LKR {{ number_format($totalPending, 2) }}</h3>
                            <small class="opacity-75">Awaiting collection</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Average Transaction</p>
                            <h3 class="fw-bold mb-0">LKR {{ number_format($averageTransaction ?? 0, 2) }}</h3>
                            <small class="opacity-75">Per payment</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Late Fees</p>
                            <h3 class="fw-bold mb-0">LKR {{ number_format($totalLateFee, 2) }}</h3>
                            <small class="opacity-75">Total penalties</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-tag text-success fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Total Discounts</h6>
                    <h5 class="fw-bold text-success">LKR {{ number_format($totalDiscount, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-percent text-info fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">SSCL Tax</h6>
                    <h5 class="fw-bold text-info">LKR {{ number_format($ssclTaxTotal ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bank text-warning fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Bank Charges</h6>
                    <h5 class="fw-bold text-warning">LKR {{ number_format($bankChargesTotal ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-exchange text-primary fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Transactions</h6>
                    <h5 class="fw-bold text-primary">{{ $totalTransactions ?? 0 }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0">Sri Lanka Student Distribution</h6>
                        <small class="text-muted">District-wise registered student count</small>
                    </div>
                    <div class="d-flex gap-3 small text-muted">
                        <span><strong id="district-map-total-students">0</strong> students</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="district-map-layout">
                        <div class="district-map-panel">
                            <div class="district-map-shell">
                                <img src="{{ asset('images/general/sri-lanka-location-map.svg') }}" class="district-real-map-image" alt="Sri Lanka map">
                                <div class="district-map-markers" id="districtMapMarkers"></div>
                            </div>
                            <div class="district-map-legend mt-3">
                                <span class="legend-title">Student count</span>
                                <div class="legend-scale">
                                    <span>Low</span>
                                    <div class="legend-bar"></div>
                                    <span>High</span>
                                </div>
                            </div>
                        </div>
                        <div class="district-map-insights">
                            <div class="district-highlight-card" id="districtHighlightCard">
                                <div class="district-highlight-label">Selected district</div>
                                <div class="district-highlight-name">All districts</div>
                                <div class="district-highlight-stats">
                                    <div>
                                        <span>Students</span>
                                        <strong id="district-highlight-students">0</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>District</th>
                                            <th class="text-end">Students</th>
                                        </tr>
                                    </thead>
                                    <tbody id="districtAnalyticsTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">Area Insights</h6>
                </div>
                <div class="card-body">
                    <div id="districtTopList" class="district-top-list"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📊 Monthly Collection Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📈 Payment Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">💳 Payment Methods</h6>
                </div>
                <div class="card-body">
                    <canvas id="methodChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📋 Payment Types</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📅 Weekly Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Tables Row --}}
    <div class="row g-4 mb-4">
        {{-- Top Students --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">🏆 Top 10 Paying Students</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Student ID</th>
                                    <th>Payments</th>
                                    <th class="text-end">Total Amount</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topStudents as $i => $student)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $i < 3 ? 'warning' : 'secondary' }}">
                                                {{ $i + 1 }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">{{ $student->student_id }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $student->payment_count ?? 0 }} txns
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            LKR {{ number_format($student->total, 2) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('payment.summary.student', $student->student_id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No student data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Payments --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">🕐 Recent Transactions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments ?? [] as $payment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('payment.summary.student', $payment->student_id) }}" 
                                               class="text-decoration-none">
                                                {{ $payment->student_id }}
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                @if(is_null($payment->installment_type) && !is_null($payment->misc_category))
                                                    Misc ({{ ucfirst($payment->misc_category) }})
                                                @else
                                                    {{ ucfirst($payment->installment_type ?? 'Unknown') }}
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            {{ number_format($payment->total_fee, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ ($payment->status ?? 'unknown') == 'paid' ? 'success' : (($payment->status ?? 'unknown') == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($payment->status ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No recent payments
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script nonce="{{ $cspNonce }}" src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script nonce="{{ $cspNonce }}">
// Event delegation for buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-export-data')) {
        exportData();
    } else if (e.target.closest('.btn-apply-filters')) {
        applyFilters();
    } else if (e.target.closest('.btn-reset-filters')) {
        resetFilters();
    }
});

// ========== FILTER FUNCTIONS (FIXED) ==========
function applyFilters() {
    const range = document.getElementById('rangeFilter').value;
    const method = document.getElementById('methodFilter').value;
    const status = document.getElementById('statusFilter').value;
    const studentId = document.getElementById('studentFilter').value;

    // Build query parameters
    const params = new URLSearchParams();
    params.append('range', range);
    if (method) params.append('payment_method', method);
    if (status) params.append('status', status);
    if (studentId) params.append('student_id', studentId);

    // Show loading indicator
    showLoading();

    // Redirect to the same page with query parameters
    window.location.href = `{{ route('payment.summary') }}?${params.toString()}`;
}

function resetFilters() {
    document.getElementById('rangeFilter').value = '1y';
    document.getElementById('methodFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('studentFilter').value = '';
    
    // Redirect to clean URL
    window.location.href = '{{ route("payment.summary") }}';
}

function exportData() {
    const range = document.getElementById('rangeFilter').value;
    const method = document.getElementById('methodFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    params.append('format', 'csv');
    params.append('range', range);
    if (method) params.append('payment_method', method);
    if (status) params.append('status', status);
    
    window.location.href = `{{ route('payment.export') }}?${params.toString()}`;
}

function showLoading() {
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:9999;';
    document.body.appendChild(overlay);
}

// ========== PRESERVE FILTERS ON PAGE LOAD ==========
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Restore filter values from URL
    if (urlParams.has('range')) {
        document.getElementById('rangeFilter').value = urlParams.get('range');
    }
    if (urlParams.has('payment_method')) {
        document.getElementById('methodFilter').value = urlParams.get('payment_method');
    }
    if (urlParams.has('status')) {
        document.getElementById('statusFilter').value = urlParams.get('status');
    }
    if (urlParams.has('student_id')) {
        document.getElementById('studentFilter').value = urlParams.get('student_id');
    }

    const rangeFilter = document.getElementById('rangeFilter');
    if (rangeFilter) {
        rangeFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
});

// ========== CHART INITIALIZATION ==========
document.addEventListener("DOMContentLoaded", () => {
    const paymentByMethod = @json($paymentByMethod);
    const paymentByType = @json($paymentByType);
    const paymentByStatus = @json($paymentByStatus ?? []);
    const monthlyIncome = @json($monthlyIncome);
    const weeklyTrend = @json($weeklyTrend ?? []);
    const districtAnalytics = @json($districtAnalytics ?? []);

    const districtCoordinates = {
        'Jaffna': { lat: 9.6615, lng: 80.0255 },
        'Kilinochchi': { lat: 9.3803, lng: 80.4088 },
        'Mannar': { lat: 8.9806, lng: 79.9042 },
        'Mullaitivu': { lat: 9.2671, lng: 80.8142 },
        'Vavuniya': { lat: 8.7514, lng: 80.4971 },
        'Trincomalee': { lat: 8.5874, lng: 81.2152 },
        'Anuradhapura': { lat: 8.3114, lng: 80.4037 },
        'Puttalam': { lat: 8.0362, lng: 79.8283 },
        'Kurunegala': { lat: 7.4863, lng: 80.3647 },
        'Polonnaruwa': { lat: 7.9403, lng: 81.0188 },
        'Matale': { lat: 7.4675, lng: 80.6234 },
        'Kandy': { lat: 7.2906, lng: 80.6337 },
        'Kegalle': { lat: 7.2513, lng: 80.3464 },
        'Nuwara Eliya': { lat: 6.9497, lng: 80.7891 },
        'Badulla': { lat: 6.9934, lng: 81.0550 },
        'Gampaha': { lat: 7.0873, lng: 79.9990 },
        'Colombo': { lat: 6.9271, lng: 79.8612 },
        'Kalutara': { lat: 6.5854, lng: 79.9607 },
        'Ratnapura': { lat: 6.6828, lng: 80.3992 },
        'Monaragala': { lat: 6.8728, lng: 81.3507 },
        'Ampara': { lat: 7.2917, lng: 81.6724 },
        'Batticaloa': { lat: 7.7102, lng: 81.6924 },
        'Galle': { lat: 6.0535, lng: 80.2210 },
        'Matara': { lat: 5.9549, lng: 80.5540 },
        'Hambantota': { lat: 6.1241, lng: 81.1185 }
    };

    const districtNames = [
        'Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle',
        'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle',
        'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Monaragala',
        'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura',
        'Trincomalee', 'Vavuniya'
    ];

    const districtMapData = districtNames.map(name => {
        const match = districtAnalytics.find(item => item.district === name);
        return {
            district: name,
            student_count: Number(match?.student_count || 0),
            coordinates: districtCoordinates[name]
        };
    });

    const totalDistrictStudents = districtMapData.reduce((sum, item) => sum + item.student_count, 0);

    // Bounds for Wikimedia Sri Lanka location map (equirectangular)
    const mapGeoBounds = {
        north: 10.2,
        south: 5.5,
        west: 79.2,
        east: 82.3
    };

    const nf = new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function formatCurrency(value) {
        return `LKR ${nf.format(Number(value || 0))}`;
    }

    function renderDistrictMap(items) {
        const markerLayer = document.getElementById('districtMapMarkers');
        const tableBody = document.getElementById('districtAnalyticsTableBody');
        const topList = document.getElementById('districtTopList');
        if (!tableBody || !topList || !markerLayer) {
            return;
        }

        document.getElementById('district-map-total-students').textContent = totalDistrictStudents.toLocaleString();

        const maxStudents = Math.max(...items.map(item => item.student_count), 1);
        markerLayer.innerHTML = '';
        tableBody.innerHTML = '';

        items.forEach(item => {
            if (!item.coordinates || typeof item.coordinates.lat !== 'number' || typeof item.coordinates.lng !== 'number') {
                return;
            }

            const intensity = item.student_count > 0 ? (item.student_count / maxStudents) : 0;
            const xPct = ((item.coordinates.lng - mapGeoBounds.west) / (mapGeoBounds.east - mapGeoBounds.west)) * 100;
            const yPct = ((mapGeoBounds.north - item.coordinates.lat) / (mapGeoBounds.north - mapGeoBounds.south)) * 100;

            const size = item.student_count > 0 ? (10 + Math.round(intensity * 16)) : 8;
            const marker = document.createElement('button');
            marker.type = 'button';
            marker.className = 'district-marker';
            marker.style.left = `${xPct}%`;
            marker.style.top = `${yPct}%`;
            marker.style.width = `${size}px`;
            marker.style.height = `${size}px`;
            marker.style.background = item.student_count > 0
                ? `rgba(29, 78, 216, ${0.35 + intensity * 0.5})`
                : 'rgba(148, 163, 184, 0.5)';
            marker.style.borderColor = item.student_count > 0 ? '#1d4ed8' : '#94a3b8';
            marker.title = `${item.district}: ${item.student_count.toLocaleString()} students`;
            marker.setAttribute('aria-label', marker.title);
            marker.addEventListener('mouseenter', () => updateDistrictHighlight(item));
            marker.addEventListener('focus', () => updateDistrictHighlight(item));
            marker.addEventListener('click', () => updateDistrictHighlight(item));
            markerLayer.appendChild(marker);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.district}</td>
                <td class="text-end">${item.student_count.toLocaleString()}</td>
            `;
            row.addEventListener('mouseenter', () => updateDistrictHighlight(item));
            row.addEventListener('click', () => updateDistrictHighlight(item));
            tableBody.appendChild(row);
        });

        const ranked = [...items]
            .filter(item => item.student_count > 0)
            .sort((a, b) => b.student_count - a.student_count)
            .slice(0, 6);

        topList.innerHTML = ranked.length
            ? ranked.map((item, index) => `
                <div class="district-top-item">
                    <div class="district-top-rank">${index + 1}</div>
                    <div class="district-top-content">
                        <div class="district-top-name">${item.district}</div>
                        <div class="district-top-meta">${item.student_count} students</div>
                    </div>
                    <div class="district-top-amount">${item.student_count}</div>
                </div>
            `).join('')
            : '<div class="text-muted small">No district activity found for the selected filters.</div>';

        const initial = ranked[0] || items[0] || {
            district: 'All districts',
            student_count: totalDistrictStudents
        };
        updateDistrictHighlight(initial);
    }

    function updateDistrictHighlight(item) {
        const nameEl = document.querySelector('.district-highlight-name');
        if (!nameEl) {
            return;
        }

        nameEl.textContent = item.district;
        document.getElementById('district-highlight-students').textContent = Number(item.student_count || 0).toLocaleString();
    }

    renderDistrictMap(districtMapData);

    // Chart.js default options
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // ---- Monthly Collection Trend (Line + Bar) ----
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyIncome.map(p => p.month),
            datasets: [{
                label: 'Paid',
                data: monthlyIncome.map(p => p.paid),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2
            }, {
                label: 'Pending',
                data: monthlyIncome.map(p => p.pending),
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 15 }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed.y);
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

    // ---- Payment Status (Doughnut) ----
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: paymentByStatus.map(p => p.status ? p.status.charAt(0).toUpperCase() + p.status.slice(1) : 'Unknown'),
            datasets: [{
                data: paymentByStatus.map(p => p.total),
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed) + 
                                   ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // ---- Payment Methods (Pie) ----
    new Chart(document.getElementById('methodChart'), {
        type: 'pie',
        data: {
            labels: paymentByMethod.map(p => {
                const methods = {
                    'cash': 'Cash',
                    'cheque': 'Cheque',
                    'bank_transfer': 'Bank Transfer',
                    'online': 'Online',
                    'card': 'Card'
                };
                return methods[p.payment_method] || p.payment_method || 'Unknown';
            }),
            datasets: [{
                data: paymentByMethod.map(p => p.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, usePointStyle: true, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed);
                        }
                    }
                }
            }
        }
    });

    // ---- Payment Types (Doughnut) ----
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: paymentByType.map(p => p.type || 'Unknown'),
            datasets: [{
                data: paymentByType.map(p => p.total),
                backgroundColor: ['#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#20c997'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, usePointStyle: true, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed);
                        }
                    }
                }
            }
        }
    });

    // ---- Weekly Trend (Bar) ----
    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weeklyTrend.map(p => 'Week ' + p.week),
            datasets: [{
                label: 'Weekly Revenue',
                data: weeklyTrend.map(p => p.total),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'LKR ' + new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<style nonce="{{ $cspNonce }}">
.payment-summary-reset{} /* anchor for quick search */
/* ---- Page background: force clean white and remove any image for this page only ---- */
/* body, .app-content, .content, .content-wrapper, main, #payment-summary {
    background-color: #ffffff !important;
    background-image: none !important;
} */

/* ---- Make all sections/cards light and airy ---- */
#payment-summary .card {
    background-color: #ffffff;
    border: 1px solid #eef2f7;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

#payment-summary .card-header {
    background-color: #ffffff !important;
    border-bottom: 1px solid #eef2f7;
}

/* ---- KPI section: replace heavy gradients with light tones ---- */
#payment-summary #kpiSection .card.bg-gradient {
    background: #f8fafc !important;
}

/* Ensure text is dark on light KPI cards (override .text-white) */
#payment-summary #kpiSection .card.bg-gradient .card-body,
#payment-summary #kpiSection .card.bg-gradient .card-body h3,
#payment-summary #kpiSection .card.bg-gradient .card-body p,
#payment-summary #kpiSection .card.bg-gradient .card-body small {
    color: #0f172a !important;
}

/* Subtle muted copy inside KPI cards */
#payment-summary #kpiSection .card.bg-gradient .opacity-75,
#payment-summary #kpiSection .card.bg-gradient small {
    color: #64748b !important;
    opacity: 1 !important;
}

/* Icon bubbles and accent borders per KPI tile */
#payment-summary #kpiSection > .col-xl-3:nth-child(1) .card { border-left: 3px solid #0d6efd; }
#payment-summary #kpiSection > .col-xl-3:nth-child(1) .bg-white.bg-opacity-25 { background: rgba(13,110,253,.12) !important; color: #0d6efd !important; }

#payment-summary #kpiSection > .col-xl-3:nth-child(2) .card { border-left: 3px solid #f59f00; }
#payment-summary #kpiSection > .col-xl-3:nth-child(2) .bg-white.bg-opacity-25 { background: rgba(245,159,0,.12) !important; color: #f59f00 !important; }

#payment-summary #kpiSection > .col-xl-3:nth-child(3) .card { border-left: 3px solid #0dcaf0; }
#payment-summary #kpiSection > .col-xl-3:nth-child(3) .bg-white.bg-opacity-25 { background: rgba(13,202,240,.12) !important; color: #0dcaf0 !important; }

#payment-summary #kpiSection > .col-xl-3:nth-child(4) .card { border-left: 3px solid #dc3545; }
#payment-summary #kpiSection > .col-xl-3:nth-child(4) .bg-white.bg-opacity-25 { background: rgba(220,53,69,.12) !important; color: #dc3545 !important; }

/* Tables: keep headers light */
#payment-summary table thead {
    background-color: #f8f9fc;
}

.bg-gradient {
    background-size: cover;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.district-map-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.75fr);
    gap: 1.5rem;
    align-items: start;
}

.district-map-shell {
    position: relative;
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
}

.district-real-map-image {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 20px;
    border: 1px solid #c7d2fe;
    box-shadow: 0 18px 35px rgba(59, 130, 246, 0.12);
    background: #f8fafc;
}

.district-map-markers {
    position: absolute;
    inset: 0;
    border-radius: 20px;
}

.district-marker {
    position: absolute;
    transform: translate(-50%, -50%);
    border: 2px solid transparent;
    border-radius: 999px;
    box-shadow: 0 8px 18px rgba(30, 41, 59, 0.14);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    cursor: pointer;
}

.district-marker:hover,
.district-marker:focus {
    transform: translate(-50%, -50%) scale(1.12);
    box-shadow: 0 12px 26px rgba(30, 41, 59, 0.2);
    outline: none;
}

.district-map-legend {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.district-map-legend .legend-title {
    font-size: 0.85rem;
    color: #64748b;
}

.district-map-legend .legend-scale {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    justify-content: flex-end;
    color: #64748b;
    font-size: 0.78rem;
}

.district-map-legend .legend-bar {
    width: 140px;
    height: 10px;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(148, 163, 184, 0.3) 0%, rgba(13, 110, 253, 0.95) 100%);
}

.district-highlight-card {
    background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
    border: 1px solid #dbeafe;
    border-radius: 18px;
    padding: 1rem 1.1rem;
}

.district-highlight-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    margin-bottom: 0.35rem;
}

.district-highlight-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.9rem;
}

.district-highlight-stats {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 0.75rem;
}

.district-highlight-stats span {
    display: block;
    font-size: 0.74rem;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.05em;
    margin-bottom: 0.2rem;
}

.district-highlight-stats strong {
    display: block;
    color: #0f172a;
    font-size: 1rem;
}

.district-top-list {
    display: grid;
    gap: 0.85rem;
}

.district-top-item {
    display: grid;
    grid-template-columns: 40px minmax(0, 1fr) auto;
    gap: 0.8rem;
    align-items: center;
    padding: 0.85rem 0.9rem;
    border-radius: 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.district-top-rank {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: #dbeafe;
    color: #1d4ed8;
    font-weight: 700;
}

.district-top-name {
    font-weight: 600;
    color: #0f172a;
}

.district-top-meta {
    font-size: 0.82rem;
    color: #64748b;
}

.district-top-amount {
    font-weight: 700;
    color: #0f766e;
    white-space: nowrap;
}

@media (max-width: 991.98px) {
    .district-map-layout {
        grid-template-columns: 1fr;
    }

    .district-highlight-stats {
        grid-template-columns: 1fr;
    }
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>

@endsection