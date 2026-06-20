<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aged Debtors Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h2 { margin: 0 0 4px; font-size: 16px; }
        .meta { margin-bottom: 14px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; }
        th { background: #f0f0f0; text-align: left; }
        .text-end { text-align: right; }
        tfoot td { font-weight: bold; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Aged Debtors Report</h2>
        <p>{{ auth()->user()->school->school_name ?? 'School' }}</p>
    </div>

    <div class="meta">
        <strong>As at:</strong> {{ $report['asOf']->format('d M Y') }}<br>
        @if($selectedTerm)
            <strong>Term:</strong> {{ $selectedTerm->name }} {{ $selectedTerm->year }}<br>
        @endif
        @if($selectedClass)
            <strong>Class:</strong> {{ $selectedClass->name }}<br>
        @endif
        <strong>Debtors:</strong> {{ $report['debtorCount'] }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Admission</th>
                <th>Student</th>
                <th>Class</th>
                <th>Term</th>
                <th>Oldest Invoice</th>
                <th>Days</th>
                <th class="text-end">Current</th>
                <th class="text-end">31–60</th>
                <th class="text-end">61–90</th>
                <th class="text-end">91+</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['rows'] as $row)
                <tr>
                    <td>{{ $row['admission'] }}</td>
                    <td>{{ $row['student_name'] }}</td>
                    <td>{{ $row['class_name'] }}</td>
                    <td>{{ $row['term_name'] }}</td>
                    <td>{{ $row['invoice_date'] ?? '—' }}</td>
                    <td>{{ $row['days_outstanding'] }}</td>
                    <td class="text-end">{{ number_format($row['buckets']['current'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['buckets']['days_31_60'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['buckets']['days_61_90'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['buckets']['days_91_plus'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['total_balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        @if(count($report['rows']) > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="text-end">Totals</td>
                    <td class="text-end">{{ number_format($report['totals']['current'], 2) }}</td>
                    <td class="text-end">{{ number_format($report['totals']['days_31_60'], 2) }}</td>
                    <td class="text-end">{{ number_format($report['totals']['days_61_90'], 2) }}</td>
                    <td class="text-end">{{ number_format($report['totals']['days_91_plus'], 2) }}</td>
                    <td class="text-end">{{ number_format($report['totals']['total_balance'], 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
