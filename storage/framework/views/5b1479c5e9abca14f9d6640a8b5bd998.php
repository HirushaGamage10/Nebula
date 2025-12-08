<?php $__env->startSection('title', 'NEBULA | Hostel Manager Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<style>


    .stat-card {
        transition: 0.3s;
        cursor: pointer;
        background: white;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

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
</style>

<div class="container-fluid">

    <div class="page-wrapper">

        <!-- Header -->
        <div class="card shadow-sm p-3 mb-4 bg-white">
            <h3 class="fw-bold text-dark m-0">Hostel Manager Dashboard</h3>
        </div>

        <!-- Overview Filters -->
        <div class="card p-4 shadow-sm mb-4">
            <h5 class="fw-semibold mb-2">Overview Filters</h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <label>Year</label>
                    <select id="overviewYear" class="form-select">
                        <?php for($y = 2020; $y <= now()->year; $y++): ?>
                            <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Month</label>
                    <select id="overviewMonth" class="form-select">
                        <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>"><?php echo e(date('F', mktime(0,0,0,$m,1))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button id="applyOverview" class="btn btn-primary w-100">Apply</button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div id="pendingCard" class="card stat-card p-4 shadow-sm">
                    <h6 class="text-muted">Pending Requests</h6>
                    <h2 id="pendingCount" class="text-primary fw-bold">0</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div id="approvedCard" class="card stat-card p-4 shadow-sm">
                    <h6 class="text-muted">Approved This Month</h6>
                    <h2 id="approvedCount" class="text-success fw-bold">0</h2>
                </div>
            </div>

            <div class="col-md-4">
                <div id="rejectedCard" class="card stat-card p-4 shadow-sm">
                    <h6 class="text-muted">Rejected This Month</h6>
                    <h2 id="rejectedCount" class="text-danger fw-bold">0</h2>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-semibold mb-3">Filters</h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <label>Course</label>
                    <select id="filterCourse" class="form-select"><option value="">All Courses</option></select>
                </div>

                <div class="col-md-3">
                    <label>Intake</label>
                    <select id="filterIntake" class="form-select"><option value="">All Intakes</option></select>
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
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-3">
                    <label>Year</label>
                    <select id="filterYear" class="form-select">
                        <option value="">All</option>
                        <?php for($y = 2020; $y <= now()->year; $y++): ?>
                            <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Month</label>
                    <select id="filterMonth" class="form-select">
                        <option value="">All</option>
                        <?php $__currentLoopData = range(1,12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>"><?php echo e(date('F', mktime(0,0,0,$m,1))); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="card shadow-sm p-3 mb-4 bg-white">
            <input id="searchInput" type="text" class="form-control p-3" placeholder="Search by Student Name or ID">
        </div>

        <!-- Action List -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-semibold mb-3">Students Requiring Action</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Requested At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="actionListTable"></tbody>
                </table>
            </div>
        </div>

        <!-- Recent Clearances -->
        <div class="card shadow-sm p-4 mb-5 bg-white">
            <h5 class="fw-semibold mb-3">Recent Hostel Clearances</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody id="recentClearanceTable"></tbody>
                </table>
            </div>
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
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content bg-white">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Clearance Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p><b>Student:</b> <span id="detailStudent"></span></p>
                <p><b>Course:</b> <span id="detailCourse"></span></p>
                <p><b>Intake:</b> <span id="detailIntake"></span></p>
                <p><b>Status:</b> <span id="detailStatus"></span></p>
                <p><b>Requested At:</b> <span id="detailRequested"></span></p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>



<?php $__env->startSection('scripts'); ?>
<script>

document.addEventListener("DOMContentLoaded", function() {

    loadOverview();
    loadActionList();
    loadRecent();

    $("#applyOverview").click(loadOverview);

    $("#searchInput").keyup(function() {
        $.get("<?php echo e(route('api.hostel.manager.search')); ?>", {search: this.value}, renderActionList);
    });

    $("#applyFilters").click(() => {
        let params = {
            course_id: $('#filterCourse').val(),
            intake_id: $('#filterIntake').val(),
            status: $('#filterStatus').val(),
            sort: $('#filterSort').val(),
            year: $('#filterYear').val(),
            month: $('#filterMonth').val()
        };

        $.get("<?php echo e(route('api.hostel.manager.filter')); ?>", params, renderActionList);
    });

    $("#pendingCard").click(() => openStatusModal("pending"));
    $("#approvedCard").click(() => openStatusModal("approved"));
    $("#rejectedCard").click(() => openStatusModal("rejected"));

});


// Overview Metrics
function loadOverview() {
    let year = $("#overviewYear").val();
    let month = $("#overviewMonth").val();

    $.get("<?php echo e(route('api.hostel.manager.overview')); ?>", {year, month}, function(data) {
        $("#pendingCount").text(data.totalPending);
        $("#approvedCount").text(data.monthly.approved);
        $("#rejectedCount").text(data.monthly.rejected);
    });
}


// Action list
function loadActionList() {
    $.get("<?php echo e(route('api.hostel.manager.action.list')); ?>", renderActionList);
}

function renderActionList(data) {
    let rows = "";

    data.forEach(r => {
        rows += `
            <tr>
                <td>${r.student?.full_name ?? ''}</td>
                <td>${r.course?.course_name ?? ''}</td>
                <td>${r.intake?.intake_name ?? ''}</td>
                <td>${r.requested_at}</td>
                <td><span class="badge-status badge-${r.status}">${r.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary"
                        onclick="openDetailModal(
                            '${r.student?.full_name}',
                            '${r.course?.course_name}',
                            '${r.intake?.intake_name}',
                            '${r.status}',
                            '${r.requested_at}'
                        )">
                        View
                    </button>
                </td>
            </tr>
        `;
    });

    $("#actionListTable").html(rows);
}


// Recent list
function loadRecent() {
    $.get("<?php echo e(route('api.hostel.manager.recent.clearances')); ?>", function(data) {
        let rows = "";

        data.forEach(r => {
            rows += `
                <tr>
                    <td>${r.student?.full_name ?? ''}</td>
                    <td>${r.course?.course_name ?? ''}</td>
                    <td><span class="badge-status badge-${r.status}">${r.status}</span></td>
                    <td>${r.updated_at}</td>
                </tr>
            `;
        });

        $("#recentClearanceTable").html(rows);
    });
}



// Modal: Status list
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

    let url = nextPageUrl ?? "<?php echo e(route('api.hostel.manager.list.by.status')); ?>?status=" + status;

    $.get(url, function(res) {

        let html = "";
        res.data.forEach(r => {
            html += `
                <div class="p-3 border rounded mb-2">
                    <b>${r.student?.full_name}</b> |
                    ${r.course?.course_name ?? ''} |
                    ${r.intake?.intake_name ?? ''} |
                    <span class="badge-status badge-${r.status}">${r.status}</span>
                </div>
            `;
        });

        if (append) $("#statusListContainer").append(html);
        else $("#statusListContainer").html(html);

        nextPageUrl = res.next_page_url;

        if (!nextPageUrl) $("#loadMoreBtn").hide();
        else $("#loadMoreBtn").show();
    });
}

$("#loadMoreBtn").click(() => {
    loadStatusList(currentStatus, true);
});



// View Details Modal
function openDetailModal(student, course, intake, status, requestedAt) {

    $("#detailStudent").text(student);
    $("#detailCourse").text(course);
    $("#detailIntake").text(intake);
    $("#detailStatus").text(status);
    $("#detailRequested").text(requestedAt);

    $("#detailModal").modal("show");
}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/hostel_manager_dashboard.blade.php ENDPATH**/ ?>