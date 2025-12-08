<?php $__env->startSection('title', 'NEBULA | Student Counselor Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.min.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        
        .kpi-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .kpi-card:hover {
            border-left-width: 6px;
        }
        
        .avatar-initial {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
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
        
        .status-special {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .counselor-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f8f9fa;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 rounded shadow-sm mb-4">
    <div class="page-title-box d-flex align-items-center justify-content-between">
        
        <div class="d-flex align-items-center">
            <div class="me-3">
                <div class="avatar-initial">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div>
                <h4 class="mb-1 fw-bold text-dark"> Student Counselor Dashboard</h4>
                <p class="text-muted mb-0">Monitor student intake and marketing effectiveness</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: 200px;">
                <input type="text" class="form-control" placeholder="Search students..." id="searchStudents">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <button class="btn btn-outline-primary btn-sm" onclick="refreshAllData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
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
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-3 text-muted"><i class="fas fa-calendar-alt me-1"></i> Time Period:</span>
                            <div class="d-flex flex-wrap">
                                <button class="time-filter-btn active" onclick="setTimePeriod('today')">Today</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('week')">This Week</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('month')">This Month</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('quarter')">Last 3 Months</button>
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
                <div class="card kpi-card card-hover border-left-purple">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-purple bg-opacity-10 text-purple">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="badge bg-purple bg-opacity-10 text-purple">Total</span>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">Total Registered</h5>
                        <h2 class="fw-bold text-purple mb-1" id="totalRegistered">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-user-graduate me-1"></i> Students
                        </div>
                        <div class="progress mt-3" style="height: 4px;">
                            <div class="progress-bar bg-purple" id="totalProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div id="todayGrowth" style="display: none;">
                                <span id="todayGrowthIcon" class="me-1"></span>
                                <span id="todayGrowthValue" class="badge"></span>
                            </div>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">Today's Registrations</h5>
                        <h2 class="fw-bold text-primary mb-1" id="todayRegistrations">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-bolt me-1"></i> New today
                        </div>
                        <div class="progress mt-3" style="height: 4px;">
                            <div class="progress-bar bg-primary" id="todayProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-success bg-opacity-10 text-success">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success">Weekly</span>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">This Week</h5>
                        <h2 class="fw-bold text-success mb-1" id="weekRegistrations">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-chart-line me-1"></i> Registrations
                        </div>
                        <div class="progress mt-3" style="height: 4px;">
                            <div class="progress-bar bg-success" id="weekProgress" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="avatar-initial bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning">Action Needed</span>
                        </div>
                        <h5 class="card-title text-muted text-uppercase fs-12">Pending</h5>
                        <h2 class="fw-bold text-warning mb-1" id="pendingRegistrations">-</h2>
                        <div class="text-muted fs-13">
                            <i class="fas fa-exclamation-circle me-1"></i> Awaiting approval
                        </div>
                        <div class="progress mt-3" style="height: 4px;">
                            <div class="progress-bar bg-warning" id="pendingProgress" style="width: 0%"></div>
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
                                <p class="text-muted mb-0">Lead sources overview</p>
                            </div>
                            <select id="surveyChartType" class="form-select form-select-sm" style="width: auto;">
                                <option value="bar">Bar Chart</option>
                                <option value="pie">Pie Chart</option>
                                <option value="doughnut">Doughnut Chart</option>
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
                                <h5 class="card-title mb-1">📈 Registration Trend</h5>
                                <p class="text-muted mb-0">Last 7 days performance</p>
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
                            <canvas id="dailyTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Distribution -->
        <div class="row mb-4">
            <div class="col-xl-6 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">📍 Student Location Distribution</h5>
                                <p class="text-muted mb-0">Geographic spread</p>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 mb-4">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">🎯 Counselor Performance</h5>
                                <p class="text-muted mb-0">Top performing counselors</p>
                            </div>
                            <select id="performancePeriod" class="form-select form-select-sm" style="width: auto;">
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <canvas id="counselorChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1">📋 Recent Student Registrations</h5>
                                <p class="text-muted mb-0">Latest student intake</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm" onclick="filterRegistrations('all')">
                                    All
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="filterRegistrations('pending')">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </button>
                                <button class="btn btn-outline-success btn-sm" onclick="filterRegistrations('registered')">
                                    <i class="fas fa-check me-1"></i> Registered
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Date & Time</th>
                                        <th>Location</th>
                                        <th>Counselor</th>
                                        <th>Source</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRegistrationsContainer">
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2">Loading student registrations...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="text-muted fs-13" id="registrationsCount">
                                Showing 0 registrations
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm" onclick="previousPage()">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="nextPage()">
                                    Next <i class="fas fa-chevron-right"></i>
                                </button>
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
        let currentTimePeriod = 'week';
        let currentFilter = 'all';
        let chartInstances = {};
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            
            // Add event listeners
            document.getElementById('surveyChartType').addEventListener('change', function() {
                fetchMarketingSurveyData(this.value);
            });
            
            document.getElementById('performancePeriod').addEventListener('change', function() {
                fetchCounselorPerformanceData(this.value);
            });
            
            // Search functionality
            document.getElementById('searchStudents').addEventListener('input', function(e) {
                searchRegistrations(e.target.value);
            });
            
            // Auto-refresh every 3 minutes
            setInterval(loadDashboardData, 180000);
        });
        
        function loadDashboardData() {
            fetchOverviewMetrics();
            fetchMarketingSurveyData('bar');
            fetchDailyTrend('line');
            fetchLocationData();
            fetchCounselorPerformanceData('week');
            fetchRecentRegistrations(1);
        }
        
        function refreshAllData() {
            // Add loading state
            document.body.classList.add('data-loading');
            
            loadDashboardData();
            
            // Show toast notification
            showToast('Dashboard data refreshed successfully', 'success');
            
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
        
        function filterRegistrations(filter) {
            currentFilter = filter;
            
            // Update button states
            document.querySelectorAll('.btn-outline-secondary, .btn-outline-warning, .btn-outline-success').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            fetchRecentRegistrations(1);
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
                const response = await fetch(`/api/student-counselor/overview?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                // Update KPI cards
                document.getElementById('totalRegistered').textContent = data.total_registered?.toLocaleString() || '0';
                document.getElementById('todayRegistrations').textContent = data.today_registrations?.toLocaleString() || '0';
                document.getElementById('weekRegistrations').textContent = data.week_registrations?.toLocaleString() || '0';
                document.getElementById('pendingRegistrations').textContent = data.pending_registrations?.toLocaleString() || '0';
                
                // Update progress bars
                updateProgressBars(data);
                
                // Update today's growth indicator
                const todayGrowth = document.getElementById('todayGrowth');
                const todayGrowthValue = document.getElementById('todayGrowthValue');
                const todayGrowthIcon = document.getElementById('todayGrowthIcon');
                
                if (data.today_growth_percentage && data.today_growth_percentage !== 0) {
                    todayGrowth.style.display = 'flex';
                    const isPositive = data.today_growth_percentage > 0;
                    
                    todayGrowthIcon.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>`;
                    todayGrowthValue.className = `badge bg-${isPositive ? 'success' : 'danger'} bg-opacity-10 text-${isPositive ? 'success' : 'danger'}`;
                    todayGrowthValue.textContent = (isPositive ? '+' : '') + data.today_growth_percentage + '%';
                } else {
                    todayGrowth.style.display = 'none';
                }
                
                // Update quick stats
                updateQuickStats(data);
            } catch (error) {
                console.error('Error fetching overview metrics:', error);
                showToast('Failed to load dashboard metrics', 'danger');
            }
        }
        
        function updateProgressBars(data) {
            // These are example progress calculations - adjust based on your actual data
            const totalProgress = Math.min((data.total_registered / 500) * 100, 100);
            const todayProgress = Math.min((data.today_registrations / 50) * 100, 100);
            const weekProgress = Math.min((data.week_registrations / 200) * 100, 100);
            const pendingProgress = Math.min((data.pending_registrations / 30) * 100, 100);
            
            document.getElementById('totalProgress').style.width = `${totalProgress}%`;
            document.getElementById('todayProgress').style.width = `${todayProgress}%`;
            document.getElementById('weekProgress').style.width = `${weekProgress}%`;
            document.getElementById('pendingProgress').style.width = `${pendingProgress}%`;
        }
        
        function updateQuickStats(data) {
            if (data.avg_daily) document.getElementById('avgDaily').textContent = data.avg_daily.toLocaleString();
            if (data.conversion_rate) document.getElementById('conversionRate').textContent = data.conversion_rate + '%';
            if (data.top_course) document.getElementById('topCourse').textContent = data.top_course;
            if (data.avg_response_time) document.getElementById('responseTime').textContent = data.avg_response_time;
        }
        
        // Marketing Survey Chart
        async function fetchMarketingSurveyData(chartType = 'bar') {
            try {
                const response = await fetch(`/api/student-counselor/marketing-survey?period=${currentTimePeriod}`, {
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
                            borderWidth: 2,
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
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: { grid: { display: false } }
                        } : {}
                    }
                });
            } catch (error) {
                console.error('Error fetching marketing survey data:', error);
            }
        }
        
        // Daily Trend Chart
        async function fetchDailyTrend(chartType = 'line') {
            try {
                const response = await fetch(`/api/student-counselor/daily-trend?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('dailyTrendChart');
                if (chartInstances.dailyTrend) {
                    chartInstances.dailyTrend.destroy();
                }
                
                chartInstances.dailyTrend = new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: data.map(item => item.date),
                        datasets: [{
                            label: 'Registrations',
                            data: data.map(item => item.count),
                            borderColor: 'rgba(118, 75, 162, 1)',
                            backgroundColor: chartType === 'line' ? 'rgba(118, 75, 162, 0.1)' : 'rgba(118, 75, 162, 0.8)',
                            borderWidth: 2,
                            fill: chartType === 'line',
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(118, 75, 162, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 10,
                                cornerRadius: 6
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching daily trend:', error);
            }
        }
        
        function toggleTrendChart(type) {
            // Update button states
            document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');
            
            fetchDailyTrend(type);
        }
        
        // Location Data
        async function fetchLocationData() {
            try {
                const response = await fetch(`/api/student-counselor/location-data?period=${currentTimePeriod}`, {
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
                    type: 'doughnut',
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
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching location data:', error);
            }
        }
        
        // Counselor Performance Chart
        async function fetchCounselorPerformanceData(period = 'week') {
            try {
                const response = await fetch(`/api/student-counselor/counselor-performance?period=${period}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('counselorChart');
                if (chartInstances.counselorChart) {
                    chartInstances.counselorChart.destroy();
                }
                
                chartInstances.counselorChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.counselor_name?.substring(0, 15) || 'Unknown'),
                        datasets: [{
                            label: 'Students Assisted',
                            data: data.map(item => item.student_count),
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching counselor performance data:', error);
                // Show empty state
                const ctx = document.getElementById('counselorChart');
                if (chartInstances.counselorChart) {
                    chartInstances.counselorChart.destroy();
                }
                chartInstances.counselorChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['No data available'],
                        datasets: [{
                            label: 'Students Assisted',
                            data: [0],
                            backgroundColor: 'rgba(200, 200, 200, 0.5)'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        }
        
        // Recent Registrations
        async function fetchRecentRegistrations(page = 1) {
            currentPage = page;
            
            const container = document.getElementById('recentRegistrationsContainer');
            container.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading student registrations...</p>
                    </td>
                </tr>
            `;
            
            try {
                const response = await fetch(`/api/student-counselor/recent-registrations?page=${page}&period=${currentTimePeriod}&filter=${currentFilter}`, {
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
                        <td colspan="8" class="text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>Failed to load student registrations</p>
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i>
                            <p>No student registrations found</p>
                            <p class="small">Try changing the time period or filter</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.forEach(reg => {
                const statusClass = reg.status === 'Registered' ? 'status-registered' : 
                                  reg.status === 'Pending' ? 'status-pending' : 'status-special';
                
                const sourceColors = {
                    'Social Media': 'badge bg-purple bg-opacity-10 text-purple',
                    'Email': 'badge bg-primary bg-opacity-10 text-primary',
                    'Referral': 'badge bg-success bg-opacity-10 text-success',
                    'Website': 'badge bg-info bg-opacity-10 text-info',
                    'Event': 'badge bg-warning bg-opacity-10 text-warning'
                };
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial me-3" style="width: 36px; height: 36px;">
                                    ${reg.student_name?.charAt(0) || 'S'}
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
                            <small class="text-muted">${reg.registration_time || ''}</small>
                        </td>
                        <td>
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                            ${reg.location}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="counselor-avatar me-2">
                                    ${reg.counselor_name?.charAt(0) || 'C'}
                                </div>
                                <div>
                                    <div class="fw-medium">${reg.counselor_name}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="${sourceColors[reg.marketing_source] || 'badge bg-secondary'}">
                                ${reg.marketing_source}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge ${statusClass}">
                                ${reg.status}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="action-btn btn btn-outline-primary btn-sm" onclick="viewStudentDetails(${reg.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${reg.status === 'Pending' ? `
                                    <button class="action-btn btn btn-outline-success btn-sm" onclick="approveRegistration(${reg.id})" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="action-btn btn btn-outline-danger btn-sm" onclick="rejectRegistration(${reg.id})" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                                <button class="action-btn btn btn-outline-info btn-sm" onclick="contactStudent(${reg.student_id || reg.id})" title="Contact">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            container.innerHTML = html;
            document.getElementById('registrationsCount').textContent = 
                `Showing ${data.length} student registrations`;
        }
        
        function searchRegistrations(query) {
            const rows = document.querySelectorAll('#recentRegistrationsContainer tr');
            rows.forEach(row => {
                if (row.cells.length > 1) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
                }
            });
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
        
        // Action functions
        function viewStudentDetails(id) {
            showToast(`Viewing student details for ID: ${id}`, 'info');
            // Implement actual view functionality
        }
        
        function approveRegistration(id) {
            if (confirm('Are you sure you want to approve this registration?')) {
                showToast(`Registration ${id} approved successfully`, 'success');
                // Implement API call to approve registration
                fetchRecentRegistrations(currentPage); // Refresh table
            }
        }
        
        function rejectRegistration(id) {
            if (confirm('Are you sure you want to reject this registration?')) {
                const reason = prompt('Please enter rejection reason:');
                if (reason) {
                    showToast(`Registration ${id} rejected: ${reason}`, 'danger');
                    // Implement API call to reject registration
                    fetchRecentRegistrations(currentPage); // Refresh table
                }
            }
        }
        
        function contactStudent(id) {
            showToast(`Opening contact form for student ID: ${id}`, 'info');
            // Implement contact functionality
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/student_counselor_dashboard.blade.php ENDPATH**/ ?>