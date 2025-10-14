<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Registration List</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container-fluid { width: 100%; }
        .card { border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-body { padding: 20px; }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        hr { border: none; border-top: 1px solid #ddd; margin: 1rem 0; }
        h2 { font-size: 24px; font-weight: bold; margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; }
        th { background: #f8f9fa; font-weight: bold; text-align: center; }
        .fw-bold { font-weight: bold; }
        .filter-details { margin-bottom: 16px; font-size: 14px; }
        .status-chip { font-size: 11px; padding: 2px 8px; border-radius: 10px; border: 1px solid #bbb; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center mb-4">Student Registration List</h2>
                <hr>

                <div class="filter-details">
                    <strong>Location:</strong> <?php echo e($locationText); ?><br>
                    <strong>Course:</strong> <?php echo e($courseText); ?><br>
                    <strong>Batch:</strong> <?php echo e($intakeText); ?><br>
                    <strong>View:</strong>
                    <?php if(($status ?? 'all') === 'all'): ?> All <?php else: ?> <?php echo e(ucfirst($status)); ?> <?php endif; ?>
                </div>

                <div class="mt-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course Registration ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td style="text-align:center;"><?php echo e($i + 1); ?></td>
                                    <td style="text-align:center;"><?php echo e($st->course_registration_id); ?></td>
                                    <td style="text-align:center;"><?php echo e($st->student_id); ?></td>
                                    <td><?php echo e($st->name); ?></td>
                                    <td style="text-align:center;">
                                        <span class="status-chip"><?php echo e($st->status); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;">No students found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-2" style="text-align:right;">
                        <span class="fw-bold">Total Students: <?php echo e($total_count ?? count($students ?? [])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/student_list_pdf.blade.php ENDPATH**/ ?>