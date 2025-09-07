
<?php $__env->startSection('title','Edit Payment Plan'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Edit Payment Plan</h2>

            
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('payment.plan.update', $plan->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <select name="location" class="form-select" required>
                        <?php $__currentLoopData = ['Welisara','Moratuwa','Peradeniya']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc); ?>" <?php if($plan->location==$loc): echo 'selected'; endif; ?>><?php echo e($loc); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->course_id); ?>" <?php if($plan->course_id==$c->course_id): echo 'selected'; endif; ?>><?php echo e($c->course_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Intake</label>
                    <select name="intake_id" class="form-select">
                        <option value="">None</option>
                        <?php $__currentLoopData = $intakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->intake_id); ?>" <?php if($plan->intake_id==$i->intake_id): echo 'selected'; endif; ?>><?php echo e($i->intake_id); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Registration Fee</label>
                    <input type="number" name="registration_fee" class="form-control" value="<?php echo e($plan->registration_fee); ?>" required min="0" step="0.01">
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Local Fee</label>
                    <input type="number" name="local_fee" class="form-control" value="<?php echo e($plan->local_fee); ?>" required min="0" step="0.01">
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Franchise Fee</label>
                    <input type="number" name="international_fee" class="form-control" value="<?php echo e($plan->international_fee); ?>" required min="0" step="0.01">
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text" name="international_currency" class="form-control" value="<?php echo e($plan->international_currency); ?>" required>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">SSCL Tax</label>
                    <input type="number" name="sscl_tax" class="form-control" value="<?php echo e($plan->sscl_tax); ?>" min="0" step="0.01">
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Bank Charges</label>
                    <input type="number" name="bank_charges" class="form-control" value="<?php echo e($plan->bank_charges); ?>" min="0" step="0.01">
                </div>

                
                <div class="mb-3 form-check">
                    <input type="checkbox" name="apply_discount" value="1" class="form-check-input" id="applyDiscountCheckbox"
                           <?php echo e($plan->apply_discount ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="applyDiscountCheckbox">Apply Full Payment Discount</label>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Discount (%)</label>
                    <input type="number" class="form-control" name="discount" value="<?php echo e($plan->discount); ?>" min="0" step="0.01">
                </div>

                
                <div class="mb-3 form-check">
                    <input type="checkbox" name="installment_plan" value="1" class="form-check-input" id="installmentPlanCheckbox"
                           <?php echo e($plan->installment_plan ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="installmentPlanCheckbox">Enable Installment Plan</label>
                </div>

                
                <div class="mb-3">
                    <label class="form-label">Installments</label>

                    <table class="table table-bordered bg-white">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Due Date</th>
                                <th>Local (LKR)</th>
                                <th>International (<?php echo e($plan->international_currency); ?>)</th>
                                <th>Tax?</th>
                            </tr>
                        </thead>
                        <tbody id="installmentsTableBody">
                            <?php $__empty_1 = true; $__currentLoopData = $installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($i+1); ?></td>
                                    <td><input type="date" name="installments[<?php echo e($i); ?>][due_date]" value="<?php echo e($inst['due_date'] ?? ''); ?>" class="form-control"></td>
                                    <td><input type="number" step="0.01" name="installments[<?php echo e($i); ?>][local_amount]" value="<?php echo e($inst['local_amount'] ?? ''); ?>" class="form-control"></td>
                                    <td><input type="number" step="0.01" name="installments[<?php echo e($i); ?>][international_amount]" value="<?php echo e($inst['international_amount'] ?? ''); ?>" class="form-control"></td>
                                    <td class="text-center">
                                        <input type="checkbox" name="installments[<?php echo e($i); ?>][apply_tax]" value="1" <?php if(!empty($inst['apply_tax'])): echo 'checked'; endif; ?>>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No installments defined</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-primary" onclick="addInstallmentRow()">+ Add Row</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeLastRow()">Remove Last</button>
                    </div>
                </div>

                
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>


<script>
function addInstallmentRow() {
    let tbody = document.getElementById('installmentsTableBody');
    let index = tbody.rows.length;
    let row = tbody.insertRow();

    row.innerHTML = `
        <td>${index+1}</td>
        <td><input type="date" name="installments[${index}][due_date]" class="form-control"></td>
        <td><input type="number" step="0.01" name="installments[${index}][local_amount]" class="form-control"></td>
        <td><input type="number" step="0.01" name="installments[${index}][international_amount]" class="form-control"></td>
        <td class="text-center"><input type="checkbox" name="installments[${index}][apply_tax]" value="1"></td>
    `;
}

function removeLastRow() {
    let tbody = document.getElementById('installmentsTableBody');
    if (tbody.rows.length > 0) {
        tbody.deleteRow(tbody.rows.length - 1);
    }
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/payment_plan_edit.blade.php ENDPATH**/ ?>