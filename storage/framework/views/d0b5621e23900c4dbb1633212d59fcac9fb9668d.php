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

    // Amount: prefer computed LKR for franchise; otherwise use amount
    $amountLkr    = $get('lkr_amount');                // only set for franchise fee with FX
    $amount       = (float) ($amountLkr ?? $get('amount', 0));

    // Teleshop overlay (optional)
    $ts           = $get('teleshop', []);
    $paymentType  = data_get($ts, 'payment_type', 'Miscellaneous');
    $costCentre   = data_get($ts, 'cost_centre', '5212');
    $accountCode  = data_get($ts, 'account_code', '481.910');

    // Determine payment code from course (fallback to 1080)
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

    // Reference 1 (nice human label)
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
<title>Teleshop Payment Slip - <?php echo e($receiptNo); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    @page { margin: 20px; }
    body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 12px; }
    .header { text-align: center; margin-bottom: 16px; border-bottom: 1px solid #000; padding-bottom: 10px; }
    .logo { height: 50px; margin-bottom: 6px; }
    .title { font-size: 16px; font-weight: bold; margin: 4px 0; }
    .sub   { font-size: 12px; margin: 2px 0; }
    .meta  { font-size: 11px; color: #333; margin-top: 4px; }
    .section { margin-bottom: 14px; }
    .section-title { font-weight: bold; font-size: 14px; margin-bottom: 6px; }
    .row { margin: 3px 0; }
    .label { font-weight: bold; display: inline-block; min-width: 160px; }
    .mono { font-family: monospace; }
    .divider { border-top: 1px solid #000; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #000; padding: 6px; }
    th { background: #f5f5f5; text-align: left; }
    .right { text-align: right; }
    .total { font-weight: bold; }
</style>
</head>
<body>

    
    <div class="header">

<?php
    $rel   = 'images/logos/nebula.png';
    $path  = public_path($rel);
    $mime  = 'image/png';
    $src   = null;

    if (is_file($path)) {
        // Best option: inline as base64 so DomPDF doesn't need filesystem or network
        try {
            $src = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
        } catch (\Throwable $e) {
            $src = null;
        }
    }

    // Fallback: absolute URL (requires isRemoteEnabled=true and correct APP_URL)
    if (!$src) {
        $src = asset($rel);
    }
?>

<?php if($src): ?>
  <img src="<?php echo e($src); ?>" alt="Nebula" width="180" class="logo">
<?php else: ?>
  <div style="width:180px;height:50px;border:1px solid #ccc;display:inline-block;"></div>
<?php endif; ?>

        <div class="title">SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY</div>
        <div class="sub">Teleshop Payment Slip</div>
        <div class="meta">
            <span class="label">Teleshop Receipt No:</span>
            <span class="mono"><?php echo e($receiptNo); ?></span> &nbsp; | &nbsp;
            <span class="label" style="min-width: auto;">Generated:</span>
            <span class="mono"><?php echo e($generatedAt); ?></span>
        </div>
    </div>

    
    <div class="section">
        <div class="section-title">Teleshop Payment</div>
        <div class="row"><span class="label">Payment Type:</span> <?php echo e($paymentType); ?></div>
        <div class="row"><span class="label">Cost Centre:</span> <?php echo e($costCentre); ?></div>
        <div class="row"><span class="label">Account Code:</span> <?php echo e($accountCode); ?></div>
        <div class="row"><span class="label">Payment Code:</span> <?php echo e($paymentCode); ?></div>
    </div>

    <div class="divider"></div>

    
    <div class="section">
        <div class="section-title">Customer Details</div>
        <div class="row"><span class="label">Name:</span> <?php echo e($studentName); ?></div>
        <div class="row"><span class="label">Reference Number:</span> <?php echo e($studentId); ?></div>
        <div class="row"><span class="label">Course:</span> <?php echo e($courseName); ?></div>
        <div class="row"><span class="label">Intake:</span> <?php echo e($intake); ?></div>
        <div class="row"><span class="label">Installment #:</span> <?php echo e($installment ?? '-'); ?></div>
        <div class="row"><span class="label">Due Date:</span> <?php echo e($dateOr($dueDate)); ?></div>
        <div class="row"><span class="label">Reference:</span> <?php echo e($reference1); ?></div>
    </div>

    
    <table>
        <thead>
            <tr>
                <th style="width:70%;">Course</th>
                <th style="width:30%;">Code</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>CAIT</td><td>1010</td></tr>
            <tr><td>Foundation</td><td>1020</td></tr>
            <tr><td>BTEC DT</td><td>1030</td></tr>
            <tr><td>BTEC EE</td><td>1040</td></tr>
            <tr><td>UH</td><td>1050</td></tr>
            <tr><td>English</td><td>1060</td></tr>
            <tr><td>BTEC Computing</td><td>1070</td></tr>
            <tr><td>Other Courses</td><td>1080</td></tr>
            <tr><td>Hostel</td><td>1090</td></tr>
        </tbody>
    </table>

    
    <div class="section" style="margin-top: 12px;">
        <div class="row"><span class="label">Amount (Rs.):</span> <?php echo e($fmt($amount)); ?></div>
        <div class="row"><span class="label">Late Payment Amount (Rs.):</span> <?php echo e($fmt(0)); ?></div>
        <div class="row total"><span class="label">Total Payment to Pay (Rs.):</span> <?php echo e($fmt($amount)); ?></div>
    </div>

</body>
</html>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/pdf/payment_slip.blade.php ENDPATH**/ ?>