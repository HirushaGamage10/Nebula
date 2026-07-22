<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Dashboard Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }
        h2 {
            margin: 0 0 6px 0;
            font-size: 18px;
        }
        .meta {
            margin-bottom: 12px;
            color: #4b5563;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        thead th {
            background: #e5e7eb;
            border: 1px solid #9ca3af;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        tbody td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <h2>Payment Dashboard Export</h2>
    <div class="meta">Generated at: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>NIC</th>
                <th>Course</th>
                <th>Intake</th>
                <th>Location</th>
                <th>Payment Type</th>
                <th>Amount (LKR)</th>
                <th>Effective (Paid) Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['student_name'] }}</td>
                    <td>{{ $row['nic'] }}</td>
                    <td>{{ $row['course'] }}</td>
                    <td>{{ $row['intake'] }}</td>
                    <td>{{ $row['location'] }}</td>
                    <td>{{ $row['payment_type'] }}</td>
                    <td class="amount">{{ number_format($row['amount'], 2) }}</td>
                    <td>{{ $row['effective_paid_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No payments found for selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
