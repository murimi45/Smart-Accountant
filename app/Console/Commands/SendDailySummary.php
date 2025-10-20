use App\Models\User;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Console\Command;

public function handle()
{
    $totalIncome = Payment::whereDate('created_at', today())->sum('amount');
    $totalExpense = Expense::whereDate('created_at', today())->sum('amount');

    $admins = User::where('role', 'admin')->get();

    foreach ($admins as $admin) {
        $admin->notify(new \App\Notifications\SummaryNotification([
            'title' => 'Daily Summary',
            'message' => 'Income: KES '.$totalIncome.' | Expense: KES '.$totalExpense.' (Today)',
        ]));
    }

    $this->info('Summary notifications sent!');
}
