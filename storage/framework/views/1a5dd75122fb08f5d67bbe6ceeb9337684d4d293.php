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
    $courseName   = $get('course_name', '-');
    $intake       = $get('intake', '-');
    $installment  = $get('installment_number');
    $dueDate      = $get('due_date');

    // Amount
    $amountLkr    = $get('lkr_amount');                // for franchise w/ FX
    $amount       = (float) ($amountLkr ?? $get('amount', 0));

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
        if (strcasecmp($courseName, $k) === 0 || stripos($courseName, $k) !== false) { $derivedCode = $code; break; }
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
    @page { size: A4; margin: 5mm; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
        color: #000;
    }

    /* Container splits page into 4 rows (¼ A4 each) */
    .page {
        display: flex;
        flex-direction: column;
        height: calc(297mm - 10mm); /* A4 height minus margins */
    }
    .receipt-strip {
        flex: 1; /* divide evenly into 4 */
        min-height: 74.25mm; /* 297/4 */
        border: 0.3mm solid #000;
        box-sizing: border-box;
        padding: 6mm;
        margin-bottom: 2mm;
        display: flex;
        flex-direction: column;
        gap: 4mm;
    }

    /* Header */
    .header {
        text-align: center;
        border-bottom: 0.25mm solid #000;
        padding-bottom: 2mm;
    }
    .logo { height: 14mm; object-fit: contain; }
    .title { font-size: 12pt; font-weight: bold; margin: 2mm 0 1mm; }
    .sub   { font-size: 9pt; margin: 0; }
    .meta  { font-size: 8.5pt; margin-top: 1.5mm; }

    /* Sections */
    .section-title {
        font-weight: bold;
        font-size: 10pt;
        margin: 0 0 2mm;
        border-bottom: 0.25mm solid #000;
        padding-bottom: 1mm;
    }
    .row { margin: 0.75mm 0; line-height: 1.2; }
    .label { font-weight: bold; display: inline-block; min-width: 34mm; }
    .mono  { font-family: monospace; }

    table { width: 100%; border-collapse: collapse; margin-top: 1.5mm; }
    th, td { border: 0.25mm solid #000; padding: 2.5mm 2mm; font-size: 9pt; vertical-align: top; }
    th { text-align: left; font-weight: bold; }
    .right { text-align: right; }
    .total { font-weight: bold; }

    .details-table td { border-bottom: 0.25mm solid #000; }
    .details-table .label { font-weight: bold; width: 38mm; }

    .ref-box {
        margin-top: 2mm;
        text-align: center;
        font-weight: bold;
        border: 0.25mm solid #000;
        padding: 2mm;
        font-size: 9pt;
    }
</style>
</head>
<body>

<div class="page">
  <?php for($i=0; $i<1; $i++): ?> 
  <div class="receipt-strip">

    <div class="header">
      <div class="title">SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY</div>
      <div class="sub">Teleshop Payment Slip</div>
      <div class="meta">
        Receipt No: <span class="mono"><?php echo e($receiptNo); ?></span> |
        Generated: <span class="mono"><?php echo e($generatedAt); ?></span>
      </div>
    </div>
    
    <div class="section">
      <div class="section-title">Teleshop Payment</div>
      <div class="row"><span class="label">Payment Type:</span> <?php echo e($paymentType); ?></div>
      <div class="row"><span class="label">Cost Centre:</span> <?php echo e($costCentre); ?></div>
      <div class="row"><span class="label">Account Code:</span> <?php echo e($accountCode); ?></div>
      <div class="row"><span class="label">Payment Code:</span> <?php echo e($paymentCode); ?></div>
    </div>

    <div class="section">
      <div class="section-title">Customer Details</div>
      <table class="details-table">
        <tr><td class="label">Student Number:</td><td><?php echo e($studentId); ?></td></tr>
        <tr><td class="label">Name:</td><td><?php echo e($studentName); ?></td></tr>
        <tr><td class="label">Course Name:</td><td><?php echo e($courseName); ?></td></tr>
        <tr><td class="label">Intake:</td><td><?php echo e($intake); ?></td></tr>
        <tr><td class="label">Installment #:</td><td><?php echo e($installment ?? '-'); ?></td></tr>
        <tr><td class="label">Due Date:</td><td><?php echo e($dateOr($dueDate)); ?></td></tr>
        <!-- <tr><td class="label">Reference:</td><td><?php echo e($reference1); ?></td></tr> -->
      </table>
      <div class="ref-box">
        <?php echo e($intake); ?> / REF-<?php echo e($studentId); ?> / INST-<?php echo e($installment ?? '-'); ?>

      </div>
    </div>

    <div class="section">
      <table>
        <tr><td class="label">Amount (Rs.):</td><td class="right"><?php echo e($fmt($amount)); ?></td></tr>
        <tr><td class="label">Late Payment (Rs.):</td><td class="right"><?php echo e($fmt(0)); ?></td></tr>
        <tr><td class="label total">Total Payment (Rs.):</td><td class="right total"><?php echo e($fmt($amount)); ?></td></tr>
      </table>
    </div>

  </div>
  <?php endfor; ?>
</div>

</body>
</html>
<?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/pdf/payment_slip.blade.php ENDPATH**/ ?>