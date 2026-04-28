@extends('inc.app')

@section('title', 'NEBULA | Program Administrator (Level 02) Trainee Dashboard')

@section('content')
    <link nonce="{{ $cspNonce }}" rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link nonce="{{ $cspNonce }}" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">
    <script nonce="{{ $cspNonce }}" src="{{ asset('libs/chartjs/chart.min.js') }}"></script>

    <style nonce="{{ $cspNonce }}">
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .activity-item {
            padding: 12px;
            border-left: 3px solid #e9ecef;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            background: #f8f9fa;
            border-left-color: #667eea;
        }
        
        .badge-trainee {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-user-graduate me-2"></i>
                        Program Administrator (Level 02) Trainee
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
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #667eea !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Total Students</p>
                                <h3 class="mb-0 fw-bold" id="totalStudents">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #e7ebf9; color: #667eea;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #f093fb !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Active Intakes</p>
                                <h3 class="mb-0 fw-bold" id="activeIntakes">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #fde9fc; color: #f093fb;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #f5576c !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Today's Attendance</p>
                                <h3 class="mb-0 fw-bold" id="todayAttendance">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #ffebee; color: #f5576c;">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card kpi-card border-0 shadow-sm" style="border-left-color: #4facfe !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1 fw-500">Pending Results</p>
                                <h3 class="mb-0 fw-bold" id="pendingResults">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                            <div class="kpi-icon" style="background: #e3f5ff; color: #4facfe;">
                                <i class="fas fa-file-alt"></i>
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
                            <i class="fas fa-chart-line text-primary me-2"></i>
                            Attendance Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-pie text-success me-2"></i>
                            Academic Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Recent Activities
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="recentActivities">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-3">Loading activities...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function() {
            // Load overview metrics
            loadOverviewMetrics();
            loadAttendanceChart();
            loadPerformanceChart();
            loadRecentActivities();
        });

        function loadOverviewMetrics() {
            fetch('/api/program-admin-l2-trainee/overview')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalStudents').textContent = data.total_students || 0;
                    document.getElementById('activeIntakes').textContent = data.active_intakes || 0;
                    document.getElementById('todayAttendance').textContent = data.today_attendance || 0;
                    document.getElementById('pendingResults').textContent = data.pending_results || 0;
                })
                .catch(error => {
                    console.error('Error loading overview metrics:', error);
                    document.getElementById('totalStudents').textContent = '0';
                    document.getElementById('activeIntakes').textContent = '0';
                    document.getElementById('todayAttendance').textContent = '0';
                    document.getElementById('pendingResults').textContent = '0';
                });
        }

        function loadAttendanceChart() {
            fetch('/api/program-admin-l2-trainee/attendance-overview')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('attendanceChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.date),
                            datasets: [{
                                label: 'Attendance',
                                data: data.map(item => item.count),
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                tension: 0.4,
                                fill: true
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
                .catch(error => console.error('Error loading attendance chart:', error));
        }

        function loadPerformanceChart() {
            fetch('/api/program-admin-l2-trainee/academic-performance')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('performanceChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.map(item => item.grade),
                            datasets: [{
                                data: data.map(item => item.count),
                                backgroundColor: [
                                    '#667eea',
                                    '#f093fb',
                                    '#f5576c',
                                    '#4facfe',
                                    '#43e97b'
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
                .catch(error => console.error('Error loading performance chart:', error));
        }

        function loadRecentActivities() {
            fetch('/api/program-admin-l2-trainee/recent-activities')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentActivities');
                    if (data.length === 0) {
                        container.innerHTML = '<p class="text-muted text-center py-4">No recent activities</p>';
                        return;
                    }
                    
                    container.innerHTML = data.map(activity => `
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-${activity.type === 'attendance' ? 'clipboard-check' : 'file-alt'} text-primary me-2"></i>
                                    ${activity.description}
                                </div>
                                <small class="text-muted">${activity.time}</small>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading activities:', error);
                    document.getElementById('recentActivities').innerHTML = 
                        '<p class="text-danger text-center py-4">Error loading activities</p>';
                });
        }
    </script>
@endsection
