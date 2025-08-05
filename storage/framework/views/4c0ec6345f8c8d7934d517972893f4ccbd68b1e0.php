<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Slip - <?php echo e($slipData['receipt_no']); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .slip-container {
                page-break-inside: avoid;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 9px;
            line-height: 1.3;
        }
        
        .slip-container {
            width: 210mm;
            height: 148.5mm;
            position: relative;
            border: 2px solid #000;
            box-sizing: border-box;
            padding: 6mm;
            background: #fff;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            position: relative;
        }
        
        .form-number {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .logo {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
            border: 3px solid #000;
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f8f8f8;
        }
        
        .company-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
        }
        
        .receipt-label {
            font-size: 10px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 2px 8px;
            border-radius: 3px;
        }
        
        .main-content {
            height: calc(100% - 80px);
            position: relative;
        }
        
        .numbered-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            height: 100%;
        }
        
        .left-section {
            display: flex;
            flex-direction: column;
            padding-right: 10px;
        }
        
        .right-section {
            display: flex;
            flex-direction: column;
            border-left: 2px solid #000;
            padding-left: 15px;
        }
        
        .field-group {
            margin-bottom: 8px;
        }
        
        .field-label {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #333;
        }
        
        .field-value {
            border-bottom: 1px solid #000;
            min-height: 14px;
            padding: 2px 4px;
            font-size: 9px;
            background-color: #fafafa;
        }
        
        .payment-section {
            margin: 12px 0;
            flex-grow: 1;
            background-color: #f9f9f9;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .payment-text {
            font-size: 9px;
            margin-bottom: 4px;
            font-weight: 500;
        }
        
        .amount-box {
            border: 2px solid #000;
            min-height: 18px;
            padding: 4px;
            font-size: 11px;
            font-weight: bold;
            margin: 6px 0;
            text-align: center;
            background-color: #fff;
        }
        
        .settlement-text {
            font-size: 9px;
            margin: 4px 0;
            font-style: italic;
        }
        
        .itemized-list {
            margin: 10px 0;
            background-color: #fff;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .list-item {
            display: flex;
            margin-bottom: 4px;
            font-size: 8px;
            align-items: center;
        }
        
        .item-number {
            width: 15px;
            margin-right: 5px;
            font-weight: bold;
            color: #666;
        }
        
        .item-line {
            flex-grow: 1;
            border-bottom: 1px dotted #999;
            min-height: 12px;
            padding: 2px;
            font-size: 8px;
        }
        
        .right-amount-section {
            margin: 18px 0;
            text-align: center;
        }
        
        .total-box {
            border: 3px solid #000;
            min-height: 25px;
            padding: 6px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        
        .bottom-fields {
            margin-top: auto;
            padding-top: 10px;
        }
        
        .stamp-duty {
            margin: 10px 0;
            font-size: 8px;
            font-style: italic;
            color: #666;
            text-align: center;
            background-color: #f0f0f0;
            padding: 3px;
            border-radius: 3px;
        }
        
        .validity-note {
            margin: 6px 0;
            font-size: 7px;
            font-style: italic;
            color: #888;
            text-align: center;
            line-height: 1.2;
        }
        
        .language-note {
            position: absolute;
            bottom: 25px;
            left: 6mm;
            font-size: 7px;
            color: #666;
            line-height: 1.2;
            background-color: #f9f9f9;
            padding: 3px;
            border-radius: 2px;
        }
        
        .footer {
            position: absolute;
            bottom: 8px;
            left: 6mm;
            right: 6mm;
            font-size: 7px;
            color: #888;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 3px;
            background-color: #fafafa;
        }
        
        /* Print optimizations */
        @media print {
            .slip-container {
                border: 2px solid #000 !important;
                box-shadow: none;
            }
            
            .field-value {
                background-color: transparent !important;
            }
            
            .payment-section {
                background-color: transparent !important;
                border: 1px solid #000 !important;
            }
            
            .itemized-list {
                background-color: transparent !important;
                border: 1px solid #000 !important;
            }
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
                        
                        <div class="stamp-duty">
                            (15) (Stamp Duty Paid)
                        </div>
                        
                        <div class="validity-note">
                            (16) (This receipt is valid only after the realization of the cheque)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Language Note -->
        <div class="language-note">
            සිංහල පරිවර්ථනය පසුපිටෙහි ඇත.<br>
            தமிழ் மொழிபெயர்ப்பை மறுபக்கम் பார்க்கவும்.
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Printed By: Narah Computer Forms Tel: 2245700, 2230060-2 Fax: 2245900
        </div>
    </div>
</body>
</html><?php /**PATH E:\JOB\Projects\Nebula\resources\views/pdf/payment_slip.blade.php ENDPATH**/ ?>