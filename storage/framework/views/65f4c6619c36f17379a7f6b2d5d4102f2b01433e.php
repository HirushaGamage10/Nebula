

<?php $__env->startSection('title', 'Late Fee Approval'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h2>Late Fee Approval</h2>
    <hr>

    
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">Select Student & Course</div>
    <div class="card-body">
        <form method="GET" onsubmit="event.preventDefault(); goToApprovalPage();">
    <div class="row mb-3">
        <div class="col-md-5">
            <label for="student_nic">Student NIC</label>
            <input type="text" id="student-nic" name="student_nic" class="form-control" placeholder="Enter NIC" required>
        </div>

        <div class="col-md-5">
            <label for="course_id">Course</label>
            <select id="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Load</button>
        </div>
    </div>
</form>

    </div>
</div>


    <?php if(isset($installments)): ?>

    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Global Reduction - Feature Coming Soon</div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('latefee.approve.global', [$student->id_value ?? $studentId, $courseId])); ?>">
                <?php echo csrf_field(); ?>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Reduction Amount</label>
                        <input type="number" step="0.01" name="reduction_amount" class="form-control" required disabled>
                    </div>
                    <div class="col-md-8">
                        <label>Approval Note</label>
                        <input type="text" name="approval_note" class="form-control" disabled>
                    </div>
                </div>
                <button class="btn btn-success" disabled>Apply Global Reduction</button>
            </form>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header bg-dark text-white">Installment-wise Approval</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Installment #</th>
                        <th>Due Date</th>
                        <th>Final Amount</th>
                        <th>Calculated Late Fee</th>
                        <th>Approved Late Fee</th>
                        <th>Approval Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($installment->installment_number); ?></td>
                            <td><?php echo e($installment->formatted_due_date); ?></td>
                            <td><?php echo e($installment->formatted_amount); ?></td>
                            <td>LKR <?php echo e(number_format($installment->calculated_late_fee ?? 0, 2)); ?></td>
                            <td>
                                <?php if($installment->approved_late_fee !== null): ?>
                                    <span class="text-success fw-bold">
                                        LKR <?php echo e(number_format($installment->approved_late_fee, 2)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Not approved</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($installment->approval_note ?? '-'); ?></td>
                            <td>
                                <form method="POST" action="<?php echo e(route('latefee.approve.installment', $installment->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <input type="number" step="0.01" name="approved_late_fee" 
                                                   class="form-control" placeholder="Approved Fee"
                                                   value="<?php echo e($installment->approved_late_fee ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text" name="approval_note" class="form-control" 
                                                   placeholder="Note" value="<?php echo e($installment->approval_note ?? ''); ?>">
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-primary mt-1">Approve</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
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

<script>
document.getElementById("student-nic").addEventListener("blur", function() {
    let nic = this.value;
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
        let courseSelect = document.getElementById("course_id");
        courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";

        if (data.success) {
            data.courses.forEach(c => {
                let option = document.createElement("option");
                option.value = c.course_id;
                option.textContent = c.course_name;
                courseSelect.appendChild(option);
            });
        } else {
            alert("No courses found for this NIC.");
        }
    })
    .catch(err => console.error(err));
});

function goToApprovalPage() {
    let nic = document.getElementById("student-nic").value;
    let courseId = document.getElementById("course_id").value;

    if (!nic || !courseId) {
        alert("Please enter NIC and select a course.");
        return;
    }

    let url = "<?php echo e(url('/late-fee/approval')); ?>/" + nic + "/" + courseId;
    window.location.href = url;
}


document.addEventListener("DOMContentLoaded", function () {
    const studentNicInput = document.getElementById("student-nic");
    const courseSelect = document.getElementById("course-select");

    studentNicInput.addEventListener("change", function () {
        let nic = studentNicInput.value.trim();
        if (!nic) return;

        // Clear old options
        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';

        fetch("<?php echo e(route('latefee.get.courses')); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify({ student_nic: nic })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                data.courses.forEach(course => {
                    let opt = document.createElement("option");
                    opt.value = course.course_id;
                    opt.textContent = course.course_name + " (" + course.registration_date + ")";
                    courseSelect.appendChild(opt);
                });
            } else {
                alert(data.message || "No courses found for this NIC");
            }
        })
        .catch(err => console.error("Error fetching courses:", err));
    });
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/late_fee/approval.blade.php ENDPATH**/ ?>