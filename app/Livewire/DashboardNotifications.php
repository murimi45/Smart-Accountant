<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class DashboardNotifications extends Component
{
    // number of items per group to show
    public $perGroup = 5;

    // used to trigger re-render from outside if needed
    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function mount($perGroup = 5)
    {
        $this->perGroup = $perGroup;
    }

    /**
     * Mark all notifications (for this user) as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        if (! $user) return;

        $user->unreadNotifications->each(function (DatabaseNotification $n) {
            $n->markAsRead();
        });

        // re-render the component
        $this->emitSelf('$refresh');
    }

    /**
     * Convenience: fetch unread notifications grouped by type
     * in this exact order (grouped), newest-first inside each group.
     *
     * Returns array with keys: overdue, income, expense, summary
     */
    protected function fetchGroupedUnread()
    {
        $user = Auth::user();
        if (! $user) {
            return [
                'overdue' => collect(),
                'income'  => collect(),
                'expense' => collect(),
                'summary' => collect(),
            ];
        }

        // unreadNotifications returns a Collection of DatabaseNotification models
        $unread = $user->unreadNotifications; // Collection

        // helper filter (safely handle missing data key)
        $filterByType = function ($type) use ($unread) {
            return $unread
                ->filter(function ($n) use ($type) {
                    return isset($n->data['type']) && $n->data['type'] === $type;
                })
                ->sortByDesc('created_at')
                ->values()
                ->take($this->perGroup);
        };

        return [
            'overdue' => $filterByType('overdue'),
            'income'  => $filterByType('income'),
            'expense' => $filterByType('expense'),
            'summary' => $filterByType('summary'),
        ];
    }

    public function render()
    {
        $groups = $this->fetchGroupedUnread();

        return view('livewire.dashboard-notifications', [
            'overdue' => $groups['overdue'],
            'income'  => $groups['income'],
            'expense' => $groups['expense'],
            'summary' => $groups['summary'],
        ]);
    }
}
