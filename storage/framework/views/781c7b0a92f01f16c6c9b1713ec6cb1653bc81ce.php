<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Statement</title>
    <style>
        body { 
            font-family: 'Arial', 'Helvetica', sans-serif; 
            font-size: 11px; 
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #000;
            background: #fff;
        }
        
        .institute-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .institute-name {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding: 10px 0 15px 0;
            margin-bottom: 25px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .student-info {
            background: #f8f8f8;
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .student-info .student-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding: 8px 0 5px 0;
            margin: 25px 0 15px 0;
            letter-spacing: 1px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            border: 2px solid #000;
        }
        
        th {
            background: #000;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        tr:hover {
            background: #f0f0f0;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }
        
        .summary {
            border: 2px solid #000;
            background: #f8f8f8;
            padding: 15px;
            margin: 25px 0;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 13px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-amount {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .no-records {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .installment-table {
            font-size: 10px;
        }
        
        .installment-table th {
            font-size: 9px;
            padding: 8px 4px;
        }
        
        .installment-table td {
            padding: 6px 4px;
        }
        
        .total-row {
            background: #e8e8e8 !important;
            font-weight: bold;
            border-top: 2px solid #000 !important;
        }
        
        .total-row td {
            padding: 10px 4px;
            border-top: 2px solid #000;
        }
        
        .outstanding {
            color: #666;
            font-style: italic;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            background: #fff;
        }
        
        .page-number:before {
            content: "Page " counter(page);
        }
        
        @media print {
            body {
                padding: 10px;
                padding-bottom: 60px;
            }
            
            .institute-header, .header {
                page-break-after: avoid;
            }
            
            table {
                page-break-inside: avoid;
            }
            
            .summary {
                page-break-inside: avoid;
            }
            
            .footer {
                position: fixed;
                bottom: 10px;
                left: 0;
                right: 0;
                height: 30px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 10px;
                color: #000;
            }
        }
        
        @page {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="institute-header">
        <h1 class="institute-name">Nebula Institute</h1>
    </div>

    <div class="header">
        <h1>Statement of Account</h1>
    </div>

    <div class="student-info">
        <div class="student-name"><?php echo e($student['id']); ?> - <?php echo e($student['name']); ?></div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">NIC:</span> <?php echo e($student['nic']); ?>

            </div>
            <div class="info-item">
                <span class="info-label">Date Issued:</span> <?php echo e($generated_date); ?>

            </div>
            <div class="info-item">
                <span class="info-label">Course:</span> <?php echo e($course['name']); ?>

            </div>
            <div class="info-item">
                <span class="info-label">Intake:</span> <?php echo e($course['intake']); ?>

            </div>
            <div class="info-item">
                <span class="info-label">Registration:</span> <?php echo e($course['registration_date']); ?>

            </div>
        </div>
    </div>

    <h2 class="section-title">Payment Details</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Item Description</th>
                <th style="width: 15%;">Payment Mode</th>
                <th style="width: 15%;">Receipt No</th>
                <th style="width: 15%;">Date</th>
                <th style="width: 15%;">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <?php echo e($p['description']); ?>

                        <?php if(($p['amount'] ?? 0) == 0): ?>
                            <span class="outstanding">(Outstanding)</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($p['method'] ?? '-'); ?></td>
                    <td><?php echo e($p['receipt_no'] ?? '-'); ?></td>
                    <td><?php echo e($p['date'] ?? '-'); ?></td>
                    <td class="amount-cell"><?php echo e(number_format($p['amount'], 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="no-records">No payment records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Total Amount:</span>
            <span class="summary-amount">Rs. <?php echo e(number_format($totals['total_amount'], 2)); ?></span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Paid:</span>
            <span class="summary-amount">Rs. <?php echo e(number_format($totals['total_paid'], 2)); ?></span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Outstanding:</span>
            <span class="summary-amount">Rs. <?php echo e(number_format($totals['total_remaining'], 2)); ?></span>
        </div>
    </div>

    <?php if($paymentPlan && $paymentPlan->installments->count()): ?>
    <h2 class="section-title">Student Payment Plan (LKR)</h2>
    <table class="installment-table">
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 15%;">Due Date</th>
                <th style="width: 18%;">Base Amount</th>
                <th style="width: 15%;">Discount</th>
                <th style="width: 18%;">SLT Loan</th>
                <th style="width: 18%;">Final Amount</th>
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
                    <td class="amount-cell"><?php echo e(number_format($inst->base_amount ?? $inst->amount ?? 0, 2)); ?></td>
                    <td class="amount-cell"><?php echo e(number_format($inst->discount_amount ?? 0, 2)); ?></td>
                    <td class="amount-cell"><?php echo e(number_format($inst->slt_loan_amount ?? 0, 2)); ?></td>
                    <td class="amount-cell"><?php echo e(number_format($inst->final_amount ?? ($inst->base_amount ?? $inst->amount ?? 0), 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL</td>
                <td class="amount-cell"><?php echo e(number_format($sumBase, 2)); ?></td>
                <td class="amount-cell"><?php echo e(number_format($sumDisc, 2)); ?></td>
                <td class="amount-cell"><?php echo e(number_format($sumLoan, 2)); ?></td>
                <td class="amount-cell"><?php echo e(number_format($sumFinal, 2)); ?></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if(!empty($courseInstallments)): ?>
    <h2 class="section-title">Course Installment Plan (Master)</h2>
    <table class="installment-table">
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 20%;">Due Date</th>
                <th style="width: 25%;">Local Amount (LKR)</th>
                <th style="width: 25%;">Foreign Amount</th>
                <th style="width: 15%;">Currency</th>
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
                    <td class="amount-cell"><?php echo e(number_format($inst['local_amount'] ?? 0, 2)); ?></td>
                    <td class="amount-cell"><?php echo e(number_format($inst['international_amount'] ?? 0, 2)); ?></td>
                    <td><?php echo e($coursePlan->international_currency ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL</td>
                <td class="amount-cell"><?php echo e(number_format($sumLocal, 2)); ?></td>
                <td class="amount-cell"><?php echo e(number_format($sumForeign, 2)); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="footer">
        <div class="page-number"></div>
    </div>

</body>
</html><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/pdf/payment_statement.blade.php ENDPATH**/ ?>