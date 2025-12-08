@extends('inc.app')

@section('title', 'NEBULA | Program Administrator Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    .kpi-card {
        border-left: 4px solid;
        min-height: 140px;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .activity-item {
        padding: 12px;
        border-left: 3px solid #dee2e6;
        transition: all 0.2s;
    }
    .activity-item:hover {
        background: #f8f9fa;
        border-left-color: #667eea;
    }
    .action-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .time-filter-btn {
        padding: 6px 16px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        transition: all 0.2s;
    }
    .time-filter-btn.active {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }
    .modal-backdrop.show {
        opacity: 0.7;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">📊 Program Administrator Dashboard</h4>
                            <p class="text-muted mb-0">Comprehensive system overview and management</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshAllData()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="exportDashboard()">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center flex-wrap">
                        <span class="me-3 text-muted"><i class="fas fa-calendar-alt me-1"></i> Period:</span>
                        <button class="time-filter-btn" onclick="setTimePeriod('today')">Today</button>
                        <button class="time-filter-btn" onclick="setTimePeriod('week')">This Week</button>
                        <button class="time-filter-btn active" onclick="setTimePeriod('month')">This Month</button>
                        <button class="time-filter-btn" onclick="setTimePeriod('year')">This Year</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row 1 - Students -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-primary" onclick="showStudentDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="badge bg-primary">Active</span>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">Total Students</h5>
                    <h2 class="fw-bold mb-1" id="totalStudents">-</h2>
                    <div class="text-muted fs-13">
                        <span id="activeStudents">-</span> active students
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-success" onclick="showRegistrationDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <span class="badge bg-success">Live</span>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">Course Registrations</h5>
                    <h2 class="fw-bold mb-1" id="totalRegistrations">-</h2>
                    <div class="text-muted fs-13">
                        <span id="pendingRegistrations">-</span> pending
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-warning" onclick="showClearanceDetails()">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <span class="badge bg-warning">Action</span>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">Clearance Requests</h5>
                    <h2 class="fw-bold mb-1" id="pendingClearances">-</h2>
                    <div class="text-muted fs-13">
                        Pending approval
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
    <a href="{{ route('payment.summary') }}" style="text-decoration: none;">
        <div class="card kpi-card card-hover border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="ti ti-chart-pie"></i>
                    </div>
                    <span class="badge bg-info">Payments</span>
                </div>

                <h5 class="text-muted text-uppercase fs-12 mb-2">Payment Dashboard</h5>

                <h2 class="fw-bold mb-1">View</h2>

                <div class="text-muted fs-13">
                    Go to payment summary
                </div>
            </div>
        </div>
    </a>
</div>

    </div>

    <!-- KPI Cards Row 2 - Academic & System -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">Total Courses</h5>
                    <h2 class="fw-bold mb-1" id="totalCourses">-</h2>
                    <div class="text-muted fs-13">
                        <span id="activeIntakes">-</span> active intakes
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-secondary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">Attendance Today</h5>
                    <h2 class="fw-bold mb-1" id="attendanceToday">-</h2>
                    <div class="text-muted fs-13">
                        Records taken
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-dark bg-opacity-10 text-dark">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">System Users</h5>
                    <h2 class="fw-bold mb-1" id="totalUsers">-</h2>
                    <div class="text-muted fs-13">
                        Active accounts
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card card-hover border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <h5 class="text-muted text-uppercase fs-12 mb-2">New Students</h5>
                    <h2 class="fw-bold mb-1" id="newStudents">-</h2>
                    <div class="text-muted fs-13">
                        This period
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">📈 Registration Trend</h5>
                            <p class="text-muted mb-0">Last 12 months</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="registrationTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">🎓 Top Courses</h5>
                            <p class="text-muted mb-0">By registrations</p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="topCoursesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Items & Activities -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">⚡ Action Items</h5>
                            <p class="text-muted mb-0">Items requiring attention</p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshActionItems()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div id="actionItemsContainer" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card card-hover h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title mb-1">🔔 Recent Activities</h5>
                            <p class="text-muted mb-0">Latest system activities</p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshActivities()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div id="activitiesContainer" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card card-hover">
                <div class="card-body">
                    <h5 class="card-title mb-4">🔗 Quick Links</h5>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('student.registration') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-user-plus fa-2x text-primary mb-2"></i>
                                    <div class="fw-medium">Student Registration</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('course.registration') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-graduation-cap fa-2x text-success mb-2"></i>
                                    <div class="fw-medium">Course Registration</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('payment.plan') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-warning mb-2"></i>
                                    <div class="fw-medium">Payment Plans</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('all.clearance.management') }}" class="text-decoration-none">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="fas fa-clipboard-check fa-2x text-info mb-2"></i>
                                    <div class="fw-medium">Clearance Management</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👥 Student Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <canvas id="studentStatsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Registration Details Modal -->
