<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucwords(str_replace('-', ' ', $reportType)) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h2 { margin: 0 0 4px; font-size: 16px; }
        .meta { margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; }
        th { background: #f0f0f0; text-align: left; }
        .text-end { text-align: right; }
        tfoot td { font-weight: bold; background: #f9f9f9; }
        h3 { font-size: 12px; margin: 12px 0 6px; }
    </style>
</head>
<body>
    @php
        $titles = [
            'trial-balance' => 'Trial Balance',
            'profit-loss' => 'Profit & Loss',
            'balance-sheet' => 'Balance Sheet',
        ];
    @endphp
    <div class="header">
        <h2>{{ $titles[$reportType] ?? 'Financial Report' }}</h2>
        <p>{{ auth()->user()->school->school_name ?? 'School' }}</p>
    </div>
    <div class="meta">
        <strong>Period:</strong> {{ $period['label'] }}<br>
        <strong>Dates:</strong> {{ $period['start']->format('d M Y') }} – {{ $period['end']->format('d M Y') }}
    </div>

    @if($reportType === 'trial-balance')
        <table>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Category</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['rows'] as $row)
                    <tr>
                        <td>{{ $row['account']->name }}</td>
                        <td>{{ ucfirst($row['category']) }}</td>
                        <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                        <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Totals</td>
                    <td class="text-end">{{ number_format($report['totalDebits'], 2) }}</td>
                    <td class="text-end">{{ number_format($report['totalCredits'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @elseif($reportType === 'profit-loss')
        <h3>Income</h3>
        <table>
            @foreach($report['incomeRows'] as $row)
                <tr><td>{{ $row['account']->name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
            @endforeach
            <tfoot><tr><td>Total Income</td><td class="text-end">{{ number_format($report['totalIncome'], 2) }}</td></tr></tfoot>
        </table>
        <h3>Expenses</h3>
        <table>
            @foreach($report['expenseRows'] as $row)
                <tr><td>{{ $row['account']->name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
            @endforeach
            <tfoot><tr><td>Total Expenses</td><td class="text-end">{{ number_format($report['totalExpenses'], 2) }}</td></tr></tfoot>
        </table>
        <p><strong>Net {{ $report['netSurplus'] >= 0 ? 'Surplus' : 'Deficit' }}:</strong> KSh {{ number_format($report['netSurplus'], 2) }}</p>
    @else
        <table>
            <tr><th colspan="2">Assets</th></tr>
            @foreach($report['assets'] as $row)
                <tr><td>{{ $row['account']->name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
            @endforeach
            <tr><td><strong>Total Assets</strong></td><td class="text-end"><strong>{{ number_format($report['totalAssets'], 2) }}</strong></td></tr>
            <tr><th colspan="2">Liabilities & Equity</th></tr>
            @foreach($report['liabilities'] as $row)
                <tr><td>{{ $row['account']->name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
            @endforeach
            @foreach($report['equity'] as $row)
                <tr><td>{{ $row['account']->name }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td></tr>
            @endforeach
            @if(abs($report['retainedEarnings']) >= 0.005)
                <tr><td>Retained earnings (computed)</td><td class="text-end">{{ number_format($report['retainedEarnings'], 2) }}</td></tr>
            @endif
            <tr><td><strong>Total Liabilities & Equity</strong></td><td class="text-end"><strong>{{ number_format($report['totalLiabilitiesAndEquity'], 2) }}</strong></td></tr>
        </table>
    @endif
</body>
</html>
