<div class="card table-card mb-4">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Balance Sheet</h5>
        <span class="text-muted small">As at {{ $period['end']->format('d M Y') }}</span>
        @if($report['balanced'])
            <span class="badge bg-success">Balanced</span>
        @else
            <span class="badge bg-warning text-dark">Check figures</span>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-3">Assets</h6>
                <table class="table table-sm">
                    <tbody>
                        @forelse($report['assets'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No asset balances</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold border-top">
                            <td>Total Assets</td>
                            <td class="text-end">{{ number_format($report['totalAssets'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted mb-3">Liabilities & Equity</h6>
                <table class="table table-sm">
                    <tbody>
                        @foreach($report['liabilities'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        @foreach($report['equity'] as $row)
                            <tr>
                                <td>{{ $row['account']->name }}</td>
                                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                        @if(abs($report['retainedEarnings']) >= 0.005)
                            <tr>
                                <td>Retained earnings (computed)</td>
                                <td class="text-end">{{ number_format($report['retainedEarnings'], 2) }}</td>
                            </tr>
                        @endif
                        @if(empty($report['liabilities']) && empty($report['equity']) && abs($report['retainedEarnings']) < 0.005)
                            <tr><td colspan="2" class="text-muted">No liability or equity balances</td></tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold border-top">
                            <td>Total Liabilities & Equity</td>
                            <td class="text-end">{{ number_format($report['totalLiabilitiesAndEquity'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