<div class="modal fade" id="registrationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📝 Registration Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <canvas id="registrationStatsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Clearance Details Modal -->
<div class="modal fade" id="clearanceDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✅ Pending Clearances</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="clearanceTableContainer"></div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Details Modal -->
<div class="modal fade" id="financialDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">💰 Financial Overview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="text-muted">Total Revenue</h6>
                                <h3 id="modalTotalRevenue">-</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="text-muted">This Period</h6>
                                <h3 id="modalPeriodRevenue">-</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="text-muted">Pending</h6>
                                <h3 id="modalPendingAmount">-</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="latePaymentsContainer"></div>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentPeriod = 'month';
let chartInstances = {};

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    setInterval(() => loadDashboardData(), 300000); // Refresh every 5 minutes
});

function loadDashboardData() {
    fetchOverviewMetrics();
    fetchStudentStats();
    fetchCourseRegistrationStats();
    fetchRecentActivities();
    fetchActionItems();
}

function setTimePeriod(period) {
    currentPeriod = period;
    document.querySelectorAll('.time-filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    loadDashboardData();
}

async function fetchOverviewMetrics() {
    try {
        const response = await fetch(`/api/admin-l1/overview?period=${currentPeriod}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        document.getElementById('totalStudents').textContent = data.total_students?.toLocaleString() || '0';
        document.getElementById('activeStudents').textContent = data.active_students?.toLocaleString() || '0';
        document.getElementById('totalRegistrations').textContent = data.total_registrations?.toLocaleString() || '0';
        document.getElementById('pendingRegistrations').textContent = data.pending_registrations?.toLocaleString() || '0';
        document.getElementById('pendingClearances').textContent = data.pending_clearances?.toLocaleString() || '0';

        document.getElementById('totalCourses').textContent = data.total_courses?.toLocaleString() || '0';
        document.getElementById('activeIntakes').textContent = data.active_intakes?.toLocaleString() || '0';
        document.getElementById('attendanceToday').textContent = data.attendance_taken_today?.toLocaleString() || '0';
        document.getElementById('totalUsers').textContent = data.total_users?.toLocaleString() || '0';
        document.getElementById('newStudents').textContent = data.new_students_this_period?.toLocaleString() || '0';
    } catch (error) {
        console.error('Error fetching overview:', error);
    }
}

async function fetchStudentStats() {
    try {
        const response = await fetch(`/api/admin-l1/student-stats?period=${currentPeriod}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.registration_trend) {
            const ctx = document.getElementById('registrationTrendChart');
            if (chartInstances.registrationTrend) chartInstances.registrationTrend.destroy();
            
            chartInstances.registrationTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.registration_trend.map(item => item.month),
                    datasets: [{
                        label: 'Students',
                        data: data.registration_trend.map(item => item.count),
                        borderColor: 'rgba(102, 126, 234, 1)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    } catch (error) {
        console.error('Error fetching student stats:', error);
    }
}

async function fetchCourseRegistrationStats() {
    try {
        const response = await fetch(`/api/admin-l1/course-registration-stats?period=${currentPeriod}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.top_courses) {
            const ctx = document.getElementById('topCoursesChart');
            if (chartInstances.topCourses) chartInstances.topCourses.destroy();
            
            chartInstances.topCourses = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.top_courses.map(item => item.course_name.substring(0, 20) + '...'),
                    datasets: [{
                        label: 'Registrations',
                        data: data.top_courses.map(item => item.registrations),
                        backgroundColor: 'rgba(118, 75, 162, 0.8)'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    } catch (error) {
        console.error('Error fetching course stats:', error);
    }
}

async function fetchRecentActivities() {
    try {
        const response = await fetch(`/api/admin-l1/recent-activities?limit=20`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        renderActivities(data);
    } catch (error) {
        console.error('Error fetching activities:', error);
    }
}

function renderActivities(activities) {
    const container = document.getElementById('activitiesContainer');
    if (!activities || activities.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-3">No recent activities</p>';
        return;
    }
    
    let html = '';
    activities.forEach(activity => {
        const icons = {
            'Student Registration': 'fa-user-plus text-primary',
            'Course Registration': 'fa-graduation-cap text-success',
            'Payment': 'fa-money-bill-wave text-info',
            'Clearance Request': 'fa-clipboard-check text-warning'
        };
        
        html += `
            <div class="activity-item mb-2">
                <div class="d-flex align-items-start">
                    <i class="fas ${icons[activity.type] || 'fa-circle'} me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="fw-medium">${activity.description}</div>
                        <small class="text-muted">${new Date(activity.created_at).toLocaleString()}</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

async function fetchActionItems() {
    try {
        const response = await fetch(`/api/admin-l1/action-items`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        renderActionItems(data);
    } catch (error) {
        console.error('Error fetching action items:', error);
    }
}

function renderActionItems(items) {
    const container = document.getElementById('actionItemsContainer');
    let html = '';
    
    if (items.pending_registrations && items.pending_registrations.length > 0) {
        html += '<h6 class="text-muted mb-3">📋 Pending Registrations</h6>';
        items.pending_registrations.forEach(reg => {
            html += `
                <div class="activity-item mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-medium">${reg.student_name}</div>
                            <small class="text-muted">${reg.course_name}</small>
                        </div>
                        <span class="action-badge bg-warning text-dark">Pending</span>
                    </div>
                </div>
            `;
        });
    }
    
    if (items.pending_clearances && items.pending_clearances.length > 0) {
        html += '<h6 class="text-muted mb-3 mt-4">✅ Pending Clearances</h6>';
        items.pending_clearances.forEach(clearance => {
            html += `
                <div class="activity-item mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-medium">${clearance.student_name}</div>
                            <small class="text-muted">${clearance.clearance_type} clearance</small>
                        </div>
                        <span class="action-badge bg-info text-white">Review</span>
                    </div>
                </div>
            `;
        });
    }
    
    container.innerHTML = html || '<p class="text-muted text-center py-3">No action items</p>';
}

async function showStudentDetails() {
    const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
    modal.show();
    
    try {
        const response = await fetch(`/api/admin-l1/student-stats?period=${currentPeriod}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        const ctx = document.getElementById('studentStatsChart');
        if (chartInstances.studentStats) chartInstances.studentStats.destroy();
        
        chartInstances.studentStats = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.by_location.map(item => item.institute_location),
                datasets: [{
                    data: data.by_location.map(item => item.count),
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ]
                }]
            }
        });
    } catch (error) {
        console.error('Error loading student details:', error);
    }
}

async function showRegistrationDetails() {
    const modal = new bootstrap.Modal(document.getElementById('registrationDetailsModal'));
    modal.show();
    
    try {
        const response = await fetch(`/api/admin-l1/course-registration-stats?period=${currentPeriod}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        const ctx = document.getElementById('registrationStatsChart');
        if (chartInstances.registrationStats) chartInstances.registrationStats.destroy();
        
        chartInstances.registrationStats = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.by_status.map(item => item.status),
                datasets: [{
                    data: data.by_status.map(item => item.count),
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ]
                }]
            }
        });
    } catch (error) {
        console.error('Error loading registration details:', error);
    }
}

async function showClearanceDetails() {
    const modal = new bootstrap.Modal(document.getElementById('clearanceDetailsModal'));
    modal.show();
    
    try {
        const response = await fetch(`/api/admin-l1/clearance-stats`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        let html = '<table class="table table-hover"><thead><tr>' +
                   '<th>Student</th><th>Type</th><th>Course</th><th>Date</th><th>Status</th>' +
                   '</tr></thead><tbody>';
        
        data.pending_list.forEach(clearance => {
            html += `
                <tr>
                    <td>${clearance.student_name}</td>
                    <td><span class="badge bg-info">${clearance.clearance_type}</span></td>
                    <td>${clearance.course_name}</td>
                    <td>${new Date(clearance.created_at).toLocaleDateString()}</td>
                    <td><span class="badge bg-warning">${clearance.status}</span></td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        document.getElementById('clearanceTableContainer').innerHTML = html;
    } catch (error) {
        console.error('Error loading clearance details:', error);
    }
}

async function showFinancialDetails() {
    const modal = new bootstrap.Modal(document.getElementById('financialDetailsModal'));
    modal.show();
    
    try {
        const response = await fetch(`/api/admin-l1/financial-stats?period=${currentPeriod}`, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        document.getElementById('modalTotalRevenue').textContent = 
            'LKR ' + (data.revenue_summary.total_revenue?.toLocaleString() || '0');
        document.getElementById('modalPeriodRevenue').textContent = 
            'LKR ' + (data.revenue_summary.revenue_this_period?.toLocaleString() || '0');
        document.getElementById('modalPendingAmount').textContent = 
            'LKR ' + (data.revenue_summary.pending_amount?.toLocaleString() || '0');
        
        let html = '<h6 class="mt-4 mb-3">Late Payments</h6><table class="table table-hover"><thead><tr>' +
                   '<th>Student</th><th>Due Date</th><th>Amount</th><th>Late Fee</th></tr></thead><tbody>';
        
        data.late_payments.forEach(payment => {
            html += `
                <tr>
                    <td>${payment.student_name}</td>
                    <td>${new Date(payment.due_date).toLocaleDateString()}</td>
                    <td>LKR ${payment.amount?.toLocaleString()}</td>
                    <td class="text-danger">LKR ${payment.calculated_late_fee?.toLocaleString()}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        document.getElementById('latePaymentsContainer').innerHTML = html;
    } catch (error) {
        console.error('Error loading financial details:', error);
    }
}

function refreshAllData() {
    document.body.style.opacity = '0.6';
    loadDashboardData();
    setTimeout(() => { document.body.style.opacity = '1'; }, 1000);
}

function refreshActionItems() {
    fetchActionItems();
}

function refreshActivities() {
    fetchRecentActivities();
}

function exportDashboard() {
    alert('Export feature coming soon!');
}
</script>
@endsection