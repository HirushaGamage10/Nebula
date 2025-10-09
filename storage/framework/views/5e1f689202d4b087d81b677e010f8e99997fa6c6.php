

<?php $__env->startSection('title', 'NEBULA | Payment Plans'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="mb-3">Payment Plans</h2>
            <form method="GET" action="<?php echo e(route('payment.plan.index')); ?>" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Location</label>
                    <select name="location" class="form-select">
                        <option value="">All</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc); ?>" <?php if(request('location')===$loc): echo 'selected'; endif; ?>><?php echo e($loc); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Course</label>
                    <select name="course_id" id="filter-course" class="form-select">
                        <option value="">All</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->course_id); ?>" <?php if((string)request('course_id')===(string)$c->course_id): echo 'selected'; endif; ?>>
                                <?php echo e($c->course_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Intake</label>
                    <select name="intake_id" id="filter-intake" class="form-select" <?php if(!request('course_id')): echo 'disabled'; endif; ?>>
                        <option value="">All</option>
                        <?php $__currentLoopData = $intakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->intake_id); ?>" <?php if((string)request('intake_id')===(string)$i->intake_id): echo 'selected'; endif; ?>>
                                <?php echo e($i->batch ?? 'Batch ' . $i->intake_id); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Location</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th class="text-end">Reg. Fee (LKR)</th>
                        <th class="text-end">Local Fee (LKR)</th>
                        <th class="text-end">Franchise</th>
                        <th>Discount</th>
                        <th>Installments</th>
                        <th>Created</th>
                        <th style="width: 130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        // Ensure installments is an array even if stored as string
                        $items = is_array($plan->installments) ? $plan->installments : (json_decode($plan->installments, true) ?? []);
                        $count = count($items);

                        $firstDue = $count ? ($items[0]['due_date'] ?? null) : null;
                        $lastDue  = $count ? ($items[array_key_last($items)]['due_date'] ?? null) : null;

                        $totalLocal = 0; $totalIntl = 0;
                        foreach ($items as $it) {
                            $totalLocal += (float)($it['local_amount'] ?? 0);
                            $totalIntl  += (float)($it['international_amount'] ?? 0);
                        }
                    ?>
                    <tr>
                        <td><?php echo e($plan->id); ?></td>
                        <td><?php echo e($plan->location); ?></td>
                        <td><?php echo e(optional($plan->course)->course_name ?? '—'); ?></td>
                        <td><?php echo e(optional($plan->intake)->batch ?? '—'); ?></td>
                        <td class="text-end"><?php echo e(number_format($plan->registration_fee, 2, '.', ',')); ?></td>
                        <td class="text-end"><?php echo e(number_format($plan->local_fee, 2, '.', ',')); ?></td>
                        <td class="text-end">
                            <?php echo e(number_format($plan->international_fee, 2, '.', ',')); ?>

                            <small class="text-muted"><?php echo e($plan->international_currency); ?></small>
                        </td>
                        <td>
                            <?php if($plan->apply_discount): ?>
                                <?php echo e(rtrim(rtrim(number_format($plan->discount ?? 0, 2, '.', ''), '0'), '.')); ?>%
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($plan->installment_plan): ?>
                                <button class="btn btn-sm btn-outline-secondary"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#inst-<?php echo e($plan->id); ?>">
                                    View <?php echo e($count); ?> Installments
                                </button>
                                <div class="small text-muted mt-1">
                                    <?php if($count): ?>
                                        <?php echo e($firstDue); ?> → <?php echo e($lastDue); ?>

                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small text-muted"><?php echo e($plan->created_at?->format('Y-m-d H:i')); ?></div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                
                                
                                <a href="<?php echo e(route('payment.plan.edit',$plan->id)); ?>" class="btn btn-sm btn-warning">Edit</a>

                                
                                
                            </div>
                        </td>
                    </tr>
                    <?php if($plan->installment_plan): ?>
                        <tr class="collapse" id="inst-<?php echo e($plan->id); ?>">
                            <td colspan="11">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-2">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>#</th>
                                                <th>Due Date</th>
                                                <th class="text-end">Local (LKR)</th>
                                                <th class="text-end">International (<?php echo e($plan->international_currency); ?>)</th>
                                                <th>Tax?</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_2 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <tr>
                                                    <td><?php echo e($it['installment_number'] ?? ''); ?></td>
                                                    <td><?php echo e($it['due_date'] ?? ''); ?></td>
                                                    <td class="text-end"><?php echo e(number_format((float)($it['local_amount'] ?? 0), 2, '.', ',')); ?></td>
                                                    <td class="text-end"><?php echo e(number_format((float)($it['international_amount'] ?? 0), 2, '.', ',')); ?></td>
                                                    <td>
                                                        <?php if(!empty($it['apply_tax'])): ?>
                                                            <span class="badge bg-success">Yes</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <tr><td colspan="5" class="text-center text-muted">No installments found</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="fw-semibold">
                                                <td colspan="2" class="text-end">Totals:</td>
                                                <td class="text-end"><?php echo e(number_format($totalLocal, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(number_format($totalIntl, 2, '.', ',')); ?></td>
                                                <td></td>
                                            </tr>
                                            <tr class="small text-muted">
                                                <td colspan="2" class="text-end">Required:</td>
                                                <td class="text-end"><?php echo e(number_format((float)$plan->local_fee, 2, '.', ',')); ?></td>
                                                <td class="text-end"><?php echo e(number_format((float)$plan->international_fee, 2, '.', ',')); ?></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="11" class="text-center text-muted">No payment plans found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing <?php echo e($plans->firstItem() ?? 0); ?>–<?php echo e($plans->lastItem() ?? 0); ?> of <?php echo e($plans->total()); ?>

                </div>
                <?php echo e($plans->links()); ?>

            </div>
        </div>
    </div>
</div>


<script>
// 1. When Location changes → update courses
document.querySelector('select[name="location"]')?.addEventListener('change', function () {
    const location = this.value;
    const courseSelect = document.getElementById('filter-course');
    const intakeSelect = document.getElementById('filter-intake');

    // reset course + intake
    courseSelect.innerHTML = '<option value="">All</option>';
    intakeSelect.innerHTML = '<option value="">All</option>';
    intakeSelect.disabled = true;

    if (!location) return;

    fetch("<?php echo e(route('courses.byLocation')); ?>", {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ location })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data.length) {
            data.data.forEach(course => {
                const opt = document.createElement('option');
                opt.value = course.course_id;
                opt.textContent = course.course_name;
                courseSelect.appendChild(opt);
            });
        }
    });
});

