<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h2 { margin: 0 0 5px; font-size: 18px; }
        .meta { margin-bottom: 5px; color: #4b5563; }
        .total { margin: 0 0 14px; font-size: 13px; font-weight: bold; color: #0f766e; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background: #e5e7eb; border: 1px solid #9ca3af; padding: 6px; text-align: left; }
        td { border: 1px solid #d1d5db; padding: 5px; vertical-align: top; overflow-wrap: break-word; }
        .amount { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <div class="meta">Month: {{ $month->format('F Y') }} | Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>
    <div class="total">{{ $amountLabel }} total: LKR {{ number_format($totalAmount, 2) }} ({{ number_format($rows->count()) }} record{{ $rows->count() === 1 ? '' : 's' }})</div>

    <table>
        <thead>
            <tr>
                <th style="width: 17%">Student</th>
                <th style="width: 13%">NIC / Student ID</th>
                <th style="width: 20%">Course</th>
                <th style="width: 11%">Intake</th>
                <th style="width: 15%">Reference</th>
                <th style="width: 10%">Date</th>
                <th style="width: 14%" class="amount">{{ $amountLabel }} (LKR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['student_name'] }}</td>
                    <td>{{ $row['nic'] }}</td>
                    <td>{{ $row['course'] }}</td>
                    <td>{{ $row['intake'] }}</td>
                    <td>{{ $row['reference'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td class="amount">{{ number_format($row['amount'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="center">No matching records found for this KPI.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
