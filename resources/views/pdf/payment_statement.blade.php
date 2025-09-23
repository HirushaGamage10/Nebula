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
    <p><strong>{{ $student['id'] }} - {{ $student['name'] }}</strong></p>

    <p><strong>NIC:</strong> {{ $student['nic'] }}</p>
    <p><strong>Course:</strong> {{ $course['name'] }}</p>
    <p><strong>Intake:</strong> {{ $course['intake'] }}</p>
    <p><strong>Registration Date:</strong> {{ $course['registration_date'] }}</p>
    <p><strong>Date Issued:</strong> {{ $generated_date }}</p>

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
            @forelse($payments as $p)
                <tr>
                    <td>
                        {{ $p['description'] }}
                        @if(($p['amount'] ?? 0) == 0)
                            (Outstanding)
                        @endif
                    </td>
                    <td>{{ $p['method'] ?? '-' }}</td>
                    <td>{{ $p['receipt_no'] ?? '-' }}</td>
                    <td>{{ $p['date'] ?? '-' }}</td>
                    <td style="text-align:right;">{{ number_format($p['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">No payment records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Amount:</strong> Rs. {{ number_format($totals['total_amount'], 2) }}</p>
        <p><strong>Total Paid:</strong> Rs. {{ number_format($totals['total_paid'], 2) }}</p>
        <p><strong>Total Outstanding:</strong> Rs. {{ number_format($totals['total_remaining'], 2) }}</p>
    </div>
    @if($paymentPlan && $paymentPlan->installments->count())
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
            @php
                $sumBase   = 0;
                $sumDisc   = 0;
                $sumLoan   = 0;
                $sumFinal  = 0;
            @endphp
            @foreach($paymentPlan->installments as $inst)
                @php
                    $sumBase  += $inst->base_amount ?? $inst->amount ?? 0;
                    $sumDisc  += $inst->discount_amount ?? 0;
                    $sumLoan  += $inst->slt_loan_amount ?? 0;
                    $sumFinal += $inst->final_amount ?? ($inst->base_amount ?? $inst->amount ?? 0);
                @endphp
                <tr>
                    <td>{{ $inst->installment_number }}</td>
                    <td>{{ $inst->formatted_due_date }}</td>
                    <td>{{ number_format($inst->base_amount ?? $inst->amount ?? 0, 2) }}</td>
                    <td>{{ number_format($inst->discount_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($inst->slt_loan_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($inst->final_amount ?? ($inst->base_amount ?? $inst->amount ?? 0), 2) }}</td>

                </tr>
            @endforeach
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="2" align="right">TOTAL</td>
                <td>{{ number_format($sumBase, 2) }}</td>
                <td>{{ number_format($sumDisc, 2) }}</td>
                <td>{{ number_format($sumLoan, 2) }}</td>
                <td>{{ number_format($sumFinal, 2) }}</td>

            </tr>
        </tbody>
    </table>
@endif

@if(!empty($courseInstallments))
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
            @php
                $sumLocal   = 0;
                $sumForeign = 0;
            @endphp
            @foreach($courseInstallments as $inst)
                @php
                    $sumLocal   += $inst['local_amount'] ?? 0;
                    $sumForeign += $inst['international_amount'] ?? 0;
                @endphp
                <tr>
                    <td>{{ $inst['installment_number'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($inst['due_date'])->format('d/m/Y') }}</td>
                    <td>{{ number_format($inst['local_amount'] ?? 0, 2) }}</td>
                    <td>{{ number_format($inst['international_amount'] ?? 0, 2) }}</td>
                    <td>{{ $coursePlan->international_currency ?? '-' }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background: #f9f9f9;">
                <td colspan="2" align="right">TOTAL</td>
                <td>{{ number_format($sumLocal, 2) }}</td>
                <td>{{ number_format($sumForeign, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
@endif


</body>
</html>
