<?php $__env->startSection('title', 'NEBULA | Program Administrator (Level 02) Dashboard'); ?>

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
        
        .nav-tabs-custom {
            border-bottom: 2px solid #dee2e6;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: 0;
            position: relative;
        }
        
        .nav-tabs-custom .nav-link.active {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            background: none;
        }
        
        .badge-purple {
            background-color: #667eea;
            color: white;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
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
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-dark">Program Administrator (Level 02) Dashboard</h4>
                                <p class="text-muted mb-0">Operational management, student headcount, academic performance, and course status</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" class="form-control" placeholder="Search..." id="searchInput">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom mb-4" id="dashboardTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                    <i class="fas fa-chart-pie me-2"></i> Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals" type="button" role="tab">
                                    <i class="fas fa-check-circle me-2"></i> Approvals
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">
                                    <i class="fas fa-graduation-cap me-2"></i> Academic Performance
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                                    <i class="fas fa-calendar-check me-2"></i> Attendance
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="clearance-tab" data-bs-toggle="tab" data-bs-target="#clearance" type="button" role="tab">
                                    <i class="fas fa-clipboard-check me-2"></i> Clearance Status
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                                    <i class="fas fa-credit-card me-2"></i> Payments
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="dashboardTabsContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
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
                                                <h5 class="card-title text-muted text-uppercase fs-12">Total Active Students</h5>
                                                <h2 class="fw-bold text-purple mb-1" id="totalActiveStudents">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-user-graduate me-1"></i> Enrolled Students
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card kpi-card card-hover border-left-primary">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="avatar-initial bg-primary bg-opacity-10 text-primary">
                                                        <i class="fas fa-layer-group"></i>
                                                    </div>
                                                    <div id="batchGrowth" style="display: none;">
                                                        <span id="batchGrowthIcon" class="me-1"></span>
                                                        <span id="batchGrowthValue" class="badge"></span>
                                                    </div>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">Active Batches</h5>
                                                <h2 class="fw-bold text-primary mb-1" id="activeBatches">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-calendar-alt me-1"></i> Running Intakes
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card kpi-card card-hover border-left-success">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="avatar-initial bg-success bg-opacity-10 text-success">
                                                        <i class="fas fa-clock"></i>
                                                    </div>
                                                    <span class="badge bg-success bg-opacity-10 text-success">Pending</span>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">Pending Approvals</h5>
                                                <h2 class="fw-bold text-success mb-1" id="pendingApprovals">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Awaiting action
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card kpi-card card-hover border-left-warning">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="avatar-initial bg-warning bg-opacity-10 text-warning">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Performance</span>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">Avg Attendance Rate</h5>
                                                <h2 class="fw-bold text-warning mb-1" id="avgAttendanceRate">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-chart-line me-1"></i> Overall attendance
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Row -->
                                <div class="row mb-4">
                                    <div class="col-xl-8 mb-4">
                                        <div class="card card-hover h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📊 Student Count by Batch</h5>
                                                        <p class="text-muted mb-0">Monitor capacity distribution</p>
                                                    </div>
                                                    <select id="batchChartType" class="form-select form-select-sm" style="width: auto;">
                                                        <option value="bar">Bar Chart</option>
                                                        <option value="horizontalBar">Horizontal Bar</option>
                                                    </select>
                                                </div>
                                                <div class="chart-container">
                                                    <canvas id="batchStudentChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-4 mb-4">
                                        <div class="card card-hover h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">🎯 Today's Performance</h5>
                                                        <p class="text-muted mb-0">Daily overview</p>
                                                    </div>
                                                    <button class="btn btn-outline-secondary btn-sm" onclick="refreshOverview()">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light-primary rounded">
                                                            <div>
                                                                <div class="text-muted fs-12">Today's Registrations</div>
                                                                <div class="fw-bold fs-18" id="todayRegistrations">0</div>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="text-muted fs-12">Growth</div>
                                                                <div class="fw-bold fs-14" id="growthPercentage">0%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="p-3 bg-light-success rounded">
                                                            <div class="text-muted fs-12">Pending Clearances</div>
                                                            <div class="fw-bold fs-16" id="pendingClearances">0</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 mb-2">
                                                        <div class="p-3 bg-light-warning rounded">
                                                            <div class="text-muted fs-12">Special Approvals</div>
                                                            <div class="fw-bold fs-16" id="specialApprovalNeeded">0</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-3 bg-light-info rounded">
                                                            <div class="text-muted fs-12">Exam Pass Rate</div>
                                                            <div class="fw-bold fs-16" id="passRate">0%</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-3 bg-light-purple rounded">
                                                            <div class="text-muted fs-12">Semester Reg.</div>
                                                            <div class="fw-bold fs-16" id="monthSemesterReg">0</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Semesters -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card card-hover">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📅 Active Semesters</h5>
                                                        <p class="text-muted mb-0">Currently running semesters</p>
                                                    </div>
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewAllSemesters()">
                                                        <i class="fas fa-eye me-1"></i> View All
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover" id="activeSemestersTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Semester</th>
                                                                <th>Course</th>
                                                                <th>Start Date</th>
                                                                <th>End Date</th>
                                                                <th>Registered Students</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="activeSemestersBody">
                                                            <tr>
                                                                <td colspan="7" class="text-center py-4">
                                                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                                                    <span class="ms-2">Loading active semesters...</span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approvals Tab -->
                            <div class="tab-pane fade" id="approvals" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card card-hover">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">✅ Pending Registration Approvals</h5>
                                                        <p class="text-muted mb-0">Approve or reject student registrations</p>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-outline-success btn-sm" onclick="approveAll()">
                                                            <i class="fas fa-check-double me-1"></i> Approve All
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm" onclick="rejectAll()">
                                                            <i class="fas fa-times me-1"></i> Reject All
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Student</th>
                                                                <th>Course</th>
                                                                <th>Batch</th>
                                                                <th>Registration Date</th>
                                                                <th>Fee (LKR)</th>
                                                                <th>Counselor</th>
                                                                <th>Remarks</th>
                                                                <th>Submitted</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="pendingApprovalsBody">
                                                            <tr>
                                                                <td colspan="9" class="text-center py-5">
                                                                    <div class="spinner-border text-primary" role="status">
                                                                        <span class="visually-hidden">Loading...</span>
                                                                    </div>
                                                                    <p class="text-muted mt-2">Loading pending approvals...</p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Performance Tab -->
                            <div class="tab-pane fade" id="academic" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-xl-8 mb-4">
                                        <div class="card card-hover h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📈 Grade Distribution</h5>
                                                        <p class="text-muted mb-0">Overall academic performance</p>
                                                    </div>
                                                    <select id="gradeChartType" class="form-select form-select-sm" style="width: auto;">
                                                        <option value="bar">Bar Chart</option>
                                                        <option value="pie">Pie Chart</option>
                                                        <option value="doughnut">Doughnut Chart</option>
                                                    </select>
                                                </div>
                                                <div class="chart-container">
                                                    <canvas id="gradeDistributionChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-4 mb-4">
                                        <div class="card card-hover h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">🏆 Top Performing Courses</h5>
                                                        <p class="text-muted mb-0">Course-wise pass rates</p>
                                                    </div>
                                                    <span class="badge badge-purple">Pass Rate</span>
                                                </div>
                                                <div id="coursePerformanceList" class="list-group list-group-flush">
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="text-muted">Loading...</div>
                                                        <span class="badge bg-secondary">0%</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 pt-3 border-top">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="text-muted">Repeat Students</div>
                                                        <div class="fw-bold fs-16" id="repeatStudents">0</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Tab -->
                            <div class="tab-pane fade" id="attendance" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card card-hover">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📊 Attendance Overview</h5>
                                                        <p class="text-muted mb-0">Daily attendance trends</p>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-outline-secondary btn-sm" onclick="exportAttendance()">
                                                            <i class="fas fa-download me-1"></i> Export
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="chart-container">
                                                    <canvas id="attendanceTrendChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card card-hover">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📋 Course-wise Attendance</h5>
                                                        <p class="text-muted mb-0">Attendance rates by course</p>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="text-muted fs-12">Overall Attendance</div>
                                                        <div class="fw-bold fs-18" id="overallAttendanceRate">0%</div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Course</th>
                                                                <th>Attendance Rate</th>
                                                                <th>Total Records</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="courseAttendanceBody">
                                                            <tr>
                                                                <td colspan="5" class="text-center py-4">
                                                                    Loading attendance data...
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Clearance Status Tab -->
                            <div class="tab-pane fade" id="clearance" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-xl-6 mb-4">
                                        <div class="card card-hover h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📊 Clearance Request Status</h5>
                                                        <p class="text-muted mb-0">By clearance type</p>
                                                    </div>
                                                    <select id="clearanceChartType" class="form-select form-select-sm" style="width: auto;">
                                                        <option value="bar">Stacked Bar</option>
                                                        <option value="stackedBar">Grouped Bar</option>
                                                    </select>
                                                </div>
                                                <div class="chart-container">
                                                    <canvas id="clearanceStatusChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-6 mb-4">
                                        <div class="card card-hover h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">🔄 Recent Clearance Requests</h5>
                                                        <p class="text-muted mb-0">Latest 10 requests</p>
                                                    </div>
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewAllClearances()">
                                                        <i class="fas fa-eye me-1"></i> View All
                                                    </button>
                                                </div>
                                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Student</th>
                                                                <th>Type</th>
                                                                <th>Course</th>
                                                                <th>Status</th>
                                                                <th>Requested</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="recentClearancesBody">
                                                            <tr>
                                                                <td colspan="5" class="text-center py-3">
                                                                    Loading clearance requests...
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                <div class="row mb-4">
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card kpi-card card-hover border-left-success">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="avatar-initial bg-success bg-opacity-10 text-success">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <span class="badge bg-success bg-opacity-10 text-success">Total</span>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">Total Revenue</h5>
                                                <h2 class="fw-bold text-success mb-1" id="totalRevenue">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-coins me-1"></i> Collected amount
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
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">Pending Payments</h5>
                                                <h2 class="fw-bold text-warning mb-1" id="pendingPayments">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Awaiting payment
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card kpi-card card-hover border-left-primary">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="avatar-initial bg-primary bg-opacity-10 text-primary">
                                                        <i class="fas fa-chart-line"></i>
                                                    </div>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">Monthly</span>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">This Month</h5>
                                                <h2 class="fw-bold text-primary mb-1" id="thisMonthRevenue">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-calendar me-1"></i> Monthly collection
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card kpi-card card-hover border-left-purple">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="avatar-initial bg-purple bg-opacity-10 text-purple">
                                                        <i class="fas fa-percentage"></i>
                                                    </div>
                                                    <span class="badge bg-purple bg-opacity-10 text-purple">Rate</span>
                                                </div>
                                                <h5 class="card-title text-muted text-uppercase fs-12">Collection Rate</h5>
                                                <h2 class="fw-bold text-purple mb-1" id="collectionRate">-</h2>
                                                <div class="text-muted fs-13">
                                                    <i class="fas fa-chart-pie me-1"></i> Payment efficiency
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card card-hover">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="card-title mb-1">📈 Monthly Revenue Trend</h5>
                                                        <p class="text-muted mb-0">Last 6 months performance</p>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="chart-toggle-btn active" onclick="toggleRevenueChart('line')">
                                                            <i class="fas fa-chart-line"></i>
                                                        </button>
                                                        <button class="chart-toggle-btn" onclick="toggleRevenueChart('bar')">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="chart-container">
                                                    <canvas id="revenueTrendChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject Registration</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentTimePeriod = 'month';
        let currentRejectId = null;
        let chartInstances = {};
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            
            // Load data for active tab
            const activeTab = document.querySelector('#dashboardTabs .nav-link.active');
            if (activeTab.id === 'overview-tab') {
                fetchOverviewMetrics();
                fetchActiveSemesters();
            } else if (activeTab.id === 'approvals-tab') {
                fetchPendingApprovals();
            } else if (activeTab.id === 'academic-tab') {
                fetchAcademicPerformance();
            } else if (activeTab.id === 'attendance-tab') {
                fetchAttendanceOverview();
            } else if (activeTab.id === 'clearance-tab') {
                fetchClearanceStatus();
            } else if (activeTab.id === 'payments-tab') {
                fetchPaymentOverview();
            }
            
            // Tab change event
            document.querySelectorAll('#dashboardTabs button').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const tabId = event.target.id;
                    if (tabId === 'overview-tab') {
                        fetchOverviewMetrics();
                        fetchActiveSemesters();
                    } else if (tabId === 'approvals-tab') {
                        fetchPendingApprovals();
                    } else if (tabId === 'academic-tab') {
                        fetchAcademicPerformance();
                    } else if (tabId === 'attendance-tab') {
                        fetchAttendanceOverview();
                    } else if (tabId === 'clearance-tab') {
                        fetchClearanceStatus();
                    } else if (tabId === 'payments-tab') {
                        fetchPaymentOverview();
                    }
                });
            });
            
            // Chart type changes
            document.getElementById('batchChartType').addEventListener('change', function() {
                fetchStudentCountByBatch(this.value);
            });
            
            document.getElementById('gradeChartType').addEventListener('change', function() {
                fetchAcademicPerformance(this.value);
            });
            
            document.getElementById('clearanceChartType').addEventListener('change', function() {
                fetchClearanceStatus(this.value);
            });
            
            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function(e) {
                searchDashboard(e.target.value);
            });
            
            // Auto-refresh every 5 minutes
            setInterval(refreshOverview, 300000);
        });
        
        function loadDashboardData() {
            fetchOverviewMetrics();
        }
        
        function refreshAllData() {
            document.body.classList.add('data-loading');
            
            // Refresh based on active tab
            const activeTab = document.querySelector('#dashboardTabs .nav-link.active');
            if (activeTab.id === 'overview-tab') {
                fetchOverviewMetrics();
                fetchActiveSemesters();
            } else if (activeTab.id === 'approvals-tab') {
                fetchPendingApprovals();
            } else if (activeTab.id === 'academic-tab') {
                fetchAcademicPerformance();
            } else if (activeTab.id === 'attendance-tab') {
                fetchAttendanceOverview();
            } else if (activeTab.id === 'clearance-tab') {
                fetchClearanceStatus();
            } else if (activeTab.id === 'payments-tab') {
                fetchPaymentOverview();
            }
            
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
            refreshAllData();
        }
        
        function showToast(message, type = 'info') {
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
            
            const container = document.querySelector('.toast-container') || document.body;
            container.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
        
        // Overview Metrics
        async function fetchOverviewMetrics() {
            try {
                const response = await fetch(`/api/program-admin-l2/overview`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update KPI cards
                    document.getElementById('totalActiveStudents').textContent = 
                        data.data.total_active_students?.toLocaleString() || '0';
                    document.getElementById('activeBatches').textContent = 
                        data.data.active_batches?.toLocaleString() || '0';
                    document.getElementById('pendingApprovals').textContent = 
                        data.data.pending_approvals?.toLocaleString() || '0';
                    document.getElementById('avgAttendanceRate').textContent = 
                        data.data.avg_attendance_rate + '%' || '0%';
                    
                    // Update today's metrics
                    document.getElementById('todayRegistrations').textContent = 
                        data.data.today_registrations || '0';
                    document.getElementById('growthPercentage').textContent = 
                        data.data.growth_percentage + '%' || '0%';
                    document.getElementById('pendingClearances').textContent = 
                        data.data.pending_clearances || '0';
                    document.getElementById('specialApprovalNeeded').textContent = 
                        data.data.special_approval_needed || '0';
                    document.getElementById('passRate').textContent = 
                        data.data.pass_rate + '%' || '0%';
                    document.getElementById('monthSemesterReg').textContent = 
                        data.data.month_semester_reg || '0';
                    
                    // Update student count by batch chart
                    updateBatchStudentChart(data.data.student_count_by_batch);
                    
                } else {
                    showToast(data.message || 'Failed to load overview metrics', 'danger');
                }
            } catch (error) {
                console.error('Error fetching overview metrics:', error);
                showToast('Failed to load dashboard metrics', 'danger');
            }
        }
        
        function updateBatchStudentChart(data) {
            const ctx = document.getElementById('batchStudentChart');
            const chartType = document.getElementById('batchChartType').value;
            
            if (chartInstances.batchStudent) {
                chartInstances.batchStudent.destroy();
            }
            
            if (!data || data.length === 0) {
                // Show empty state
                ctx.innerHTML = '<div class="text-center py-5 text-muted">No data available</div>';
                return;
            }
            
            const labels = data.map(item => `${item.batch}\n${item.course_name.substring(0, 20)}...`);
            const counts = data.map(item => item.count);
            
            chartInstances.batchStudent = new Chart(ctx, {
                type: chartType === 'horizontalBar' ? 'bar' : chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Student Count',
                        data: counts,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 1,
                        borderRadius: chartType === 'bar' ? 6 : 0
                    }]
                },
                options: {
                    indexAxis: chartType === 'horizontalBar' ? 'y' : 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Students: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
        
        // Active Semesters
        async function fetchActiveSemesters() {
            try {
                const response = await fetch(`/api/program-admin-l2/active-semesters`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const body = document.getElementById('activeSemestersBody');
                
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(semester => {
                        const statusBadge = semester.status === 'active' ? 
                            '<span class="badge badge-success">Active</span>' :
                            '<span class="badge badge-warning">' + semester.status + '</span>';
                        
                        html += `
                            <tr>
                                <td>${semester.name}</td>
                                <td>${semester.course_name}</td>
                                <td>${semester.start_date}</td>
                                <td>${semester.end_date}</td>
                                <td>
                                    <span class="badge badge-purple">${semester.registered_count}</span>
                                </td>
                                <td>${statusBadge}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewSemester(${semester.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    body.innerHTML = html;
                } else {
                    body.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i>
                                <p>No active semesters found</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error fetching active semesters:', error);
                const body = document.getElementById('activeSemestersBody');
                body.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>Failed to load active semesters</p>
                        </td>
                    </tr>
                `;
            }
        }
        
        // Pending Approvals
        async function fetchPendingApprovals() {
            try {
                const response = await fetch(`/api/program-admin-l2/pending-approvals`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const body = document.getElementById('pendingApprovalsBody');
                
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(approval => {
                        html += `
                            <tr>
                                <td>
                                    <div class="fw-medium">${approval.student_name}</div>
                                    <small class="text-muted">ID: ${approval.student_id}</small>
                                </td>
                                <td>${approval.course_name}</td>
                                <td>${approval.batch}</td>
                                <td>${approval.registration_date}</td>
                                <td>${parseFloat(approval.registration_fee).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td>${approval.counselor_name || 'N/A'}</td>
                                <td>${approval.remarks || '-'}</td>
                                <td>${approval.created_at}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-success" onclick="approveRegistration(${approval.id})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="showRejectionModal(${approval.id})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="viewStudent(${approval.student_id})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    
                    body.innerHTML = html;
                } else {
                    body.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-3 opacity-50"></i>
                                <p>No pending approvals</p>
                                <p class="small">All registrations have been processed</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error fetching pending approvals:', error);
                const body = document.getElementById('pendingApprovalsBody');
                body.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <p>Failed to load pending approvals</p>
                            <button class="btn btn-sm btn-outline-danger mt-2" onclick="fetchPendingApprovals()">
                                Retry
                            </button>
                        </td>
                    </tr>
                `;
            }
        }
        
        // Academic Performance
        async function fetchAcademicPerformance(chartType = 'bar') {
            try {
                const response = await fetch(`/api/program-admin-l2/academic-performance`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update grade distribution chart
                    updateGradeDistributionChart(data.data.grade_distribution, chartType);
                    
                    // Update course performance list
                    updateCoursePerformanceList(data.data.course_performance);
                    
                    // Update repeat students
                    document.getElementById('repeatStudents').textContent = 
                        data.data.repeat_students || '0';
                }
            } catch (error) {
                console.error('Error fetching academic performance:', error);
            }
        }
        
        function updateGradeDistributionChart(data, chartType = 'bar') {
            const ctx = document.getElementById('gradeDistributionChart');
            
            if (chartInstances.gradeDistribution) {
                chartInstances.gradeDistribution.destroy();
            }
            
            if (!data || data.length === 0) {
                ctx.innerHTML = '<div class="text-center py-5 text-muted">No exam data available</div>';
                return;
            }
            
            const labels = data.map(item => item.grade || 'No Grade');
            const counts = data.map(item => item.count);
            
            chartInstances.gradeDistribution = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Students',
                        data: counts,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',  // A - Green
                            'rgba(0, 123, 255, 0.8)',  // B - Blue
                            'rgba(255, 193, 7, 0.8)',  // C - Yellow
                            'rgba(220, 53, 69, 0.8)',  // D - Red
                            'rgba(108, 117, 125, 0.8)', // F - Gray
                            'rgba(102, 126, 234, 0.8)'  // Others - Purple
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: chartType === 'pie' || chartType === 'doughnut',
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        function updateCoursePerformanceList(data) {
            const container = document.getElementById('coursePerformanceList');
            
            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="text-muted">No course performance data</div>
                        <span class="badge bg-secondary">-</span>
                    </div>
                `;
                return;
            }
            
            let html = '';
            data.slice(0, 5).forEach(course => {
                const badgeClass = course.pass_rate >= 70 ? 'badge-success' : 
                                 course.pass_rate >= 50 ? 'badge-warning' : 'badge-danger';
                
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-medium">${course.course_name}</div>
                            <small class="text-muted">${course.passed}/${course.total} students</small>
                        </div>
                        <span class="badge ${badgeClass}">${course.pass_rate}%</span>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        // Attendance Overview
        async function fetchAttendanceOverview() {
            try {
                const response = await fetch(`/api/program-admin-l2/attendance-overview?period=${currentTimePeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update attendance trend chart
                    updateAttendanceTrendChart(data.data.daily_attendance);
                    
                    // Update course attendance table
                    updateCourseAttendanceTable(data.data.course_attendance);
                    
                    // Update overall stats
                    if (data.data.overall_stats) {
                        document.getElementById('overallAttendanceRate').textContent = 
                            Math.round(data.data.overall_stats.overall_rate) + '%' || '0%';
                    }
                }
            } catch (error) {
                console.error('Error fetching attendance overview:', error);
            }
        }
        
        function updateAttendanceTrendChart(data) {
            const ctx = document.getElementById('attendanceTrendChart');
            
            if (chartInstances.attendanceTrend) {
                chartInstances.attendanceTrend.destroy();
            }
            
            if (!data || data.length === 0) {
                ctx.innerHTML = '<div class="text-center py-5 text-muted">No attendance data available</div>';
                return;
            }
            
            const labels = data.map(item => item.attendance_date);
            const rates = data.map(item => Math.round(item.attendance_rate));
            
            chartInstances.attendanceTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Attendance Rate (%)',
                        data: rates,
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
        }
        
        function updateCourseAttendanceTable(data) {
            const body = document.getElementById('courseAttendanceBody');
            
            if (!data || data.length === 0) {
                body.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No attendance records found
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.forEach(course => {
                const statusClass = course.attendance_rate >= 80 ? 'badge-success' : 
                                   course.attendance_rate >= 60 ? 'badge-warning' : 'badge-danger';
                const statusText = course.attendance_rate >= 80 ? 'Good' : 
                                   course.attendance_rate >= 60 ? 'Average' : 'Poor';
                
                html += `
                    <tr>
                        <td>${course.course_name}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${course.attendance_rate >= 80 ? 'bg-success' : course.attendance_rate >= 60 ? 'bg-warning' : 'bg-danger'}" 
                                     style="width: ${course.attendance_rate}%">
                                    ${course.attendance_rate}%
                                </div>
                            </div>
                        </td>
                        <td>${course.total_records}</td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewCourseAttendance('${course.course_name}')">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            body.innerHTML = html;
        }
        
        // Clearance Status
        async function fetchClearanceStatus(chartType = 'bar') {
            try {
                const response = await fetch(`/api/program-admin-l2/clearance-status`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update clearance status chart
                    updateClearanceStatusChart(data.data.clearance_by_type, chartType);
                    
                    // Update recent clearances
                    updateRecentClearances(data.data.recent_requests);
                }
            } catch (error) {
                console.error('Error fetching clearance status:', error);
            }
        }
        
        function updateClearanceStatusChart(data, chartType = 'bar') {
            const ctx = document.getElementById('clearanceStatusChart');
            
            if (chartInstances.clearanceStatus) {
                chartInstances.clearanceStatus.destroy();
            }
            
            if (!data || Object.keys(data).length === 0) {
                ctx.innerHTML = '<div class="text-center py-5 text-muted">No clearance data available</div>';
                return;
            }
            
            const types = Object.keys(data);
            const pendingData = types.map(type => data[type].pending || 0);
            const approvedData = types.map(type => data[type].approved || 0);
            const rejectedData = types.map(type => data[type].rejected || 0);
            
            chartInstances.clearanceStatus = new Chart(ctx, {
                type: chartType === 'stackedBar' ? 'bar' : 'bar',
                data: {
                    labels: types,
                    datasets: [
                        {
                            label: 'Pending',
                            data: pendingData,
                            backgroundColor: 'rgba(255, 193, 7, 0.8)'
                        },
                        {
                            label: 'Approved',
                            data: approvedData,
                            backgroundColor: 'rgba(40, 167, 69, 0.8)'
                        },
                        {
                            label: 'Rejected',
                            data: rejectedData,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        x: chartType === 'stackedBar' ? {
                            stacked: true
                        } : {},
                        y: chartType === 'stackedBar' ? {
                            stacked: true
                        } : {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        function updateRecentClearances(data) {
            const body = document.getElementById('recentClearancesBody');
            
            if (!data || data.length === 0) {
                body.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">
                            No recent clearance requests
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.forEach(request => {
                const statusBadge = request.status === 'approved' ? 'badge-success' :
                                   request.status === 'rejected' ? 'badge-danger' : 'badge-warning';
                const statusText = request.status.charAt(0).toUpperCase() + request.status.slice(1);
                
                html += `
                    <tr>
                        <td>${request.student_name}</td>
                        <td><span class="badge bg-info">${request.clearance_type}</span></td>
                        <td>${request.course_name}</td>
                        <td><span class="badge ${statusBadge}">${statusText}</span></td>
                        <td>${request.requested_at}</td>
                    </tr>
                `;
            });
            
            body.innerHTML = html;
        }
        
        // Payment Overview
        async function fetchPaymentOverview() {
            try {
                const response = await fetch(`/api/program-admin-l2/payment-overview`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update KPI cards
                    document.getElementById('totalRevenue').textContent = 
                        'LKR ' + (data.data.total_revenue?.toLocaleString('en-US', {minimumFractionDigits: 2}) || '0.00');
                    document.getElementById('pendingPayments').textContent = 
                        data.data.pending_payments?.toLocaleString() || '0';
                    
                    // Calculate this month revenue
                    const thisMonth = new Date().toLocaleString('default', { month: 'short', year: 'numeric' });
                    const thisMonthRevenue = data.data.monthly_revenue?.find(m => m.month === thisMonth)?.revenue || 0;
                    document.getElementById('thisMonthRevenue').textContent = 
                        'LKR ' + thisMonthRevenue.toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    // Calculate collection rate
                    const paidCount = data.data.payment_stats?.find(p => p.status === 'paid')?.count || 0;
                    const pendingCount = data.data.pending_payments || 0;
                    const totalCount = paidCount + pendingCount;
                    const collectionRate = totalCount > 0 ? Math.round((paidCount / totalCount) * 100) : 0;
                    document.getElementById('collectionRate').textContent = collectionRate + '%';
                    
                    // Update revenue trend chart
                    updateRevenueTrendChart(data.data.monthly_revenue);
                }
            } catch (error) {
                console.error('Error fetching payment overview:', error);
            }
        }
        
        function updateRevenueTrendChart(data) {
            const ctx = document.getElementById('revenueTrendChart');
            
            if (chartInstances.revenueTrend) {
                chartInstances.revenueTrend.destroy();
            }
            
            if (!data || data.length === 0) {
                ctx.innerHTML = '<div class="text-center py-5 text-muted">No revenue data available</div>';
                return;
            }
            
            const labels = data.map(item => item.month);
            const revenues = data.map(item => item.revenue);
            
            chartInstances.revenueTrend = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (LKR)',
                        data: revenues,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function toggleRevenueChart(type) {
            // This would switch between line and bar chart for revenue
            // Implementation depends on your data structure
        }
        
        // Approval Actions
        async function approveRegistration(id) {
            if (!confirm('Are you sure you want to approve this registration?')) return;
            
            try {
                const response = await fetch(`/api/program-admin-l2/approve-registration/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Registration approved successfully', 'success');
                    fetchPendingApprovals();
                    fetchOverviewMetrics(); // Refresh KPI
                } else {
                    showToast(data.message || 'Failed to approve registration', 'danger');
                }
            } catch (error) {
                showToast('Failed to approve registration', 'danger');
            }
        }
        
        function showRejectionModal(id) {
            currentRejectId = id;
            const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
            modal.show();
        }
        
        async function confirmReject() {
            const reason = document.getElementById('rejectionReason').value;
            if (!reason.trim()) {
                alert('Please enter a reason for rejection');
                return;
            }
            
            try {
                const response = await fetch(`/api/program-admin-l2/reject-registration/${currentRejectId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('Registration rejected successfully', 'success');
                    document.getElementById('rejectionReason').value = '';
                    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectionModal'));
                    modal.hide();
                    fetchPendingApprovals();
                    fetchOverviewMetrics(); // Refresh KPI
                } else {
                    showToast(data.message || 'Failed to reject registration', 'danger');
                }
            } catch (error) {
                showToast('Failed to reject registration', 'danger');
            }
        }
        
        function approveAll() {
            if (!confirm('Are you sure you want to approve ALL pending registrations?')) return;
            // Implementation would need to approve all pending registrations
            showToast('This feature is under development', 'info');
        }
        
        function rejectAll() {
            if (!confirm('Are you sure you want to reject ALL pending registrations?')) return;
            // Implementation would need to reject all pending registrations with a common reason
            showToast('This feature is under development', 'info');
        }
        
        // Navigation functions
        function viewAllSemesters() {
            window.location.href = '/semester-management';
        }
        
        function viewAllClearances() {
            window.location.href = '/clearance-requests';
        }
        
        function viewStudent(studentId) {
            window.location.href = `/students/${studentId}`;
        }
        
        function viewSemester(semesterId) {
            window.location.href = `/semester-management/${semesterId}`;
        }
        
        function viewCourseAttendance(courseName) {
            // Navigate to detailed attendance for this course
            window.location.href = `/attendance?course=${encodeURIComponent(courseName)}`;
        }
        
        function exportAttendance() {
            showToast('Export feature is under development', 'info');
        }
        
        function searchDashboard(query) {
            // Search functionality across all tabs
            // Implementation depends on what you want to search
            showToast('Search feature is under development', 'info');
        }
        
        function refreshOverview() {
            fetchOverviewMetrics();
            fetchActiveSemesters();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/dashboards/program_admin_l2.blade.php ENDPATH**/ ?>