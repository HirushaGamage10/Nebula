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
            height: 148.5mm;
            position: relative;
            border: 1px solid #000;
            box-sizing: border-box;
            padding: 5mm;
            background: #fff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            position: relative;
        }
        
        .form-number {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 7px;
            font-weight: bold;
        }
        
        .logo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
            border: 2px solid #000;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .company-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .receipt-label {
            font-size: 9px;
            font-weight: bold;
        }
        
        .main-content {
            height: calc(100% - 60px);
            position: relative;
        }
        
        .numbered-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            height: 100%;
        }
        
        .left-section {
            display: flex;
            flex-direction: column;
        }
        
        .right-section {
            display: flex;
            flex-direction: column;
            border-left: 1px solid #000;
            padding-left: 10px;
        }
        
        .field-group {
            margin-bottom: 6px;
        }
        
        .field-label {
            font-size: 7px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .field-value {
            border-bottom: 1px solid #000;
            min-height: 12px;
            padding: 1px 3px;
            font-size: 8px;
        }
        
        .payment-section {
            margin: 10px 0;
            flex-grow: 1;
        }
        
        .payment-text {
            font-size: 8px;
            margin-bottom: 3px;
        }
        
        .amount-box {
            border: 1px solid #000;
            min-height: 15px;
            padding: 3px;
            font-size: 9px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .settlement-text {
            font-size: 8px;
            margin: 3px 0;
        }
        
        .itemized-list {
            margin: 8px 0;
        }
        
        .list-item {
            display: flex;
            margin-bottom: 3px;
            font-size: 7px;
        }
        
        .item-number {
            width: 12px;
            margin-right: 3px;
        }
        
        .item-line {
            flex-grow: 1;
            border-bottom: 1px dotted #666;
            min-height: 10px;
            padding: 1px;
        }
        
        .right-amount-section {
            margin: 15px 0;
        }
        
        .total-box {
            border: 2px solid #000;
            min-height: 20px;
            padding: 4px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin: 8px 0;
        }
        
        .bottom-fields {
            margin-top: auto;
        }
        
        .language-note {
            position: absolute;
            bottom: 20px;
            left: 5mm;
            font-size: 6px;
            color: #666;
            line-height: 1.1;
        }
        
        .footer {
            position: absolute;
            bottom: 5px;
            left: 5mm;
            right: 5mm;
            font-size: 6px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <!-- Header -->
        <div class="header">
            <div class="form-number">160-C02/<?php echo e($slipData['receipt_no']); ?></div>
            <div class="logo">S</div>
            <div class="company-name">Sri Lanka Telecom PLC.</div>
            <div class="receipt-label">(1) Receipt</div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="numbered-fields">
                <!-- Left Section -->
                <div class="left-section">
                    <div class="field-group">
                        <div class="field-label">(2) Serial No.</div>
                        <div class="field-value"><?php echo e($slipData['receipt_no']); ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">(3) Name</div>
                        <div class="field-value"><?php echo e($slipData['student_name']); ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">(4) Customer No.</div>
                        <div class="field-value"><?php echo e($slipData['student_id']); ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">(5) Place</div>
                        <div class="field-value"><?php echo e($slipData['location'] ?? 'NEBULA Institute'); ?></div>
                    </div>
                    
                    <!-- Payment Acknowledgment Section -->
                    <div class="payment-section">
                        <div class="payment-text">Received with thanks a sum of Rs.</div>
                        <div class="amount-box"><?php echo e(number_format($slipData['amount'], 2)); ?></div>
                        <div class="settlement-text">being settlement of the following.</div>
                        
                        <div class="field-group">
                            <div class="field-label">(7) Account No./Invoice No.</div>
                            <div class="field-value"><?php echo e($slipData['student_id']); ?>/<?php echo e($slipData['receipt_no']); ?></div>
                        </div>
                        
                        <!-- Itemized List -->
                        <div class="itemized-list">
                            <div class="list-item">
                                <div class="item-number">1.</div>
                                <div class="item-line"><?php echo e($slipData['payment_type_display']); ?> - <?php echo e($slipData['course_name']); ?></div>
                            </div>
                            <div class="list-item">
                                <div class="item-number">2.</div>
                                <div class="item-line">Installment <?php echo e($slipData['installment_number'] ?? '1'); ?></div>
                            </div>
                            <div class="list-item">
                                <div class="item-number">3.</div>
                                <div class="item-line">Due Date: <?php echo e($slipData['due_date'] ? date('d/m/Y', strtotime($slipData['due_date'])) : 'N/A'); ?></div>
                            </div>
                            <div class="list-item">
                                <div class="item-number">4.</div>
                                <div class="item-line">Course: <?php echo e($slipData['course_name']); ?></div>
                            </div>
                            <div class="list-item">
                                <div class="item-number">5.</div>
                                <div class="item-line">Intake: <?php echo e($slipData['intake']); ?></div>
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <div class="field-label">(11) Payment Mode</div>
                            <div class="field-value"><?php echo e($slipData['payment_method'] ?? 'Cash'); ?></div>
                        </div>
                        
                        <div class="field-group">
                            <div class="field-label">(12) Bank</div>
                            <div class="field-value"><?php echo e($slipData['bank_name'] ?? 'N/A'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Section -->
                <div class="right-section">
                    <div class="field-group">
                        <div class="field-label">(6) Date</div>
                        <div class="field-value"><?php echo e(date('d/m/Y', strtotime($slipData['payment_date']))); ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">(8) Description</div>
                        <div class="field-value"><?php echo e($slipData['payment_type_display']); ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">(9) Amount (Rs.Cts)</div>
                        <div class="field-value"><?php echo e(number_format($slipData['amount'], 2)); ?></div>
                    </div>
                    
                    <!-- Total Section -->
                    <div class="right-amount-section">
                        <div class="field-label">(10) Total</div>
                        <div class="total-box"><?php echo e(number_format($slipData['amount'], 2)); ?></div>
                    </div>
                    
                    <div class="bottom-fields">
                        <div class="field-group">
                            <div class="field-label">(13) No</div>
                            <div class="field-value"><?php echo e($slipData['receipt_no']); ?></div>
                        </div>
                        
                        <div class="field-group">
                            <div class="field-label">(14) Branch</div>
                            <div class="field-value"><?php echo e($slipData['location'] ?? 'NEBULA Institute'); ?></div>
                        </div>
                        
                        <div style="margin: 8px 0; font-size: 7px; font-style: italic;">
                            (15) (Stamp Duty Paid)
                        </div>
                        
                        <div style="margin: 5px 0; font-size: 7px; font-style: italic; color: #666;">
                            (16) (This receipt is valid only after the realization of the cheque)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Language Note -->
        <div class="language-note">
            සිංහල පරිවර්ථනය පසුපිටෙහි ඇත.<br>
            தமிழ் மொழிபெயர்ப்பை மறுபக்கம் பார்க்கவும்.
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Printed By: Narah Computer Forms Tel: 2245700, 2230060-2 Fax: 2245900
        </div>
    </div>
</body>
</html><?php /**PATH E:\JOB\Projects\Nebula\resources\views/pdf/payment_slip.blade.php ENDPATH**/ ?>