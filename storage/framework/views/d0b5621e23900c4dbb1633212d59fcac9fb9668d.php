<?php
    // Helpers
    $get = fn($key, $default = null) => data_get($slipData ?? [], $key, $default);
    $fmt = function ($n, $dec = 2) {
        if ($n === null || $n === '') return number_format(0, $dec, '.', ',');
        return number_format((float)$n, $dec, '.', ',');
    };
    $dateOr = function ($v, $fallback = '-') {
        try { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : $fallback; }
        catch (\Throwable $e) { return $fallback; }
    };

    // Core slip fields
    $receiptNo    = $get('receipt_no', 'N/A');
    $generatedAt  = $get('generated_at', now()->format('Y-m-d H:i:s'));
    $studentId    = $get('student_id', '-');
    $studentName  = $get('student_name', '-');
    $mobilePhone  = $get('mobile_phone', '-');
    $courseName   = $get('course_name', '-');
    $intake       = $get('intake', '-');
    $installment  = $get('installment_number');
    $dueDate      = $get('due_date');
    $remarks     = $get('remarks', '');
    $ssclTaxAmount = (float) $get('sscl_tax_amount', 0); // NEW: SSCL Tax Amount
    $bankCharges   = (float) $get('bank_charges', 0);

    // Amount
    $amountLkr    = $get('lkr_amount');                // for franchise w/ FX
    $amount       = (float) ($amountLkr ?? $get('amount', 0));
    
    // Base amount (without SSCL tax and bank charges) for display
    $baseAmount   = (float) $get('base_amount', $amount);

    // NEW: Late fee fields
    $lateFee        = (float) $get('late_fee', 0);
    $approvedLate   = (float) $get('approved_late_fee', 0);
    
    // Use the total_fee from slip data if available, otherwise calculate it
    $totalFee = (float) $get('total_fee');
    if ($totalFee <= 0) {
        // Fallback calculation: amount + late fee (approved late fee is already deducted in amount)
        $totalFee = $amount + $lateFee + $ssclTaxAmount + $bankCharges;
    }

    // Teleshop overlay
    $ts           = $get('teleshop', []);
    $paymentType  = data_get($ts, 'payment_type', 'Miscellaneous');
    $costCentre   = data_get($ts, 'cost_centre', '5212');
    $accountCode  = data_get($ts, 'account_code', '481.910');

    // Payment code derivation
    $codeMap = [
        'CAIT'            => '1010',
        'Foundation'      => '1020',
        'BTEC DT'         => '1030',
        'BTEC EE'         => '1040',
        'UH'              => '1050',
        'English'         => '1060',
        'BTEC Computing'  => '1070',
        'Other Courses'   => '1080',
        'Hostel'          => '1090',
    ];
    $derivedCode = '1080';
    foreach ($codeMap as $k => $code) {
        if (strcasecmp($courseName, $k) === 0 || stripos($courseName, $k) !== false) { 
            $derivedCode = $code; break; 
        }
    }
    $paymentCode = data_get($ts, 'reference_2', $derivedCode);

    // Reference 1
    $reference1  = data_get(
        $ts,
        'reference_1',
        trim($courseName) . ' / ' . ($installment ? ($installment . ' Installment') : 'Payment')
    );
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Teleshop Payment Slip</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    @page { 
        size: A4; 
        margin: 8mm 6mm; 
    }
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        font-family: 'Arial', 'Helvetica', sans-serif;
        font-size: 9px;
        line-height: 1.2;
        color: #000;
        background: #fff;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .page {
        width: 100%;
        max-width: 210mm;
        margin: 0 auto;
        background: #fff;
    }

    .receipt-strip {
        width: 100%;
        height: calc(297mm - 16mm);
        border: 1px solid #000;
        background: #fff;
        page-break-inside: avoid;
        display: flex;
        flex-direction: column;
    }

    /* Header Section */
    .header {
        background: #000;
        color: #fff;
        padding: 4mm 3mm;
        text-align: center;
        border-bottom: 1px solid #000;
        flex-shrink: 0;
    }
    
    .header .company-name {
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 0.3px;
        margin-bottom: 1mm;
        text-transform: uppercase;
    }
    
    .header .slip-title {
        font-size: 11px;
        font-weight: bold;
        margin-bottom: 2mm;
    }
    
    .header .receipt-info {
        font-size: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1mm;
    }
    
    .receipt-number {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 11px;
    }

    /* Content Area */
    .content {
        padding: 3mm;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2mm;
    }

    /* Section Styling */
    .section {
        border: 1px solid #ccc;
        background: #fff;
        flex-shrink: 0;
    }
    
    .section-header {
        background: #f0f0f0;
        border-bottom: 1px solid #000;
        padding: 1.5mm 2mm;
        font-weight: bold;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }
    
    .section-content {
        padding: 2mm;
    }

    /* Teleshop Info Grid */
    .teleshop-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1mm;
    }
    
    .teleshop-item {
        display: flex;
        align-items: center;
        padding: 1mm 0;
        border-bottom: 1px dotted #ddd;
        font-size: 8px;
    }
    
    .teleshop-item:last-child {
        border-bottom: none;
    }
    
    .teleshop-label {
        font-weight: bold;
        min-width: 25mm;
        color: #333;
    }
    
    .teleshop-value {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }

    /* Customer Details Table */
    .details-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .details-table td {
        padding: 1.5mm 1mm;
        border-bottom: 1px solid #e0e0e0;
        vertical-align: top;
        font-size: 8px;
    }
    
    .details-table .label-cell {
        width: 25%;
        font-weight: bold;
        color: #333;
        background: #f9f9f9;
    }
    
    .details-table .value-cell {
        width: 75%;
        font-weight: 500;
    }

    /* Reference Box */
    .reference-box {
        margin-top: 2mm;
        border: 1px solid #000;
        background: #f0f0f0;
        padding: 1.5mm;
        text-align: center;
        font-family: 'Courier New', monospace;
        font-weight: bold;
        font-size: 8px;
    }

    /* Payment Summary Table */
    .payment-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1mm;
    }
    
    .payment-table th,
    .payment-table td {
        padding: 2mm 1.5mm;
        border: 1px solid #000;
        text-align: left;
        font-size: 8px;
    }
    
    .payment-table th {
        background: #f0f0f0;
        font-weight: bold;
    }
    
    .payment-table .amount {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    
    .payment-table .total-row {
        background: #000;
        color: #fff;
        font-weight: bold;
    }

    /* Payment History */
    .payment-history {
        margin-top: 1mm;
    }
    
    .payment-history h5 {
        font-size: 9px;
        font-weight: bold;
        margin-bottom: 1mm;
        padding-bottom: 0.5mm;
        border-bottom: 1px solid #000;
    }
    
    .payment-list {
        list-style: none;
    }
    
    .payment-item {
        padding: 1mm 0;
        border-bottom: 1px dotted #ccc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 7px;
    }
    
    .payment-item:last-child {
        border-bottom: none;
    }
    
    .payment-details {
        flex: 1;
    }
    
    .payment-amount {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        min-width: 20mm;
        text-align: right;
    }
    
    .payment-method {
        font-size: 7px;
        color: #666;
    }
    
    .payment-remarks {
        font-size: 7px;
        color: #666;
        margin-top: 0.5mm;
        font-style: italic;
    }

    /* Status and Balance */
    .summary-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2mm;
        margin-top: 2mm;
        padding: 2mm;
        background: #f8f8f8;
        border: 1px solid #ddd;
        flex-shrink: 0;
    }
    
    .summary-item {
        text-align: center;
    }
    
    .summary-label {
        font-size: 7px;
        color: #666;
        margin-bottom: 0.5mm;
    }
    
    .summary-value {
        font-weight: bold;
        font-size: 9px;
        font-family: 'Courier New', monospace;
    }
    
    .status-pending {
        color: #e67e22;
    }
    
    .status-paid {
        color: #27ae60;
    }
    
    .status-partial {
        color: #3498db;
    }

    /* Print Optimizations */
    @media print {
        body {
            font-size: 8px;
        }
        
        .page {
            box-shadow: none;
        }
        
        .receipt-strip {
            page-break-inside: avoid;
            height: auto;
            min-height: calc(297mm - 16mm);
        }
    }

    /* Utility Classes */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-muted { color: #666; }
    .font-mono { font-family: 'Courier New', monospace; }
    .font-bold { font-weight: bold; }
</style>
</head>
<body>

<div class="page">
  <div class="receipt-strip">

    <div class="header">
      <div class="company-name">SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY</div>
      <div class="slip-title">TELESHOP PAYMENT SLIP</div>
      <div class="receipt-info">
        <span>Receipt No: <span class="receipt-number"><?php echo e($receiptNo); ?></span></span>
        <span>Generated: <span class="font-mono"><?php echo e($generatedAt); ?></span></span>
      </div>
    </div>

    <div class="content">
      
      <!-- Teleshop Payment Information -->
      <div class="section">
        <div class="section-header">Teleshop Payment Information</div>
        <div class="section-content">
          <div class="teleshop-grid">
            <div class="teleshop-item">
              <span class="teleshop-label">Payment Type:</span>
              <span class="teleshop-value"><?php echo e($paymentType); ?></span>
            </div>
            <div class="teleshop-item">
              <span class="teleshop-label">Cost Centre:</span>
              <span class="teleshop-value"><?php echo e($costCentre); ?></span>
            </div>
            <div class="teleshop-item">
              <span class="teleshop-label">Account Code:</span>
              <span class="teleshop-value"><?php echo e($accountCode); ?></span>
            </div>
            <!-- <div class="teleshop-item">
              <span class="teleshop-label">Payment Code:</span>
              <span class="teleshop-value"><?php echo e($paymentCode); ?></span>
            </div> -->
          </div>
        </div>
      </div>

      <!-- Customer Details -->
      <div class="section">
        <div class="section-header">Customer Details</div>
        <div class="section-content">
          <table class="details-table">
            <!-- <tr>
              <td class="label-cell">Student Number:</td>
              <td class="value-cell font-mono"><?php echo e($studentId); ?></td>
            </tr> -->
            <tr>
              <td class="label-cell">Student Name:</td>
              <td class="value-cell"><?php echo e($studentName); ?></td>
            </tr>
            <tr>
              <td class="label-cell">Reference Number:</td>
              <td class="value-cell"><?php echo e($intake); ?> / REF-<?php echo e($studentId); ?> / INST-<?php echo e($installment !== null ? $installment : 'REG-FEE'); ?></td>
            </tr>
            <tr>
              <td class="label-cell">Telephone Number:</td>
              <td class="value-cell"><?php echo e($mobilePhone); ?></td>
            </tr>
            <tr>
              <td class="label-cell">Reference 1:</td>
              <td class="value-cell"><?php echo e($courseName); ?></td>
            </tr>
            <tr>
              <td class="label-cell">Reference 2:</td>
              <td class="value-cell"><?php echo e($paymentCode); ?></td>
            </tr>
            <!-- <tr>
              <td class="label-cell">Intake:</td>
              <td class="value-cell font-mono"><?php echo e($intake); ?></td>
            </tr>
            <tr>
              <td class="label-cell">Installment #:</td>
              <td class="value-cell font-mono"><?php echo e($installment ?? '-'); ?></td>
            </tr> -->
            <tr>
              <td class="label-cell">Due Date:</td>
              <td class="value-cell font-mono"><?php echo e($dateOr($dueDate)); ?></td>
            </tr>
          </table>
          
          <div class="reference-box">
            <?php echo e($intake); ?> / REF-<?php echo e($studentId); ?> / INST-<?php echo e($installment !== null ? $installment : 'REG-FEE'); ?>

          </div>
        </div>
      </div>
<?php if(!empty($remarks) && str_contains(strtolower($paymentType), 'registration')): ?>
<div class="section">
  <div class="section-header">Remarks</div>
  <div class="section-content">
    <p class="text-sm text-muted"><?php echo e($remarks); ?></p>
  </div>
</div>
<?php endif; ?>



      <!-- Payment Summary -->
<div class="section">
  <div class="section-header">Payment Summary</div>
  <div class="section-content">
    <table class="payment-table">
      <tr>
        <td>Course / Installment Fee:</td>
        <td class="amount">LKR <?php echo e($fmt($baseAmount)); ?></td>
      </tr>
      <tr>
        <td>Late Fee:</td>
        <td class="amount">LKR <?php echo e($fmt($lateFee)); ?></td>
      </tr>
      <tr>
        <td>Approved Late Fee Discount:</td>
        <td class="amount">- LKR <?php echo e($fmt($approvedLate)); ?></td>
      </tr>
      
    <?php if(!empty($ssclTaxAmount) && $ssclTaxAmount > 0): ?>
    <tr>
        <td>Tax</td>
        <td class="amount">+ LKR <?php echo e($fmt($ssclTaxAmount)); ?></td>
    </tr>
    <?php endif; ?>

    
    <?php if(!empty($bankCharges) && $bankCharges > 0): ?>
    <tr>
        <td>Bank Charges</td>
        <td class="amount">+ LKR <?php echo e($fmt($bankCharges)); ?></td>
    </tr>
    <?php endif; ?>
      <tr class="total-row">
        <td>TOTAL PAYMENT:</td>
        <td class="amount">LKR <?php echo e($fmt($totalFee)); ?></td>
      </tr>
    </table>

    
<?php if(is_null($slipData['installment_number'] ?? null) && !empty($slipData['remarks'])): ?>
<div class="remarks mt-2">
  <strong>Remarks:</strong>
  <p class="text-sm text-muted"><?php echo e($slipData['remarks']); ?></p>
</div>
<?php endif; ?>


  </div>
</div>


      <?php
          // Decode partial payments from slipData or directly from payment_details
          $partials = [];

          if (!empty($slipData['partial_payments'])) {
              if (is_array($slipData['partial_payments'])) {
                  $partials = $slipData['partial_payments'];
              } else {
                  $partials = json_decode($slipData['partial_payments'], true) ?? [];
              }
          }
      ?>

      <!-- Payment History -->
      <?php if(!empty($partials)): ?>
      <div class="section">
        <div class="section-header">Payment History</div>
        <div class="section-content">
          <ul class="payment-list">
            <?php $__currentLoopData = $partials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="payment-item">
              <div class="payment-details">
                <div class="font-mono"><?php echo e($p['date'] ?? ''); ?></div>
                <div class="payment-method">(<?php echo e($p['method'] ?? 'N/A'); ?>)</div>
                <?php if(!empty($p['remarks'])): ?>
                  <div class="payment-remarks"><?php echo e($p['remarks']); ?></div>
                <?php endif; ?>
              </div>
              <div class="payment-amount">LKR <?php echo e(number_format($p['amount'] ?? 0, 2)); ?></div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      </div>
      <?php else: ?>
      <div class="section">
        <div class="section-header">Payment History</div>
        <div class="section-content">
          <p class="text-muted text-center">No partial payments recorded yet.</p>
        </div>
      </div>
      <?php endif; ?>

      <!-- Summary Information -->
      <div class="summary-info">
        <div class="summary-item">
          <div class="summary-label">REMAINING BALANCE</div>
          <div class="summary-value">LKR <?php echo e(number_format($slipData['remaining_amount'] ?? 0, 2)); ?></div>
        </div>
        <div class="summary-item">
          <div class="summary-label">PAYMENT STATUS</div>
          <div class="summary-value status-<?php echo e(strtolower($slipData['status'] ?? 'pending')); ?>">
            <?php echo e(strtoupper($slipData['status'] ?? 'PENDING')); ?>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

</body>
</html><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/pdf/payment_slip.blade.php ENDPATH**/ ?>