<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h3, h4 { margin: 0; padding: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    <h3 style="text-align:center;">STATEMENT OF ACCOUNT</h3>
    <p><strong><?php echo e($student['id']); ?> - <?php echo e($student['name']); ?></strong></p>

    <p><strong>NIC:</strong> <?php echo e($student['nic']); ?></p>
    <p><strong>Course:</strong> <?php echo e($course['name']); ?></p>
    <p><strong>Intake:</strong> <?php echo e($course['intake']); ?></p>
    <p><strong>Registration Date:</strong> <?php echo e($course['registration_date']); ?></p>
    <p><strong>Date Issued:</strong> <?php echo e($generated_date); ?></p>

    <h4>Payment Details</h4>
    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th>Mode of Payment</th>
                <th>Receipt No</th>
                <th>Date</th>
                <th>Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <?php echo e($p['description']); ?>

                        <?php if(($p['amount'] ?? 0) == 0): ?>
                            (Outstanding)
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($p['method'] ?? '-'); ?></td>
                    <td><?php echo e($p['receipt_no'] ?? '-'); ?></td>
                    <td><?php echo e($p['date'] ?? '-'); ?></td>
                    <td style="text-align:right;"><?php echo e(number_format($p['amount'], 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No payment records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Amount:</strong> Rs. <?php echo e(number_format($totals['total_amount'], 2)); ?></p>
        <p><strong>Total Paid:</strong> Rs. <?php echo e(number_format($totals['total_paid'], 2)); ?></p>
        <p><strong>Total Outstanding:</strong> Rs. <?php echo e(number_format($totals['total_remaining'], 2)); ?></p>
    </div>
    <?php if($paymentPlan && $paymentPlan->installments->count()): ?>
    <h4>Student Payment Plan (LKR)</h4>
    <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse: collapse; font-size: 12px;">
        <thead style="background: #f2f2f2;">
            <tr>
                <th>#</th>
                <th>Due Date</th>
                <th>Base Amount</th>
                <th>Discount</th>
                <th>SLT Loan</th>
                <th>Final Amount</th>

            </tr>
        </thead>
        <tbody>
            <?php
                $sumBase   = 0;
                $sumDisc   = 0;
                $sumLoan   = 0;
                $sumFinal  = 0;
            ?>
            <?php $__currentLoopData = $paymentPlan->installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $sumBase  += $inst->base_amount ?? $inst->amount ?? 0;
                    $sumDisc  += $inst->discount_amount ?? 0;
                    $sumLoan  += $inst->slt_loan_amount ?? 0;
                    $sumFinal += $inst->final_amount ?? ($inst->base_amount ?? $inst->amount ?? 0);
                ?>
                <tr>
                    <td><?php echo e($inst->installment_number); ?></td>
                    <td><?php echo e($inst->formatted_due_date); ?></td>
                    <td><?php echo e(number_format($inst->base_amount ?? $inst->amount ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($inst->discount_amount ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($inst->slt_loan_amount ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($inst->final_amount ?? ($inst->base_amount ?? $inst->amount ?? 0), 2)); ?></td>

                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="2" align="right">TOTAL</td>
                <td><?php echo e(number_format($sumBase, 2)); ?></td>
                <td><?php echo e(number_format($sumDisc, 2)); ?></td>
                <td><?php echo e(number_format($sumLoan, 2)); ?></td>
                <td><?php echo e(number_format($sumFinal, 2)); ?></td>

            </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php if(!empty($courseInstallments)): ?>
    <h4>Course Installment Plan (Master)</h4>
    <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse: collapse; font-size: 12px;">
        <thead style="background: #f2f2f2;">
            <tr>
                <th>#</th>
                <th>Due Date</th>
                <th>Local Amount (LKR)</th>
                <th>Foreign Amount</th>
                <th>Currency</th>

            </tr>
        </thead>
        <tbody>
            <?php
                $sumLocal   = 0;
                $sumForeign = 0;
            ?>
            <?php $__currentLoopData = $courseInstallments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $sumLocal   += $inst['local_amount'] ?? 0;
                    $sumForeign += $inst['international_amount'] ?? 0;
                ?>
                <tr>
                    <td><?php echo e($inst['installment_number']); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($inst['due_date'])->format('d/m/Y')); ?></td>
                    <td><?php echo e(number_format($inst['local_amount'] ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format($inst['international_amount'] ?? 0, 2)); ?></td>
                    <td><?php echo e($coursePlan->international_currency ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="2" align="right">TOTAL</td>
                <td><?php echo e(number_format($sumLocal, 2)); ?></td>
                <td><?php echo e(number_format($sumForeign, 2)); ?></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>


</body>
</html>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/pdf/payment_statement.blade.php ENDPATH**/ ?>