<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Statement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .student-info {
            margin-bottom: 20px;
        }
        h3 {
            margin-top: 25px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            text-align: left;
        }
        th, td {
            padding: 8px;
        }
        .summary {
            margin-top: 30px;
            width: 50%;
            float: right;
        }
        .summary td {
            padding: 6px 10px;
        }
        .balance {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student Invoice Statement</h2>
        <p><strong>School Accounting System</strong></p>
    </div>

    @php
        $statementStudentName = $student?->full_name ?? 'Deleted student';
        $statementClassName = $invoices->first()?->enrollment?->schoolClass?->name ?? 'N/A';
    @endphp
    <div class="student-info">
        <strong>Student:</strong> {{ $statementStudentName }}<br>
        <strong>Class:</strong> {{ $statementClassName }}<br>
        <strong>Term:</strong> {{ $invoices->first()?->term?->name ?? 'N/A' }}
    </div>

    @php
        $grandTotal = 0;
        $grandPaid = 0;
    @endphp

    @foreach($invoices as $invoice)
        <div class="invoice-info" style="margin-bottom: 10px;">
            <strong>Invoice #:</strong> {{ $invoice->id }}<br>
            <strong>Date Issued:</strong> {{ $invoice->created_at->format('d M Y') }}
        </div>

        <h3>Invoice Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="width: 100px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td style="text-align:right;">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Payments Made</h3>
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Date</th>
                    <th style="width: 100px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->payments as $payment)
                    <tr>
                        <td>{{ ucfirst($payment->method) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td style="text-align:right;">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center;">No payments recorded</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @php
            $grandTotal += $invoice->items->sum('amount');
            $grandPaid += $invoice->payments->sum('amount');
        @endphp
    @endforeach

    {{-- Final Summary --}}
    <div class="summary">
        <table>
            <tr>
                <td><strong>Total Invoiced:</strong></td>
                <td style="text-align:right;">{{ number_format($grandTotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Paid:</strong></td>
                <td style="text-align:right;">{{ number_format($grandPaid, 2) }}</td>
            </tr>
            <tr class="balance">
                <td><strong>Balance:</strong></td>
                <td style="text-align:right;">{{ number_format($grandTotal - $grandPaid, 2) }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