// 2. When Course changes → update intakes
document.getElementById('filter-course')?.addEventListener('change', function () {
    const courseId = this.value;
    const location = document.querySelector('select[name="location"]').value;
    const intakeSelect = document.getElementById('filter-intake');

    intakeSelect.disabled = true;
    intakeSelect.innerHTML = '<option value="">Loading...</option>';

    if (!courseId || !location) {
        intakeSelect.innerHTML = '<option value="">All</option>';
        intakeSelect.disabled = false;
        return;
    }

   fetch("<?php echo e(route('intakes.byCourse')); ?>", {
    method: 'POST',
    headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
    },
    body: JSON.stringify({ course_id: courseId, location })
})
.then(res => res.json())
.then(data => {
    console.log("Intake API response:", data); // 🔎 Debug here
    intakeSelect.innerHTML = '<option value="">All</option>';
    if (data.success && data.data.length) {
        data.data.forEach(intake => {
            const opt = document.createElement('option');
            opt.value = intake.intake_id;
            opt.textContent = intake.batch ? intake.batch : `Batch ${intake.intake_id}`;

            intakeSelect.appendChild(opt);
        });
    }
    intakeSelect.disabled = false;
})

    .catch(() => {
        intakeSelect.innerHTML = '<option value="">All</option>';
        intakeSelect.disabled = false;
    });
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Admin\Desktop\Nebula-Project\Nebula\resources\views/payment_plan_index.blade.php ENDPATH**/ ?>