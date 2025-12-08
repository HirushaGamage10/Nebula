<?php $__env->startSection('title', 'NEBULA | Project Tutor Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<style>
    .stat-card {
        transition: 0.2s;
        border-left: 4px solid transparent;
        background: white;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    .pending { border-left-color: #0d6efd }
    .approved { border-left-color: #198754 }
    .rejected { border-left-color: #dc3545 }

    .badge-status {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: bold;
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
            <h3 class="fw-bold">Project Tutor Dashboard</h3>
            <small class="text-muted">
                Reviewing project completion clearance requests
            </small>
        </div>

        <!-- KPI SUMMARY -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card pending p-4 shadow-sm">
                    <h6 class="text-muted">Pending Reviews</h6>
                    <h2 class="text-primary fw-bold"><?php echo e($pendingCount); ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card approved p-4 shadow-sm">
                    <h6 class="text-muted">Approved This Month</h6>
                    <h2 class="text-success fw-bold"><?php echo e($approvedCount); ?></h2>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card rejected p-4 shadow-sm">
                    <h6 class="text-muted">Rejected This Month</h6>
                    <h2 class="text-danger fw-bold"><?php echo e($rejectedCount); ?></h2>
                </div>
            </div>
        </div>

        <!-- STUDENT REVIEW LIST -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold m-0">Student Review List</h5>
                <span class="badge bg-primary"><?php echo e(count($pendingList)); ?> pending</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pendingList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($req->student->full_name); ?></td>
                            <td><code><?php echo e($req->student->student_id); ?></code></td>
                            <td><?php echo e($req->course->course_name); ?></td>
                            <td><?php echo e($req->intake->intake_name); ?></td>
                            <td><?php echo e($req->requested_at?->format('Y-m-d')); ?></td>
                            <td>
                                <span class="badge-status badge-pending"><?php echo e($req->status); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('project.clearance.management', $req->id)); ?>"
                                   class="btn btn-sm btn-primary">
                                   Review
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-muted py-3">No pending project clearances</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RECENT PROJECT CLEARANCES -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-semibold mb-3">Recent Project Clearance Updates</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($req->student->full_name); ?></td>
                            <td><code><?php echo e($req->student->student_id); ?></code></td>
                            <td><?php echo e($req->course->course_name); ?></td>
                            <td>
                                <span class="badge-status badge-<?php echo e($req->status); ?>">
                                    <?php echo e($req->status); ?>

                                </span>
                            </td>
                            <td><?php echo e($req->updated_at?->format('Y-m-d H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/dashboards/project_tutor_dashboard.blade.php ENDPATH**/ ?>