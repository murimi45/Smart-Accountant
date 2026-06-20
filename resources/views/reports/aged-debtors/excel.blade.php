<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
    <x:Name>Aged Debtors</x:Name>
    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
    </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="11">Aged Debtors Report — {{ $schoolName }}</th>
        </tr>
        <tr>
            <td colspan="11">
                As at: {{ $report['asOf']->format('d M Y') }}
                @if($selectedTerm) | Term: {{ $selectedTerm->name }} {{ $selectedTerm->year }} @endif
                @if($selectedClass) | Class: {{ $selectedClass->name }} @endif
                | Debtors: {{ $report['debtorCount'] }}
            </td>
        </tr>
        <tr>
            <th>Admission</th>
            <th>Student</th>
            <th>Class</th>
            <th>Term</th>
            <th>Oldest Invoice</th>
            <th>Days Outstanding</th>
            <th>Current (0–30)</th>
            <th>31–60 Days</th>
            <th>61–90 Days</th>
            <th>91+ Days</th>
            <th>Total Balance</th>
        </tr>
        @foreach($report['rows'] as $row)
            <tr>
                <td>{{ $row['admission'] }}</td>
                <td>{{ $row['student_name'] }}</td>
                <td>{{ $row['class_name'] }}</td>
                <td>{{ $row['term_name'] }}</td>
                <td>{{ $row['invoice_date'] ?? '' }}</td>
                <td>{{ $row['days_outstanding'] }}</td>
                <td>{{ number_format($row['buckets']['current'], 2, '.', '') }}</td>
                <td>{{ number_format($row['buckets']['days_31_60'], 2, '.', '') }}</td>
                <td>{{ number_format($row['buckets']['days_61_90'], 2, '.', '') }}</td>
                <td>{{ number_format($row['buckets']['days_91_plus'], 2, '.', '') }}</td>
                <td>{{ number_format($row['total_balance'], 2, '.', '') }}</td>
            </tr>
        @endforeach
        @if(count($report['rows']) > 0)
            <tr>
                <td colspan="6"><strong>Totals</strong></td>
                <td><strong>{{ number_format($report['totals']['current'], 2, '.', '') }}</strong></td>
                <td><strong>{{ number_format($report['totals']['days_31_60'], 2, '.', '') }}</strong></td>
                <td><strong>{{ number_format($report['totals']['days_61_90'], 2, '.', '') }}</strong></td>
                <td><strong>{{ number_format($report['totals']['days_91_plus'], 2, '.', '') }}</strong></td>
                <td><strong>{{ number_format($report['totals']['total_balance'], 2, '.', '') }}</strong></td>
            </tr>
        @endif
    </table>
</body>
</html>
