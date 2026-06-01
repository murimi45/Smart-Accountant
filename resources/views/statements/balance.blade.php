<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align:center; margin-bottom:20px; }
        .summary-table { width:50%; float:right; border-collapse:collapse; margin-top:20px; }
        .summary-table td { border:1px solid #ddd; padding:8px; }
        .balance { font-weight:bold; }
    </style>
</head>
<body>

<div class="header">
    <h3>Balance Statement</h3>
    <p><strong>School Accounting System</strong></p>
</div>

<p><strong>Student:</strong> {{ $student->name }}<br>
<strong>Class:</strong> {{ $student->class->name ?? 'N/A' }}<br></p>

@php
$total = $invoices->sum(fn($i) => $i->items->sum('amount'));
$paid = $invoices->sum(fn($i) => $i->payments->sum('amount'));
$balance = $total - $paid;
@endphp

<table class="summary-table">
    <tr><td>Total Invoiced</td><td>{{ number_format($total,2) }}</td></tr>
    <tr><td>Total Paid</td><td>{{ number_format($paid,2) }}</td></tr>
    <tr class="balance"><td>Balance</td><td>{{ number_format($balance,2) }}</td></tr>
</table>

</body>
</html>
