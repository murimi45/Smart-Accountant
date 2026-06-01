<div class="notifications-card" wire:poll.60s>
    <h5><i class="fa fa-bell me-2"></i> Recent Notifications</h5>

    {{-- OVERDUE / PAYMENT DUE (warning) --}}
    @foreach($overdue as $item)
        <div class="notification-item">
            <div class="notification-icon warning">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div class="notification-content">
                <h6>{{ $item->data['title'] ?? 'Payment Overdue' }}</h6>
                <p>{{ $item->data['message'] ?? '' }}</p>
                <small><i class="fa fa-clock"></i> {{ $item->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @endforeach

    {{-- INCOME (success) --}}
    @foreach($income as $item)
        <div class="notification-item">
            <div class="notification-icon success">
                <i class="fa fa-check-circle"></i>
            </div>
            <div class="notification-content">
                <h6>{{ $item->data['title'] ?? 'Payment Received' }}</h6>
                <p>{{ $item->data['message'] ?? '' }}</p>
                <small><i class="fa fa-clock"></i> {{ $item->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @endforeach

    @foreach($other_income as $item)
    <div class="notification-item">
        <div class="notification-icon success">
            <i class="fa fa-check-circle"></i>
        </div>
        <div class="notification-content">
            <h6>{{ $item->data['title'] ?? 'Other Income Recorded' }}</h6>
            <p>{{ $item->data['message'] ?? '' }}</p>
            <small><i class="fa fa-clock"></i> {{ $item->created_at->diffForHumans() }}</small>
        </div>
    </div>
@endforeach


    {{-- EXPENSE (info/danger) --}}
    @foreach($expense as $item)
        <div class="notification-item">
            <div class="notification-icon info">
                <i class="fa fa-info-circle"></i>
            </div>
            <div class="notification-content">
                <h6>{{ $item->data['title'] ?? 'Expense Recorded' }}</h6>
                <p>{{ $item->data['message'] ?? '' }}</p>
                <small><i class="fa fa-clock"></i> {{ $item->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @endforeach

    {{-- DAILY SUMMARY (info) --}}
    @foreach($summary as $item)
        <div class="notification-item">
            <div class="notification-icon warning">
                <i class="fa fa-bell"></i>
            </div>
            <div class="notification-content">
                <h6>{{ $item->data['title'] ?? 'Daily Summary' }}</h6>
                <p>{{ $item->data['message'] ?? '' }}</p>
                <small><i class="fa fa-clock"></i> {{ $item->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @endforeach

    {{-- If there are no unread notifications in any group, show friendly message --}}
    @if(
        $overdue->isEmpty() &&
        $income->isEmpty() &&
        $expense->isEmpty() &&
        $summary->isEmpty()
    )
        <div class="notification-item">
            <div class="notification-icon info">
                <i class="fa fa-info-circle"></i>
            </div>
            <div class="notification-content">
                <h6>No new notifications</h6>
                <p>You're all caught up — no unread notifications.</p>
            </div>
        </div>
    @endif

    <button class="btn btn-sm btn-primary mt-3" wire:click="markAllAsRead">
        Mark All as Read
    </button>
</div>
