<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Slip - {{ $slipData['receipt_no'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }
        .institute-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 5px 0;
        }
        .subtitle {
            font-size: 14px;
            color: #7f8c8d;
            margin: 5px 0;
        }
        .slip-title {
            font-size: 28px;
            font-weight: bold;
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .receipt-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid #3498db;
        }
        .receipt-number {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .receipt-date {
            color: #7f8c8d;
            font-size: 14px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #34495e;
            font-size: 12px;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 14px;
            color: #2c3e50;
            margin-top: 2px;
        }
        .amount-section {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #27ae60;
        }
        .amount-label {
            font-size: 16px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
        }
        .payment-details {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #ffc107;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }
        .validity {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .signature-box {
            text-align: center;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
        }
        .signature-line {
            border-top: 1px solid #34495e;
            margin-top: 30px;
            width: 200px;
            display: inline-block;
        }
        .signature-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(52, 152, 219, 0.1);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">PAID</div>
    
    <!-- Header -->
    <div class="header">
        <div class="institute-name">SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY</div>
        <div class="subtitle">Payment Slip</div>
        <div class="subtitle">Generated on: {{ date('d/m/Y H:i') }}</div>
    </div>

    <!-- Receipt Information -->
    <div class="receipt-info">
        <div class="receipt-number">Receipt No: {{ $slipData['receipt_no'] }}</div>
        <div class="receipt-date">Date: {{ date('d/m/Y', strtotime($slipData['payment_date'])) }}</div>
    </div>

    <!-- Student Information -->
    <div class="section">
        <div class="section-title">Student Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Student ID</div>
                <div class="info-value">{{ $slipData['student_id'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Student Name</div>
                <div class="info-value">{{ $slipData['student_name'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">NIC Number</div>
                <div class="info-value">{{ $slipData['student_nic'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $slipData['location'] }}</div>
            </div>
        </div>
    </div>

    <!-- Course Information -->
    <div class="section">
        <div class="section-title">Course Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Course Name</div>
                <div class="info-value">{{ $slipData['course_name'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Course Code</div>
                <div class="info-value">{{ $slipData['course_code'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Intake</div>
                <div class="info-value">{{ $slipData['intake'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Registration Date</div>
                <div class="info-value">{{ date('d/m/Y', strtotime($slipData['registration_date'])) }}</div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="section">
        <div class="section-title">Payment Information</div>
        <div class="payment-details">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Payment Type</div>
                    <div class="info-value">{{ $slipData['payment_type_display'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Installment Number</div>
                    <div class="info-value">{{ $slipData['installment_number'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Due Date</div>
                    <div class="info-value">{{ $slipData['due_date'] ? date('d/m/Y', strtotime($slipData['due_date'])) : 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{{ $slipData['payment_method'] ?? 'Cash' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Section -->
    <div class="amount-section">
        <div class="amount-label">Total Amount Due</div>
        <div class="amount-value">LKR {{ number_format($slipData['amount'], 2) }}</div>
    </div>

    <!-- Fee Breakdown -->
    <div class="section">
        <div class="section-title">Fee Breakdown</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Course Fee</div>
                <div class="info-value">LKR {{ number_format($slipData['course_fee'] ?? 0, 2) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Franchise Fee</div>
                <div class="info-value">{{ $slipData['franchise_fee_currency'] ?? 'LKR' }} {{ number_format($slipData['franchise_fee'] ?? 0, 2) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Registration Fee</div>
                <div class="info-value">LKR {{ number_format($slipData['registration_fee'] ?? 0, 2) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Current Payment</div>
                <div class="info-value">LKR {{ number_format($slipData['amount'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Remarks -->
    @if(!empty($slipData['remarks']))
    <div class="section">
        <div class="section-title">Remarks</div>
        <div class="info-value">{{ $slipData['remarks'] }}</div>
    </div>
    @endif

    <!-- Validity -->
    <div class="validity">
        <strong>Valid Until:</strong> {{ date('d/m/Y', strtotime($slipData['valid_until'])) }}
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Student Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signature</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated document. No signature is required for electronic submissions.</p>
        <p>For any queries, please contact the administration office.</p>
        <p>© {{ date('Y') }} SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY. All rights reserved.</p>
    </div>
</body>
</html> 