

<?php $__env->startSection('title', 'Late Fee Approval'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Late Fee Approval</h2>
            <hr>
            

<div class="mb-4">
    <h5 class="mb-3">Select Student & Course</h5>

    <form method="GET" onsubmit="event.preventDefault(); goToApprovalPage();">
        <div class="row mb-3">

            
            <div class="col-md-5">
                <label for="student-nic" class="form-label fw-semibold">Student NIC</label>
                <input type="text" 
                       id="student-nic" 
                       name="student_nic" 
                       class="form-control" 
                       placeholder="Enter NIC" 
                       value="<?php echo e($studentNic ?? ''); ?>" 
                       required>
            </div>

            
            <div class="col-md-5">
                <label for="course_id" class="form-label fw-semibold">Course</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c['course_id']); ?>" 
                            <?php echo e(($courseId ?? '') == $c['course_id'] ? 'selected' : ''); ?>>
                            <?php echo e($c['course_name']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex w-100 gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Load</button>
                    <button type="button" 
                            class="btn btn-outline-secondary flex-fill"
                            onclick="window.location.href='<?php echo e(url('/late-fee/approval')); ?>'">
                        Clear
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>


    <?php if(isset($installments)): ?>

    
    <div class="mb-4">
        <h5 class="mb-3">Global Reduction - Feature Coming Soon</h5>
        <form method="POST" action="<?php echo e(route('latefee.approve.global', [$student->id_value ?? $studentId, $courseId])); ?>">
            <?php echo csrf_field(); ?>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Reduction Amount</label>
                    <input type="number" step="0.01" min="0.01" name="reduction_amount" class="form-control" required>
                </div>
                <div class="col-md-8">
                    <label>Approval Note</label>
                    <input type="text" name="approval_note" class="form-control">
                </div>
            </div>
            <button class="btn btn-success">Apply Global Reduction</button>
        </form>
    </div>

    
    <div class="mb-4">
        <h5 class="mb-3">Installment-wise Approval</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Installment #</th>
                        <th>Due Date</th>
                        <th>Final Amount</th>
                        <th>Calculated Late Fee</th>
                        <th>Approved Late Fee</th>
                        <th>Overdue (Calc - Approved)</th>
                        <th>Approval Note</th>
                        <th>History</th>
                        <th style="width: 220px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($installment->installment_number); ?></td>
                            <td><?php echo e($installment->formatted_due_date); ?></td>
                            <td class="text-primary fw-semibold"><?php echo e($installment->formatted_amount); ?></td>
                            <td class="text-warning fw-semibold">
                                LKR <?php echo e(number_format($installment->calculated_late_fee ?? 0, 2)); ?>

                            </td>
                            <td>
                                <?php if($installment->approved_late_fee !== null): ?>
                                    <span class="badge bg-success p-2">
                                        LKR <?php echo e(number_format($installment->approved_late_fee, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Not approved</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $calcFee = $installment->calculated_late_fee ?? 0;
                                    $approvedFee = $installment->approved_late_fee ?? 0;
                                    $overdue = $calcFee - $approvedFee;
                                ?>

                                <?php if($overdue > 0): ?>
                                    <span class="text-danger fw-bold">
                                        LKR <?php echo e(number_format($overdue, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-success fw-bold">LKR 0.00</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($installment->approval_note ?? '-'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#history-<?php echo e($installment->id); ?>">
                                    View History
                                </button>

                                <div id="history-<?php echo e($installment->id); ?>" class="collapse mt-2 text-start">
                                    <?php
                                        $histories = is_array($installment->approval_history)
                                            ? $installment->approval_history
                                            : json_decode($installment->approval_history ?? '[]', true);
                                    ?>

                                    <?php if(empty($histories)): ?>
                                        <small class="text-muted fst-italic">No history yet</small>
                                    <?php else: ?>
                                        <ul class="list-group list-group-flush small">
                                            <?php $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="list-group-item py-1">
                                                    <strong>LKR <?php echo e(number_format($h['approved_late_fee'], 2)); ?></strong>
                                                    (<?php echo e($h['approval_note'] ?? 'No note'); ?>) 
                                                    by <span class="fw-semibold"><?php echo e($h['approved_by'] ?? 'System'); ?></span>
                                                    <small class="text-muted d-block">on <?php echo e($h['approved_at'] ?? '-'); ?></small>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="<?php echo e(route('latefee.approve.installment', $installment->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="row g-2">
                                        <?php
                                            $isPast = \Carbon\Carbon::parse($installment->due_date)->isPast();
                                        ?>

                                        <div class="col-md-6">
                                            <input type="number" step="0.01" min="0.01" name="approved_late_fee" 
                                                class="form-control form-control-sm"
                                                placeholder="Approved Fee"
                                                value="<?php echo e($installment->approved_late_fee ?? ''); ?>"
                                                <?php echo e($isPast ? '' : 'disabled'); ?>>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="approval_note" 
                                                class="form-control form-control-sm"
                                                placeholder="Note"
                                                value="<?php echo e($installment->approval_note ?? ''); ?>"
                                                <?php echo e($isPast ? '' : 'disabled'); ?>>
                                        </div>

                                        <div class="col-12">
                                            <?php if($isPast): ?>
                                                <button class="btn btn-sm btn-primary w-100">
                                                    Approve
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary w-100" disabled
                                                        title="Approval only allowed after due date">
                                                    Approve
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                No installments found for this student & course.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const studentNicInput = document.getElementById("student-nic");
    const courseSelect = document.getElementById("course_id");

    // 1️⃣ Fetch and populate courses when NIC is entered or changed
    studentNicInput.addEventListener("blur", fetchCoursesForNIC);
    studentNicInput.addEventListener("change", fetchCoursesForNIC);

    function fetchCoursesForNIC() {
        const nic = studentNicInput.value.trim();
        if (!nic) return;

        fetch("<?php echo e(route('latefee.get.courses')); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
            },
            body: JSON.stringify({ student_nic: nic })
        })
        .then(res => res.json())
        .then(data => {
            courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";

            if (data.success && Array.isArray(data.courses)) {
                data.courses.forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.course_id;
                    opt.textContent = c.course_name;
                    courseSelect.appendChild(opt);
                });
            } else {
                alert("No courses found for this NIC.");
            }
        })
        .catch(err => console.error("Error fetching courses:", err));
    }

    // 2️⃣ Auto-fill when returning from backend (values from Blade)
    const prefilledNic = "<?php echo e($studentNic ?? ''); ?>";
    const prefilledCourseId = "<?php echo e($courseId ?? ''); ?>";

    if (prefilledNic) {
        studentNicInput.value = prefilledNic;

        // If NIC is filled, fetch courses and auto-select correct one
        fetch("<?php echo e(route('latefee.get.courses')); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
            },
            body: JSON.stringify({ student_nic: prefilledNic })
        })
        .then(res => res.json())
        .then(data => {
            courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";
            if (data.success && Array.isArray(data.courses)) {
                data.courses.forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.course_id;
                    opt.textContent = c.course_name;
                    if (c.course_id == prefilledCourseId) {
                        opt.selected = true;
                    }
                    courseSelect.appendChild(opt);
                });
            }
        })
        .catch(err => console.error("Error preloading courses:", err));
    }
});

// 3️⃣ Redirect to approval page
function goToApprovalPage() {
    const nic = document.getElementById("student-nic").value.trim();
    const courseId = document.getElementById("course_id").value;

    if (!nic || !courseId) {
        alert("Please enter NIC and select a course.");
        return;
    }

    const url = "<?php echo e(url('/late-fee/approval')); ?>/" + nic + "/" + courseId;
    window.location.href = url;
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/late_fee/approval.blade.php ENDPATH**/ ?>