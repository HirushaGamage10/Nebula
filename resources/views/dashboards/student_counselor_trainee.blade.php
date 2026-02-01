@extends('inc.app')

@section('title', 'NEBULA | Student Counselor Trainee Dashboard')

@section('content')
    <link nonce="{{ $cspNonce }}" rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link nonce="{{ $cspNonce }}" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script nonce="{{ $cspNonce }}" src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style nonce="{{ $cspNonce }}">
        .dashboard-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .kpi-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        
        .kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .registration-item {
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .registration-item:hover {
            background: #f8f9fa;
            border-color: #4facfe;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-registered {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-trainee {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .growth-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .growth-positive {
            background: #d4edda;
            color: #155724;
        }
        
        .growth-negative {
            background: #f8d7da;
            color: #721c24;
        }
    </style>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-user-tie me-2"></i>
                        Student Counselor Trainee
                    </h2>
                    <p class="mb-0 opacity-75">Welcome back, {{ $user->name }} <span class="badge-trainee">TRAINEE</span></p>
                </div>
                <div class="text-end">
                    <div class="text-white-50 small">{{ now()->format('l, F j, Y') }}</div>
                    <div class="fs-5 fw-bold">{{ now()->format('h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #4facfe !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Total Registrations</p>
                                <h3 class="mb-0 fw-bold" id="totalRegistrations">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #e3f5ff; color: #4facfe;">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #00f2fe !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">This Month</p>
                                <h3 class="mb-0 fw-bold" id="thisMonth">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                                <div id="growthBadge"></div>
                            </div>
                            <div class="kpi-icon" style="background: #e0fcff; color: #00f2fe;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #43e97b !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Today's Registrations</p>
                                <h3 class="mb-0 fw-bold" id="todayRegistrations">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #e8fdf1; color: #43e97b;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #fa709a !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Active Students</p>
                                <h3 class="mb-0 fw-bold" id="activeStudents">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #ffebf2; color: #fa709a;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-bar text-primary me-2"></i>
                            Daily Registration Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-pie text-success me-2"></i>
                            Registrations by Location
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="locationChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Survey & Recent Registrations -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-bullhorn text-warning me-2"></i>
                            Marketing Channels
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="marketingChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-list text-info me-2"></i>
                            Recent Registrations
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div id="recentRegistrations">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-3">Loading registrations...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Load all data
            loadOverviewMetrics();
            loadTrendChart();
            loadLocationChart();
            loadMarketingChart();
            loadRecentRegistrations();
        });

        function loadOverviewMetrics() {
            fetch('/api/student-counselor-trainee/overview')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalRegistrations').textContent = data.total_registrations || 0;
                    document.getElementById('thisMonth').textContent = data.this_month || 0;
                    document.getElementById('todayRegistrations').textContent = data.today || 0;
                    document.getElementById('activeStudents').textContent = data.active_students || 0;
                    
                    // Show growth badge
                    const growth = data.growth_percentage || 0;
                    const growthClass = growth >= 0 ? 'growth-positive' : 'growth-negative';
                    const growthIcon = growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                    document.getElementById('growthBadge').innerHTML = `
                        <span class="growth-badge ${growthClass}">
                            <i class="fas ${growthIcon}"></i> ${Math.abs(growth).toFixed(1)}%
                        </span>
                    `;
                })
                .catch(error => {
                    console.error('Error loading overview metrics:', error);
                    document.getElementById('totalRegistrations').textContent = '0';
                    document.getElementById('thisMonth').textContent = '0';
                    document.getElementById('todayRegistrations').textContent = '0';
                    document.getElementById('activeStudents').textContent = '0';
                });
        }

        function loadTrendChart() {
            fetch('/api/student-counselor-trainee/daily-trend')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('trendChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.date),
                            datasets: [{
                                label: 'Registrations',
                                data: data.map(item => item.count),
                                backgroundColor: 'rgba(79, 172, 254, 0.8)',
                                borderColor: '#4facfe',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading trend chart:', error));
        }

        function loadLocationChart() {
            fetch('/api/student-counselor-trainee/location-data')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('locationChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.map(item => item.location),
                            datasets: [{
                                data: data.map(item => item.count),
                                backgroundColor: [
                                    '#4facfe',
                                    '#00f2fe',
                                    '#43e97b',
                                    '#fa709a'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading location chart:', error));
        }

        function loadMarketingChart() {
            fetch('/api/student-counselor-trainee/marketing-survey')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('marketingChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.map(item => item.channel),
                            datasets: [{
                                data: data.map(item => item.count),
                                backgroundColor: [
                                    '#667eea',
                                    '#f093fb',
                                    '#f5576c',
                                    '#4facfe',
                                    '#43e97b',
                                    '#fa709a'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error loading marketing chart:', error));
        }

        function loadRecentRegistrations() {
            fetch('/api/student-counselor-trainee/recent-registrations')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentRegistrations');
                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-muted text-center py-4">No recent registrations</p>';
                        return;
                    }
                    
                    container.innerHTML = data.map(reg => `
                        <div class="registration-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">${reg.student_name}</div>
                                    <div class="small text-muted">
                                        ${reg.course_name} | Intake: ${reg.intake}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="status-badge ${reg.status === 'Registered' ? 'status-registered' : 'status-pending'}">
                                        ${reg.status}
                                    </span>
                                    <div class="small text-muted mt-1">${reg.registration_date}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading registrations:', error);
                    document.getElementById('recentRegistrations').innerHTML = 
                        '<p class="text-danger text-center py-4">Error loading registrations</p>';
                });
        }
    </script>
@endsection
