<?php $__env->startSection('title', 'NEBULA | Hostel Manager Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<style>
    .stat-card {
        transition: 0.3s;
        cursor: pointer;
        background: white;
        border-left: 4px solid transparent;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .stat-card.pending { border-left-color: #0d6efd; }
    .stat-card.approved { border-left-color: #198754; }
    .stat-card.rejected { border-left-color: #dc3545; }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .badge-pending { background: #0d6efd; color: white; }
    .badge-approved { background: #198754; color: white; }
    .badge-rejected { background: #dc3545; color: white; }
    
    .analytics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    .date-display {
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 500;
    }
    
    .select-all-checkbox {
        margin-right: 10px;
    }
    
    .bulk-actions-bar {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: none;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .status-selector {
        width: 150px;
    }
</style>

<div class="container-fluid">
    <div class="page-wrapper">

        <!-- Header with Current Date -->
        <div class="card shadow-sm p-3 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-dark m-0">Hostel Manager Dashboard</h3>
                    <div class="text-muted mt-1">
                        <i class="bi bi-calendar"></i> 
                        <span id="currentDate"><?php echo e(now()->format('Y-m-d')); ?></span> | 
                        <span id="currentTime"><?php echo e(now()->format('H:i:s')); ?></span>
                    </div>
                </div>
                <div class="date-display">
                    <i class="bi bi-clock-history"></i> Last Updated: <?php echo e(now()->format('Y-m-d H:i')); ?>

                </div>
            </div>
        </div>

        <!-- Analytics Overview -->
        <div class="card shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3">Analytics Overview</h5>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="analytics-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white-50">Avg. Processing Time</h6>
                                <h3 id="avgProcessingTime" class="fw-bold">0h</h3>
                            </div>
                            <i class="bi bi-speedometer2 fs-2 opacity-75"></i>
                        </div>
                        <small class="text-white-75">This Month</small>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="analytics-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white-50">Clearance Rate</h6>
                                <h3 id="clearanceRate" class="fw-bold">0%</h3>
                            </div>
                            <i class="bi bi-graph-up fs-2 opacity-75"></i>
                        </div>
                        <small class="text-white-75">Approval Percentage</small>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="analytics-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white-50">Active Requests</h6>
                                <h3 id="activeRequests" class="fw-bold">0</h3>
                            </div>
                            <i class="bi bi-activity fs-2 opacity-75"></i>
                        </div>
                        <small class="text-white-75">Last 7 days</small>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="analytics-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white-50">Completion Time</h6>
                                <h3 id="completionTime" class="fw-bold">0d</h3>
                            </div>
                            <i class="bi bi-calendar-check fs-2 opacity-75"></i>
                        </div>
                        <small class="text-white-75">Average Days</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Filters -->
        <div class="card p-4 shadow-sm mb-4">
            <h5 class="fw-semibold mb-2">Overview Filters</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Year</label>
                    <select id="overviewYear" class="form-select">
                        <?php for($y = 2020; $y <= now()->year; $y++): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Month</label>
                    <select id="overviewMonth" class="form-select">
                        <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php echo e($m == now()->month ? 'selected' : ''); ?>>
                                <?php echo e(date('F', mktime(0,0,0,$m,1))); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Time Range</label>
                    <select id="overviewRange" class="form-select">
                        <option value="month">This Month</option>
                        <option value="week">This Week</option>
                        <option value="today">Today</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="applyOverview" class="btn btn-primary w-100">Apply</button>
                </div>
            </div>
            
            <!-- Custom Date Range (Hidden by Default) -->
            <div id="customRangeSection" class="row g-3 mt-3 d-none">
                <div class="col-md-3">
                    <label>Start Date</label>
                    <input type="date" id="startDate" class="form-control" value="<?php echo e(now()->subDays(30)->format('Y-m-d')); ?>">
                </div>
                <div class="col-md-3">
                    <label>End Date</label>
                    <input type="date" id="endDate" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>">
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div id="pendingCard" class="card stat-card pending p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted">Pending Requests</h6>
                            <h2 id="pendingCount" class="text-primary fw-bold">0</h2>
                            <small class="text-muted" id="pendingChange">+0% from last month</small>
                        </div>
                        <i class="bi bi-clock text-primary fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div id="approvedCard" class="card stat-card approved p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted">Approved This Month</h6>
                            <h2 id="approvedCount" class="text-success fw-bold">0</h2>
                            <small class="text-muted" id="approvedChange">+0% from last month</small>
                        </div>
                        <i class="bi bi-check-circle text-success fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div id="rejectedCard" class="card stat-card rejected p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted">Rejected This Month</h6>
                            <h2 id="rejectedCount" class="text-danger fw-bold">0</h2>
                            <small class="text-muted" id="rejectedChange">+0% from last month</small>
                        </div>
                        <i class="bi bi-x-circle text-danger fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Chart -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm p-4">
                    <h5 class="fw-semibold mb-3">Request Trends</h5>
                    <div class="chart-container">
                        <canvas id="requestTrendsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-4">
                    <h5 class="fw-semibold mb-3">Status Distribution</h5>
                    <div class="chart-container">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Panel -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-semibold mb-3">Bulk Actions</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Quick Filter for Bulk Actions</label>
                    <select id="bulkFilterStatus" class="form-select">
                        <option value="pending">Pending Only</option>
                        <option value="all">All Status</option>
                        <option value="approved">Approved Only</option>
                        <option value="rejected">Rejected Only</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Bulk Action</label>
                    <select id="bulkAction" class="form-select">
                        <option value="">Select Action</option>
                        <option value="approve">Approve Selected</option>
                        <option value="reject">Reject Selected</option>
                        <option value="pending">Mark as Pending</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="applyBulkAction" disabled>
                        <i class="bi bi-check-circle"></i> Apply to Selected
                    </button>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                        <label class="form-check-label" for="selectAllCheckbox">
                            Select all records on this page
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-semibold mb-3">Advanced Filters</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Course</label>
                    <select id="filterCourse" class="form-select">
                        <option value="">All Courses</option>
                        <?php $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>"><?php echo e($course->course_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Intake</label>
                    <select id="filterIntake" class="form-select">
                        <option value="">All Intakes</option>
                        <?php $__currentLoopData = $intakes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $intake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($intake->id); ?>"><?php echo e($intake->intake_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Sort</label>
                    <select id="filterSort" class="form-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name_asc">Name A-Z</option>
                        <option value="name_desc">Name Z-A</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-md-3">
                    <label>Date Range</label>
                    <select id="dateRange" class="form-select">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" id="resetFilters">Reset</button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-success w-100" id="exportDataBtn">
                        <i class="bi bi-download"></i> Export Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="card shadow-sm p-3 mb-4 bg-white">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input id="searchInput" type="text" class="form-control p-3" 
                       placeholder="Search by Student Name, ID, Course, or Intake...">
                <button class="btn btn-outline-secondary" type="button" id="clearSearch">Clear</button>
            </div>
        </div>

        <!-- Action List -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-semibold m-0">Hostel Clearance Requests</h5>
                    <small class="text-muted" id="selectedCountInfo">0 selected</small>
                </div>
                <div>
                    <span class="badge bg-primary me-2" id="actionCount">0 requests</span>
                    <button class="btn btn-sm btn-outline-primary" id="quickApproveBtn" style="display: none;">
                        <i class="bi bi-check-lg"></i> Quick Approve Selected
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                            </th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Requested At</th>
                            <th>Days Pending</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="actionListTable">
                        <tr id="loadingRow">
                            <td colspan="8" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="paginationContainer" class="d-flex justify-content-center mt-3"></div>
            </div>
        </div>

        <!-- Recent Clearances -->
        <div class="card shadow-sm p-4 mb-5 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold m-0">Recent Hostel Clearances</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="exportRecentData()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>Processing Time</th>
                        </tr>
                    </thead>
                    <tbody id="recentClearanceTable"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Bulk Actions Bar (Fixed at Bottom) -->
<div class="bulk-actions-bar" id="bulkActionsBar">
    <div class="d-flex align-items-center">
        <span class="me-3 fw-bold" id="selectedCount">0 items selected</span>
        <div class="action-buttons">
            <select id="bulkStatusChange" class="form-select status-selector me-2">
                <option value="">Change Status To...</option>
                <option value="approved">Approve</option>
                <option value="rejected">Reject</option>
                <option value="pending">Mark as Pending</option>
            </select>
            <button class="btn btn-success btn-sm" id="bulkApplyBtn" disabled>
                <i class="bi bi-check-circle"></i> Apply
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="bulkClearBtn">
                <i class="bi bi-x-circle"></i> Clear
            </button>
        </div>
    </div>
</div>

<!-- MODAL: STATUS LIST -->
<div class="modal fade" id="statusListModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-white">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="statusModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="statusListContainer"></div>
                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary" id="loadMoreBtn">Load More</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: VIEW DETAILS -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-white">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Clearance Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><b>Student Name:</b> <span id="detailStudent"></span></p>
                        <p><b>Student ID:</b> <span id="detailStudentId"></span></p>
                        <p><b>Course:</b> <span id="detailCourse"></span></p>
                        <p><b>Intake:</b> <span id="detailIntake"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><b>Status:</b> <span id="detailStatus"></span></p>
                        <p><b>Requested At:</b> <span id="detailRequested"></span></p>
                        <p><b>Updated At:</b> <span id="detailUpdated"></span></p>
                        <p><b>Processing Time:</b> <span id="detailProcessingTime"></span></p>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Action History</h6>
                    <div id="actionHistory" class="border rounded p-3 bg-light"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: BULK ACTION CONFIRMATION -->
<div class="modal fade" id="bulkConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Bulk Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkConfirmMessage">Are you sure you want to perform this action?</p>
                <div class="mb-3">
                    <label for="bulkRemarks" class="form-label">Remarks (Optional)</label>
                    <textarea id="bulkRemarks" class="form-control" rows="3" placeholder="Add remarks for this bulk action..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: SINGLE ACTION -->
<div class="modal fade" id="singleActionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Request Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="singleStatus" class="form-label">Status</label>
                    <select id="singleStatus" class="form-select">
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                        <option value="pending">Mark as Pending</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="singleRemarks" class="form-label">Remarks</label>
                    <textarea id="singleRemarks" class="form-control" rows="3" placeholder="Add remarks..."></textarea>
                </div>
                <input type="hidden" id="singleRequestId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSingleAction">Update</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let requestTrendsChart, statusDistributionChart;
let currentPage = 1;
let totalPages = 1;
let selectedRequests = new Set();
let currentBulkAction = '';
let allRequestIds = [];

document.addEventListener("DOMContentLoaded", function() {
    // Update real-time clock
    updateClock();
    setInterval(updateClock, 1000);
    
    // Initialize charts
    initCharts();
    
    // Load initial data
    loadOverview();
    loadAnalytics();
    loadActionList();
    loadRecent();
    
    // Event Listeners
    $("#applyOverview").click(function() {
        loadOverview();
        loadAnalytics();
    });
    
    $("#overviewRange").change(function() {
        if ($(this).val() === 'custom') {
            $("#customRangeSection").removeClass('d-none');
        } else {
            $("#customRangeSection").addClass('d-none');
        }
    });
    
    $("#searchInput").keyup(debounce(function() {
        if ($(this).val().length > 2 || $(this).val().length === 0) {
            currentPage = 1;
            loadActionList();
        }
    }, 300));
    
    $("#clearSearch").click(function() {
        $("#searchInput").val('');
        currentPage = 1;
        loadActionList();
    });
    
    $("#applyFilters").click(() => {
        currentPage = 1;
        loadActionList();
    });
    
    $("#resetFilters").click(() => {
        $("#filterCourse").val('');
        $("#filterIntake").val('');
        $("#filterStatus").val('');
        $("#filterSort").val('newest');
        $("#dateRange").val('all');
        $("#bulkFilterStatus").val('pending');
        currentPage = 1;
        loadActionList();
    });
    
    $("#pendingCard").click(() => openStatusModal("pending"));
    $("#approvedCard").click(() => openStatusModal("approved"));
    $("#rejectedCard").click(() => openStatusModal("rejected"));
    
    // Bulk action handlers
    $("#bulkFilterStatus").change(function() {
        currentPage = 1;
        loadActionList();
    });
    
    $("#bulkAction").change(function() {
        $("#applyBulkAction").prop('disabled', !$(this).val());
    });
    
    $("#applyBulkAction").click(function() {
        const action = $("#bulkAction").val();
        if (action && selectedRequests.size > 0) {
            showBulkConfirmModal(action, Array.from(selectedRequests));
        }
    });
    
    $("#selectAllCheckbox").change(function() {
        const isChecked = $(this).prop('checked');
        $('.request-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            allRequestIds.forEach(id => selectedRequests.add(id));
        } else {
            selectedRequests.clear();
        }
        
        updateSelectionUI();
    });
    
    $("#selectAllHeader").change(function() {
        const isChecked = $(this).prop('checked');
        $('.request-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            allRequestIds.forEach(id => selectedRequests.add(id));
        } else {
            selectedRequests.clear();
        }
        
        updateSelectionUI();
    });
    
    // Quick approve button
    $("#quickApproveBtn").click(function() {
        if (selectedRequests.size > 0) {
            showBulkConfirmModal('approve', Array.from(selectedRequests));
        }
    });
    
    // Export button
    $("#exportDataBtn").click(function() {
        exportFilteredData();
    });
    
    // Bulk actions bar
    $("#bulkStatusChange").change(function() {
        $("#bulkApplyBtn").prop('disabled', !$(this).val());
    });
    
    $("#bulkApplyBtn").click(function() {
        const status = $("#bulkStatusChange").val();
        if (status && selectedRequests.size > 0) {
            showBulkConfirmModal(status, Array.from(selectedRequests));
        }
    });
    
    $("#bulkClearBtn").click(function() {
        clearSelection();
    });
    
    // Single action modal
    $("#confirmSingleAction").click(function() {
        const requestId = $("#singleRequestId").val();
        const status = $("#singleStatus").val();
        const remarks = $("#singleRemarks").val();
        
        updateRequestStatus(requestId, status, remarks);
    });
    
    // Bulk confirm modal
    $("#confirmBulkAction").click(function() {
        const remarks = $("#bulkRemarks").val();
        performBulkAction(currentBulkAction, selectedRequests, remarks);
    });
});

function updateClock() {
    const now = new Date();
    $("#currentTime").text(now.toLocaleTimeString());
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function initCharts() {
    const trendsCtx = document.getElementById('requestTrendsChart').getContext('2d');
    requestTrendsChart = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Requests',
                data: [],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    const distCtx = document.getElementById('statusDistributionChart').getContext('2d');
    statusDistributionChart = new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Rejected'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: ['#0d6efd', '#198754', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function loadOverview() {
    let year = $("#overviewYear").val();
    let month = $("#overviewMonth").val();
    let range = $("#overviewRange").val();
    let startDate = $("#startDate").val();
    let endDate = $("#endDate").val();
    
    $.get("<?php echo e(route('api.hostel.manager.overview')); ?>", {
        year, month, range, startDate, endDate
    }, function(data) {
        $("#pendingCount").text(data.totalPending);
        $("#approvedCount").text(data.monthly.approved);
        $("#rejectedCount").text(data.monthly.rejected);
        
        // Update change percentages
        $("#pendingChange").text(data.change.pending + " from last month");
        $("#approvedChange").text(data.change.approved + " from last month");
        $("#rejectedChange").text(data.change.rejected + " from last month");
        
        // Update charts
        updateCharts(data.trends, data.distribution);
    });
}

function loadAnalytics() {
    let year = $("#overviewYear").val();
    let month = $("#overviewMonth").val();
    
    $.get("<?php echo e(route('api.hostel.manager.analytics')); ?>", {year, month}, function(data) {
        $("#avgProcessingTime").text(data.avg_processing_time + "h");
        $("#clearanceRate").text(data.clearance_rate + "%");
        $("#activeRequests").text(data.active_requests);
        $("#completionTime").text(data.avg_completion_time + "d");
    });
}

function updateCharts(trends, distribution) {
    if (trends) {
        requestTrendsChart.data.labels = trends.labels;
        requestTrendsChart.data.datasets[0].data = trends.data;
        requestTrendsChart.update();
    }
    
    if (distribution) {
        statusDistributionChart.data.datasets[0].data = [
            distribution.pending,
            distribution.approved,
            distribution.rejected
        ];
        statusDistributionChart.update();
    }
}

function loadActionList(page = 1) {
    let params = {
        page: page,
        course_id: $('#filterCourse').val(),
        intake_id: $('#filterIntake').val(),
        status: $('#filterStatus').val(),
        sort: $('#filterSort').val(),
        date_range: $('#dateRange').val(),
        search: $('#searchInput').val(),
        bulk_filter: $('#bulkFilterStatus').val()
    };
    
    $("#loadingRow").show();
    
    $.get("<?php echo e(route('api.hostel.manager.action.list')); ?>", params, function(response) {
        renderActionList(response.data);
        currentPage = response.current_page;
        totalPages = response.last_page;
        $("#actionCount").text(response.total + " requests");
        renderPagination();
        $("#loadingRow").hide();
        
        // Update all request IDs for selection
        allRequestIds = response.data.map(r => r.id);
    }).fail(function() {
        $("#loadingRow").hide();
        $("#actionListTable").html('<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data</td></tr>');
    });
}

function renderActionList(data) {
    let rows = "";
    allRequestIds = [];
    
    if (data.length === 0) {
        rows = `<tr><td colspan="8" class="text-center py-4 text-muted">No requests found</td></tr>`;
    } else {
        data.forEach(r => {
            allRequestIds.push(r.id);
            let daysPending = r.days_pending || 'N/A';
            let isSelected = selectedRequests.has(r.id);
            
            rows += `
                <tr data-request-id="${r.id}">
                    <td>
                        <input type="checkbox" class="form-check-input request-checkbox" 
                               data-request-id="${r.id}" ${isSelected ? 'checked' : ''}>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-primary text-white rounded-circle me-2" 
                                 style="width: 32px; height: 32px; line-height: 32px; text-align: center;">
                                ${r.student?.full_name?.charAt(0) || '?'}
                            </div>
                            <div>
                                <div class="fw-semibold">${r.student?.full_name || 'N/A'}</div>
                                <small class="text-muted">${r.student?.student_id || ''}</small>
                            </div>
                        </div>
                    </td>
                    <td>${r.course?.course_name || 'N/A'}</td>
                    <td>${r.intake?.intake_name || 'N/A'}</td>
                    <td>${formatDate(r.requested_at)}</td>
                    <td>
                        <span class="badge ${daysPending > 7 ? 'bg-danger' : 'bg-warning'}">
                            ${daysPending} days
                        </span>
                    </td>
                    <td><span class="badge-status badge-${r.status}">${r.status}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="openDetailModal(
                                '${r.student?.full_name || ''}',
                                '${r.student?.student_id || ''}',
                                '${r.course?.course_name || ''}',
                                '${r.intake?.intake_name || ''}',
                                '${r.status}',
                                '${r.requested_at}',
                                '${r.updated_at || r.requested_at}',
                                '${r.processing_time || ''}'
                            )">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="showSingleActionModal(${r.id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $("#actionListTable").html(rows);
    
    // Add event listeners to checkboxes
    $('.request-checkbox').change(function() {
        const requestId = $(this).data('request-id');
        if ($(this).prop('checked')) {
            selectedRequests.add(requestId);
        } else {
            selectedRequests.delete(requestId);
        }
        updateSelectionUI();
    });
    
    // Update header checkbox state
    const allChecked = allRequestIds.length > 0 && allRequestIds.every(id => selectedRequests.has(id));
    $("#selectAllHeader").prop('checked', allChecked);
    $("#selectAllCheckbox").prop('checked', allChecked);
    
    updateSelectionUI();
}

function updateSelectionUI() {
    const selectedCount = selectedRequests.size;
    
    // Update counts
    $("#selectedCount").text(selectedCount + " item" + (selectedCount !== 1 ? 's' : '') + " selected");
    $("#selectedCountInfo").text(selectedCount + " selected");
    
    // Show/hide quick approve button
    if (selectedCount > 0) {
        $("#quickApproveBtn").show();
        $("#bulkActionsBar").show();
    } else {
        $("#quickApproveBtn").hide();
        $("#bulkActionsBar").hide();
    }
    
    // Enable/disable bulk action button
    $("#applyBulkAction").prop('disabled', selectedCount === 0 || !$("#bulkAction").val());
    $("#bulkApplyBtn").prop('disabled', selectedCount === 0 || !$("#bulkStatusChange").val());
}

function clearSelection() {
    selectedRequests.clear();
    $('.request-checkbox').prop('checked', false);
    $("#selectAllHeader").prop('checked', false);
    $("#selectAllCheckbox").prop('checked', false);
    updateSelectionUI();
}

function renderPagination() {
    if (totalPages <= 1) {
        $("#paginationContainer").html('');
        return;
    }
    
    let pagination = `
        <nav>
            <ul class="pagination">
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadActionList(${currentPage - 1}); return false;">Previous</a>
                </li>
    `;
    
    // Show limited pages
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    if (startPage > 1) {
        pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadActionList(1); return false;">1</a></li>`;
        if (startPage > 2) pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadActionList(${i}); return false;">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadActionList(${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    pagination += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadActionList(${currentPage + 1}); return false;">Next</a>
                </li>
            </ul>
        </nav>
    `;
    
    $("#paginationContainer").html(pagination);
}

function loadRecent() {
    $.get("<?php echo e(route('api.hostel.manager.recent.clearances')); ?>", function(data) {
        let rows = "";
        
        data.forEach(r => {
            rows += `
                <tr>
                    <td>${r.student?.full_name || 'N/A'}</td>
                    <td><code>${r.student?.student_id || ''}</code></td>
                    <td>${r.course?.course_name || 'N/A'}</td>
                    <td><span class="badge-status badge-${r.status}">${r.status}</span></td>
                    <td>${formatDate(r.updated_at)}</td>
                    <td>${r.processing_time || 'N/A'}</td>
                </tr>
            `;
        });
        
        $("#recentClearanceTable").html(rows);
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-CA') + ' ' + date.toLocaleTimeString('en-CA', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function openDetailModal(student, studentId, course, intake, status, requested, updated, processingTime) {
    $("#detailStudent").text(student);
    $("#detailStudentId").text(studentId);
    $("#detailCourse").text(course);
    $("#detailIntake").text(intake);
    $("#detailStatus").html(`<span class="badge-status badge-${status}">${status}</span>`);
    $("#detailRequested").text(formatDate(requested));
    $("#detailUpdated").text(formatDate(updated));
    $("#detailProcessingTime").text(processingTime || 'N/A');
    
    // Simulate action history
    let history = `
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker bg-primary"></div>
                <div class="timeline-content">
                    <small>${formatDate(requested)}</small>
                    <p>Request submitted</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-marker ${status === 'approved' ? 'bg-success' : 'bg-danger'}"></div>
                <div class="timeline-content">
                    <small>${formatDate(updated)}</small>
                    <p>Status updated to ${status}</p>
                </div>
            </div>
        </div>
    `;
    $("#actionHistory").html(history);
    
    $("#detailModal").modal("show");
}

function showSingleActionModal(requestId) {
    $("#singleRequestId").val(requestId);
    $("#singleRemarks").val('');
    $("#singleStatus").val('approved');
    $("#singleActionModal").modal("show");
}

function updateRequestStatus(requestId, status, remarks = '') {
    $.ajax({
        url: "<?php echo e(route('api.hostel.manager.update.status')); ?>",
        method: 'POST',
        data: {
            request_id: requestId,
            status: status,
            remarks: remarks,
            _token: "<?php echo e(csrf_token()); ?>"
        },
        success: function(response) {
            $("#singleActionModal").modal("hide");
            showToast('Success', 'Request status updated successfully', 'success');
            loadActionList(currentPage);
            loadOverview();
            loadRecent();
        },
        error: function() {
            showToast('Error', 'Failed to update request status', 'error');
        }
    });
}

function showBulkConfirmModal(action, requestIds) {
    currentBulkAction = action;
    const actionText = {
        'approve': 'Approve',
        'reject': 'Reject',
        'pending': 'Mark as Pending'
    }[action] || action;
    
    $("#bulkConfirmMessage").text(`Are you sure you want to ${actionText.toLowerCase()} ${requestIds.length} selected request(s)?`);
    $("#bulkRemarks").val('');
    $("#bulkConfirmModal").modal("show");
}

function performBulkAction(action, requestIds, remarks = '') {
    const requestIdsArray = Array.from(requestIds);
    
    $.ajax({
        url: "<?php echo e(route('api.hostel.manager.bulk.update')); ?>",
        method: 'POST',
        data: {
            request_ids: requestIdsArray,
            status: action,
            remarks: remarks,
            _token: "<?php echo e(csrf_token()); ?>"
        },
        success: function(response) {
            $("#bulkConfirmModal").modal("hide");
            showToast('Success', `Successfully updated ${response.updated} request(s)`, 'success');
            
            // Clear selection and refresh data
            clearSelection();
            loadActionList(currentPage);
            loadOverview();
            loadRecent();
            
            // Reset bulk action dropdown
            $("#bulkAction").val('');
            $("#applyBulkAction").prop('disabled', true);
        },
        error: function(xhr) {
            showToast('Error', 'Failed to update requests: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
        }
    });
}

function exportFilteredData() {
    let params = {
        course_id: $('#filterCourse').val(),
        intake_id: $('#filterIntake').val(),
        status: $('#filterStatus').val(),
        date_range: $('#dateRange').val(),
        search: $('#searchInput').val(),
        _token: "<?php echo e(csrf_token()); ?>"
    };
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "<?php echo e(route('api.hostel.manager.export')); ?>";
    
    Object.keys(params).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function exportRecentData() {
    alert('Export functionality would be implemented here');
}

function showToast(title, message, type = 'info') {
    // You can implement a toast notification here
    // For now, using alert
    alert(`${title}: ${message}`);
}

let nextPageUrl = null;
let currentStatus = "";

function openStatusModal(status) {
    currentStatus = status;
    $("#statusModalTitle").text("List of " + status + " Requests");
    nextPageUrl = null;
    loadStatusList(status);
    $("#statusListModal").modal("show");
}

function loadStatusList(status, append = false) {
    let url = nextPageUrl || "<?php echo e(route('api.hostel.manager.list.by.status')); ?>?status=" + status;
    
    $.get(url, function(res) {
        let html = "";
        res.data.forEach(r => {
            html += `
                <div class="p-3 border rounded mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <b>${r.student?.full_name || 'N/A'}</b> | 
                        ${r.course?.course_name || ''} | 
                        ${r.intake?.intake_name || ''} | 
                        <span class="badge-status badge-${r.status}">${r.status}</span>
                    </div>
                    <small class="text-muted">${formatDate(r.requested_at)}</small>
                </div>
            `;
        });
        
        if (append) {
            $("#statusListContainer").append(html);
        } else {
            $("#statusListContainer").html(html);
        }
        
        nextPageUrl = res.next_page_url;
        $("#loadMoreBtn").toggle(!!nextPageUrl);
    });
}

$("#loadMoreBtn").click(() => {
    loadStatusList(currentStatus, true);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/hostel_manager_dashboard.blade.php ENDPATH**/ ?>