@extends('layouts.app')

@section('main')
<div class="main-wrapper">
    <div class="page-header mb-4">
        <h4 class="mb-1">Chart of Accounts</h4>
        <p class="text-muted mb-0">System default accounts used for general ledger postings from cashbook activity</p>
    </div>

    @foreach($categoryLabels as $key => $label)
        @if(isset($accounts[$key]) && $accounts[$key]->isNotEmpty())
            <div class="card table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ $label }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th>Normal Balance</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts[$key] as $account)
                                    <tr>
                                        <td>
                                            <a href="{{ route('ledger.index', ['account_id' => $account->id]) }}">
                                                {{ $account->name }}
                                            </a>
                                        </td>
                                        <td>{{ ucfirst($account->normal_balance) }}</td>
                                        <td>
                                            @if($account->is_default)
                                                <span class="badge bg-secondary">System default</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection
