<div class="card table-card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Trial Balance</h5>
        @if($report['balanced'])
            <span class="badge bg-success">Balanced</span>
        @else
            <span class="badge bg-warning text-dark">Out of balance</span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Category</th>
                        <th class="text-end">Debit (KSh)</th>
                        <th class="text-end">Credit (KSh)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['rows'] as $row)
                        <tr>
                            <td>{{ $row['account']->name }}</td>
                            <td>{{ ucfirst($row['category']) }}</td>
                            <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '-' }}</td>
                            <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No ledger activity for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($report['rows']) > 0)
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2">Totals</td>
                            <td class="text-end">{{ number_format($report['totalDebits'], 2) }}</td>
                            <td class="text-end">{{ number_format($report['totalCredits'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
