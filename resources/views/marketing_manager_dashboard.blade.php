@extends('inc.app')

@section('title', 'NEBULA | Marketing Manager Dashboard')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
        .gradient-border {
            border-image: linear-gradient(90deg, #667eea 0%, #764ba2 100%) 1;
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-registered {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .source-tag {
            display: inline-block;
            padding: 4px 10px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .kpi-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .kpi-card:hover {
            border-left-width: 6px;
        }
        
        .time-filter-btn {
            padding: 6px 16px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .time-filter-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }
        
        .time-filter-btn.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .chart-toggle-btn {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            transition: all 0.2s ease;
        }
        
        .chart-toggle-btn:hover {
            background: #f8f9fa;
        }
        
        .chart-toggle-btn.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .avatar-initial {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .data-loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .pulse-animation {
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="bg-white p-4 rounded shadow-sm mb-3">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="avatar-initial">
                <i class="fas fa-bullseye"></i>
            </div>
        </div>
        <div>
            <h4 class="mb-1 fw-bold text-dark">🎯 Marketing Manager Dashboard</h4>
            <p class="text-muted mb-0">Track campaign performance and student acquisition metrics</p>
        </div>
    </div>
</div>

                    <div class="d-flex align-items-center gap-2">
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

        <!-- Time Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-3 text-muted"><i class="fas fa-calendar-alt me-1"></i> Time Period:</span>
                            <div class="d-flex flex-wrap">
                                <button class="time-filter-btn active" onclick="setTimePeriod('today')">Today</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('week')">This Week</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('month')">This Month</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('quarter')">This Quarter</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('year')">This Year</button>
                                <div class="d-inline-block ms-2">
                                    <input type="date" id="customDate" class="form-control form-control-sm" style="width: 140px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary">Live</span>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">Total Registered Students</h5>
                        <h2 class="fw-bold mb-1" id="totalRegistered">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-chart-line me-1"></i> Active registrations
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-success bg-opacity-10 text-success">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div id="growthIndicator" style="display: none;">
                                <span id="growthIcon" class="me-1"></span>
                                <span id="growthValue" class="badge"></span>
                            </div>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">This Month</h5>
                        <h2 class="fw-bold mb-1" id="thisMonth">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-users me-1"></i> New registrations
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-database"></i>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning">All Time</span>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">Total Students</h5>
                        <h2 class="fw-bold mb-1" id="totalStudents">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-server me-1"></i> In database
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger">Previous</span>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">Last Month</h5>
                        <h2 class="fw-bold mb-1" id="lastMonth">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-history me-1"></i> Registrations
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-xl-8 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">📊 Marketing Survey Analysis</h5>
                                <p class="text-muted mb-0">Channel performance overview</p>
                            </div>
                            <select id="chartTypeSelect" class="form-select form-select-sm" style="width: auto;">
                                <option value="bar">Bar Chart</option>
                                <option value="line">Line Chart</option>
                                <option value="pie">Pie Chart</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <canvas id="marketingSurveyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">📍 Location Distribution</h5>
                                <p class="text-muted mb-0">Student registrations by region</p>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="chart-toggle-btn active" onclick="toggleLocationChart('doughnut')">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                                <button class="chart-toggle-btn" onclick="toggleLocationChart('pie')">
                                    <i class="fas fa-chart-pie"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <div class="col-xl-6 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">📈 12-Month Registration Trend</h5>
                                <p class="text-muted mb-0">Monthly overview</p>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="chart-toggle-btn active" onclick="toggleTrendChart('line')">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                                <button class="chart-toggle-btn" onclick="toggleTrendChart('bar')">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="monthlyTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">🏆 Top Performing Courses</h5>
                                <p class="text-muted mb-0">Most popular courses</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="topCoursesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">💰 Marketing ROI by Source</h5>
                                <p class="text-muted mb-0">Conversion rates by marketing channel</p>
                            </div>
                        </div>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="roiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="row">
            <div class="col-12">
                <div class="card card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">📋 Recent Registrations</h5>
                                <p class="text-muted mb-0">Latest student sign-ups</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm" onclick="previousPage()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="nextPage()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="loadMoreRegistrations()">
                                    <i class="fas fa-redo me-1"></i> Load More
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Source</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRegistrationsContainer">
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2">Loading registrations...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="text-muted fs-13" id="registrationsCount">
                                Showing 0 registrations
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="fs-4 fw-bold" id="avgRegistration">-</div>
                                <div class="text-white-50 fs-13">Avg. Daily Registrations</div>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="fs-4 fw-bold" id="bestSource">-</div>
                                <div class="text-white-50 fs-13">Best Performing Source</div>
                            </div>
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="fs-4 fw-bold" id="topLocation">-</div>
                                <div class="text-white-50 fs-13">Top Location</div>
                            </div>
                            <div class="col-md-3">
                                <div class="fs-4 fw-bold" id="conversionRate">-</div>
                                <div class="text-white-50 fs-13">Overall Conversion Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentPage = 1;
        let totalPages = 1;
        let currentTimePeriod = 'month';
        let chartInstances = {};
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            
            // Add event listeners
            document.getElementById('chartTypeSelect').addEventListener('change', function() {
                fetchMarketingSurveyData(this.value);
            });
            
            // Auto-refresh every 5 minutes
            setInterval(loadDashboardData, 300000);
        });
        
        function loadDashboardData() {
            fetchOverviewMetrics();
            fetchMarketingSurveyData('bar');
            fetchMonthlyTrend('line');
            fetchLocationData('doughnut');
            fetchTopCourses();
            fetchROIData();
            fetchRecentRegistrations(1);
        }
        
        function refreshAllData() {
            // Add loading state
            document.body.classList.add('data-loading');
            
            loadDashboardData();
            
            // Show toast notification
            showToast('Data refreshed successfully', 'success');
            
            setTimeout(() => {
                document.body.classList.remove('data-loading');
            }, 1000);
        }
        
        function setTimePeriod(period) {
            currentTimePeriod = period;
            
            // Update active button
            document.querySelectorAll('.time-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Refresh data
            loadDashboardData();
        }
        
        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            // Add to container
            const container = document.querySelector('.toast-container') || document.body;
            container.appendChild(toast);
            
            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove after hidden
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
        
        // Overview Metrics
        async function fetchOverviewMetrics() {
            try {
                const response = await fetch(`/api/marketing-manager/overview?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                // Update KPI cards
                document.getElementById('totalRegistered').textContent = data.total_registered?.toLocaleString() || '0';
                document.getElementById('thisMonth').textContent = data.this_month_registrations?.toLocaleString() || '0';
                document.getElementById('lastMonth').textContent = data.last_month_registrations?.toLocaleString() || '0';
                document.getElementById('totalStudents').textContent = data.total_students?.toLocaleString() || '0';
                
                // Update growth indicator
                const growthIndicator = document.getElementById('growthIndicator');
                const growthValue = document.getElementById('growthValue');
                const growthIcon = document.getElementById('growthIcon');
                
                if (data.growth_percentage && data.growth_percentage !== 0) {
                    growthIndicator.style.display = 'flex';
                    const isPositive = data.growth_percentage > 0;
                    
                    // Set icon
                    growthIcon.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>`;
                    
                    // Set badge class and text
                    growthValue.className = `badge bg-${isPositive ? 'success' : 'danger'} bg-opacity-10 text-${isPositive ? 'success' : 'danger'}`;
                    growthValue.textContent = (isPositive ? '+' : '') + data.growth_percentage + '%';
                } else {
                    growthIndicator.style.display = 'none';
                }
                
                // Update quick stats
                updateQuickStats(data);
            } catch (error) {
                console.error('Error fetching overview metrics:', error);
                showToast('Failed to load metrics', 'danger');
            }
        }
        
        function updateQuickStats(data) {
            if (data.avg_daily) document.getElementById('avgRegistration').textContent = data.avg_daily.toLocaleString();
            if (data.best_source) document.getElementById('bestSource').textContent = data.best_source;
            if (data.top_location) document.getElementById('topLocation').textContent = data.top_location;
            if (data.conversion_rate) document.getElementById('conversionRate').textContent = data.conversion_rate + '%';
        }
        
        // Marketing Survey Chart
        async function fetchMarketingSurveyData(chartType = 'bar') {
            try {
                const response = await fetch(`/api/marketing-manager/marketing-survey?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('marketingSurveyChart');
                if (chartInstances.marketingSurvey) {
                    chartInstances.marketingSurvey.destroy();
                }
                
                chartInstances.marketingSurvey = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: data.map(item => item.source),
                        datasets: [{
                            label: 'Number of Students',
                            data: data.map(item => item.count),
                            backgroundColor: data.map((_, index) => {
                                const colors = [
                                    'rgba(102, 126, 234, 0.8)',
                                    'rgba(118, 75, 162, 0.8)',
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(16, 185, 129, 0.8)',
                                    'rgba(245, 158, 11, 0.8)',
                                    'rgba(239, 68, 68, 0.8)'
                                ];
                                return colors[index % colors.length];
                            }),
                            borderWidth: 0,
                            borderRadius: chartType === 'bar' ? 6 : 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: chartType === 'pie' || chartType === 'doughnut',
                                position: 'bottom'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 10,
                                cornerRadius: 6
                            }
                        },
                        scales: chartType === 'bar' ? {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        } : {}
                    }
                });
            } catch (error) {
                console.error('Error fetching marketing survey data:', error);
            }
        }
        
        // Monthly Trend Chart
        async function fetchMonthlyTrend(chartType = 'line') {
            try {
                const response = await fetch(`/api/marketing-manager/monthly-trend?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('monthlyTrendChart');
                if (chartInstances.monthlyTrend) {
                    chartInstances.monthlyTrend.destroy();
                }
                
                chartInstances.monthlyTrend = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: data.map(item => item.month),
                        datasets: [{
                            label: 'Registrations',
                            data: data.map(item => item.count),
                            borderColor: 'rgba(102, 126, 234, 1)',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching monthly trend:', error);
            }
        }
        
        function toggleTrendChart(type) {
            // Update button states
            document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');
            
            fetchMonthlyTrend(type);
        }
        
        // Location Chart
        async function fetchLocationData(chartType = 'doughnut') {
            try {
                const response = await fetch(`/api/marketing-manager/location-data?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('locationChart');
                if (chartInstances.locationChart) {
                    chartInstances.locationChart.destroy();
                }
                
                chartInstances.locationChart = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: data.map(item => item.location),
                        datasets: [{
                            data: data.map(item => item.count),
                            backgroundColor: [
                                'rgba(102, 126, 234, 0.8)',
                                'rgba(118, 75, 162, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)'
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
            } catch (error) {
                console.error('Error fetching location data:', error);
            }
        }
        
        function toggleLocationChart(type) {
            // Update button states
            const buttons = event.target.closest('.btn-group').querySelectorAll('.chart-toggle-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');
            
            fetchLocationData(type);
        }
        
        // Top Courses Chart
        async function fetchTopCourses() {
            try {
                const response = await fetch(`/api/marketing-manager/top-courses?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('topCoursesChart');
                if (chartInstances.topCourses) {
                    chartInstances.topCourses.destroy();
                }
                
                chartInstances.topCourses = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.course_name.substring(0, 25) + '...'),
                        datasets: [{
                            label: 'Registrations',
                            data: data.map(item => item.registrations),
                            backgroundColor: 'rgba(245, 158, 11, 0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching top courses:', error);
            }
        }
        
        // ROI Chart
        async function fetchROIData() {
            try {
                const response = await fetch(`/api/marketing-manager/roi-data?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('roiChart');
                if (chartInstances.roiChart) {
                    chartInstances.roiChart.destroy();
                }
                
                chartInstances.roiChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.source),
                        datasets: [{
                            label: 'Conversion Rate %',
                            data: data.map(item => item.conversion_rate),
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const item = data[context.dataIndex];
                                        return [
                                            'Students: ' + item.students,
                                            'Registrations: ' + item.registrations,
                                            'Conversion: ' + item.conversion_rate + '%'
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching ROI data:', error);
            }
        }
        
        // Recent Registrations
        async function fetchRecentRegistrations(page = 1) {
            currentPage = page;
            
            const container = document.getElementById('recentRegistrationsContainer');
            container.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading registrations...</p>
                    </td>
                </tr>
            `;
            
            try {
                const response = await fetch(`/api/marketing-manager/recent-registrations?page=${page}&period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                renderRegistrationsTable(data);
            } catch (error) {
                container.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>Failed to load registrations</p>
                            <button class="btn btn-sm btn-outline-danger mt-2" onclick="fetchRecentRegistrations(${currentPage})">
                                Retry
                            </button>
                        </td>
                    </tr>
                `;
            }
        }
        
        function renderRegistrationsTable(data) {
            const container = document.getElementById('recentRegistrationsContainer');
            
            if (!data || data.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i>
                            <p>No registrations found</p>
                            <p class="small">Try changing the time period filter</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.forEach(reg => {
                const statusClass = reg.status === 'Registered' ? 'status-registered' : 'status-pending';
                const sourceColors = {
                    'Social Media': 'bg-primary bg-opacity-10 text-primary',
                    'Email': 'bg-info bg-opacity-10 text-info',
                    'Referral': 'bg-success bg-opacity-10 text-success',
                    'Website': 'bg-warning bg-opacity-10 text-warning',
                    'Event': 'bg-danger bg-opacity-10 text-danger'
                };
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial me-3">
                                    ${reg.student_name?.charAt(0) || 'U'}
                                </div>
                                <div>
                                    <div class="fw-medium">${reg.student_name}</div>
                                    <small class="text-muted">${reg.email || ''}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium">${reg.course_name}</div>
                            <small class="text-muted">${reg.course_code || ''}</small>
                        </td>
                        <td>
                            <div class="fw-medium">${reg.registration_date}</div>
                            <small class="text-muted">${reg.time || ''}</small>
                        </td>
                        <td>
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                            ${reg.location}
                        </td>
                        <td>
                            <span class="badge ${sourceColors[reg.marketing_source] || 'bg-secondary'}">
                                ${reg.marketing_source}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge ${statusClass}">
                                ${reg.status}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewRegistration(${reg.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" onclick="contactStudent(${reg.id})" title="Contact">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            container.innerHTML = html;
            document.getElementById('registrationsCount').textContent = 
                `Showing ${data.length} registrations`;
        }
        
        function loadMoreRegistrations() {
            if (currentPage < totalPages) {
                fetchRecentRegistrations(currentPage + 1);
            }
        }
        
        function previousPage() {
            if (currentPage > 1) {
                fetchRecentRegistrations(currentPage - 1);
            }
        }
        
        function nextPage() {
            if (currentPage < totalPages) {
                fetchRecentRegistrations(currentPage + 1);
            }
        }
        
        function exportDashboard() {
            // Implement export functionality
            showToast('Export feature coming soon!', 'info');
        }
        
        function viewRegistration(id) {
            // Implement view registration details
            console.log('View registration:', id);
            showToast('View feature coming soon!', 'info');
        }
        
        function contactStudent(id) {
            // Implement contact student
            console.log('Contact student:', id);
            showToast('Contact feature coming soon!', 'info');
        }
    </script>
@endsection