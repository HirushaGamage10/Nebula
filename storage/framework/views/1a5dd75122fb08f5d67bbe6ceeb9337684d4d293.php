<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Slip - <?php echo e($slipData['receipt_no']); ?></title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 9px;
            line-height: 1.2;
        }
        
        .slip-container {
            width: 210mm;
            height: 148.5mm; /* Half A4 height */
            position: relative;
            border: 1px solid #ccc;
            box-sizing: border-box;
            padding: 6mm;
            background: #fff;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        
        .form-number {
            font-size: 7px;
            font-weight: bold;
        }
        
        .logo-section {
            text-align: center;
            flex-grow: 1;
        }
        
        .logo {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-name {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .receipt-label {
            font-size: 8px;
            font-weight: bold;
        }
        
        .main-content {
            display: flex;
            height: calc(100% - 50px);
        }
        
        .left-column {
            width: 65%;
            padding-right: 8px;
        }
        
        .right-column {
            width: 35%;
            padding-left: 8px;
        }
        
        .field-row {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            min-height: 12px;
        }
        
        .field-label {
            font-size: 7px;
            font-weight: bold;
            min-width: 80px;
            margin-right: 8px;
        }
        
        .field-value {
            flex-grow: 1;
            border-bottom: 1px solid #000;
            min-height: 10px;
            padding: 1px 2px;
            font-size: 8px;
        }
        
        .payment-acknowledgment {
            margin: 8px 0;
            font-size: 8px;
        }
        
        .acknowledgment-text {
            margin-bottom: 4px;
        }
        
        .amount-line {
            border-bottom: 1px solid #000;
            min-height: 12px;
            margin: 4px 0;
            padding: 1px 2px;
        }
        
        .itemized-list {
            margin: 6px 0;
        }
        
        .list-item {
            display: flex;
            margin-bottom: 2px;
            min-height: 8px;
        }
        
        .item-number {
            width: 12px;
            font-size: 7px;
            margin-right: 4px;
        }
        
        .item-content {
            flex-grow: 1;
            border-bottom: 1px solid #ccc;
            min-height: 8px;
            padding: 1px 2px;
            font-size: 7px;
        }
        
        .total-section {
            margin-top: 8px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }
        
        .total-row {
            display: flex;
            align-items: center;
        }
        
        .total-label {
            font-size: 7px;
            font-weight: bold;
            min-width: 80px;
            margin-right: 8px;
        }
        
        .total-line {
            flex-grow: 1;
            border-bottom: 2px solid #000;
            min-height: 12px;
            font-weight: bold;
            padding: 1px 2px;
            font-size: 8px;
        }
        
        .language-notes {
            margin-top: 6px;
            font-size: 6px;
            color: #666;
        }
        
        .footer {
            position: absolute;
            bottom: 4mm;
            left: 6mm;
            right: 6mm;
            font-size: 6px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 2px;
        }
        
        .stamp-duty {
            font-size: 6px;
            font-style: italic;
            margin-top: 2px;
        }
        
        .validity-note {
            font-size: 6px;
            font-style: italic;
            margin-top: 2px;
        }
        
        .perforations {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2.5mm;
            background: repeating-linear-gradient(
                to bottom,
                transparent,
                transparent 1.5mm,
                #000 1.5mm,
                #000 2mm
            );
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="perforations"></div>
        
        <!-- Header -->
        <div class="header">
            <div class="form-number">160-C02/<?php echo e($slipData['receipt_no']); ?></div>
            <div class="logo-section">
                <div class="logo">S</div>
                <div class="company-name">Sri Lanka Telecom PLC.</div>
                <div class="receipt-label">(1) Receipt</div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Field Row 1 -->
                <div class="field-row">
                    <div class="field-label">(2) Serial No.</div>
                    <div class="field-value"><?php echo e($slipData['receipt_no']); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(3) Name</div>
                    <div class="field-value"><?php echo e($slipData['student_name']); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(4) Customer No.</div>
                    <div class="field-value"><?php echo e($slipData['student_id']); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(5) Place</div>
                    <div class="field-value"><?php echo e($slipData['location'] ?? 'NEBULA Institute'); ?></div>
                </div>
                
                <!-- Payment Acknowledgment -->
                <div class="payment-acknowledgment">
                    <div class="acknowledgment-text">Received with thanks a sum of Rs.</div>
                    <div class="amount-line">LKR <?php echo e(number_format($slipData['amount'], 2)); ?></div>
                    <div class="acknowledgment-text">being settlement of the following.</div>
                </div>
                
                <!-- Account/Invoice Details -->
                <div class="field-row">
                    <div class="field-label">(7) Account No. / Invoice No.</div>
                    <div class="field-value"><?php echo e($slipData['student_id']); ?>/<?php echo e($slipData['receipt_no']); ?></div>
                </div>
                
                <!-- Itemized List -->
                <div class="itemized-list">
                    <div class="list-item">
                        <div class="item-number">1.</div>
                        <div class="item-content"><?php echo e($slipData['payment_type_display']); ?> - <?php echo e($slipData['course_name']); ?></div>
                    </div>
                    <div class="list-item">
                        <div class="item-number">2.</div>
                        <div class="item-content">Installment <?php echo e($slipData['installment_number'] ?? '1'); ?></div>
                    </div>
                    <div class="list-item">
                        <div class="item-number">3.</div>
                        <div class="item-content">Due Date: <?php echo e($slipData['due_date'] ? date('d/m/Y', strtotime($slipData['due_date'])) : 'N/A'); ?></div>
                    </div>
                    <div class="list-item">
                        <div class="item-number">4.</div>
                        <div class="item-content">Course: <?php echo e($slipData['course_name']); ?></div>
                    </div>
                    <div class="list-item">
                        <div class="item-number">5.</div>
                        <div class="item-content">Intake: <?php echo e($slipData['intake']); ?></div>
                    </div>
                </div>
                
                <!-- Payment Method Details -->
                <div class="field-row">
                    <div class="field-label">(11) Payment Mode</div>
                    <div class="field-value"><?php echo e($slipData['payment_method'] ?? 'Cash'); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(12) Bank</div>
                    <div class="field-value"><?php echo e($slipData['bank_name'] ?? 'N/A'); ?></div>
                </div>
                
                <!-- Language Notes -->
                <div class="language-notes">
                    සිංහල පරිවර්ථනය පසුපිටෙහි ඇත.<br>
                    தமிழ் மொழிபெயர்ப்பை மறுபக்கம் பார்க்கவும்.
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="right-column">
                <div class="field-row">
                    <div class="field-label">(6) Date</div>
                    <div class="field-value"><?php echo e(date('d/m/Y', strtotime($slipData['payment_date']))); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(8) Description</div>
                    <div class="field-value"><?php echo e($slipData['payment_type_display']); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(9) Amount. (Rs.Cts)</div>
                    <div class="field-value"><?php echo e(number_format($slipData['amount'], 2)); ?></div>
                </div>
                
                <!-- Total Section -->
                <div class="total-section">
                    <div class="total-row">
                        <div class="total-label">(10) Total</div>
                        <div class="total-line">LKR <?php echo e(number_format($slipData['amount'], 2)); ?></div>
                    </div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(13) No</div>
                    <div class="field-value"><?php echo e($slipData['receipt_no']); ?></div>
                </div>
                
                <div class="field-row">
                    <div class="field-label">(14) Branch</div>
                    <div class="field-value"><?php echo e($slipData['location'] ?? 'NEBULA Institute'); ?></div>
                </div>
                
                <div class="stamp-duty">(15) (Stamp Duty Paid)</div>
                
                <div class="validity-note">(16) (This receipt is valid only after the realization of the cheque)</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Printed By: Narah Computer Forms Tel: 2245700, 2230060-2 Fax: 2245900
        </div>
    </div>
</body>
</html> <?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/pdf/payment_slip.blade.php ENDPATH**/ ?>