<?php

namespace App\Services;

use App\Jobs\SendSmsJob;
use App\Models\FeeReminderSetting;
use App\Models\Invoice;
use App\Models\Schools;
use App\Models\SmsLog;
use App\Models\Term;
use App\Models\User;
use App\Notifications\OverdueFeesNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OverdueSmsReminderService
{
    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_OVERDUE_AUTO = 'overdue_auto';

    /**
     * @return array{schools: int, queued: int, skipped: int}
     */
    public function runAll(?Carbon $asOf = null): array
    {
        $asOf = ($asOf ?? now())->copy()->startOfDay();

        $stats = ['schools' => 0, 'queued' => 0, 'skipped' => 0];

        $settings = FeeReminderSetting::withoutGlobalScopes()
            ->where('enabled', true)
            ->get();

        foreach ($settings as $setting) {
            $stats['schools']++;
            $schoolStats = $this->runForSchool($setting, $asOf);
            $stats['queued'] += $schoolStats['queued'];
            $stats['skipped'] += $schoolStats['skipped'];

            if ($schoolStats['queued'] > 0) {
                $this->notifyStaff($setting->school_id, $schoolStats['queued']);
            }
        }

        return $stats;
    }

    /**
     * @return array{queued: int, skipped: int}
     */
    public function runForSchool(FeeReminderSetting $setting, ?Carbon $asOf = null): array
    {
        $asOf = ($asOf ?? now())->copy()->startOfDay();
        $stats = ['queued' => 0, 'skipped' => 0];

        foreach ($this->eligibleInvoices($setting, $asOf) as $invoice) {
            if ($this->queueOverdueReminder($invoice, $setting, $asOf)) {
                $stats['queued']++;
            } else {
                $stats['skipped']++;
            }
        }

        return $stats;
    }

    /** @return Collection<int, Invoice> */
    public function eligibleInvoices(FeeReminderSetting $setting, Carbon $asOf): Collection
    {
        $cutoff = $asOf->copy()->subDays(max(0, (int) $setting->min_days_outstanding));

        $query = Invoice::withoutGlobalScopes()
            ->where('school_id', $setting->school_id)
            ->where('balance', '>', 0)
            ->excludeVoided()
            ->excludeTransferred()
            ->whereDate('invoice_date', '<=', $cutoff)
            ->with(['student', 'term.academicYear']);

        if ($setting->current_term_only) {
            $currentTermId = optional(Term::current1($setting->school_id))->id;
            if (! $currentTermId) {
                return collect();
            }
            $query->where('term_id', $currentTermId);
        }

        return $query->get()->filter(function (Invoice $invoice) use ($setting, $asOf) {
            if (! $invoice->student?->phone) {
                return false;
            }

            return ! $this->recentAutoReminderExists(
                $setting->school_id,
                $invoice->id,
                $asOf,
                (int) $setting->reminder_interval_days
            );
        })->values();
    }

    public function queueOverdueReminder(Invoice $invoice, FeeReminderSetting $setting, Carbon $asOf): bool
    {
        $student = $invoice->student;
        if (! $student?->phone) {
            return false;
        }

        if ($this->recentAutoReminderExists(
            $invoice->school_id,
            $invoice->id,
            $asOf,
            (int) $setting->reminder_interval_days
        )) {
            return false;
        }

        $daysOutstanding = max(
            0,
            (int) Carbon::parse($invoice->invoice_date)->startOfDay()->diffInDays($asOf, false)
        );

        $schoolName = Schools::query()->whereKey($invoice->school_id)->value('school_name');

        return $this->queueSms(
            $invoice,
            self::buildOverdueMessage($invoice, $daysOutstanding, $schoolName),
            self::SOURCE_OVERDUE_AUTO
        );
    }

    public function queueBalanceReminder(Invoice $invoice): bool
    {
        return $this->queueSms(
            $invoice,
            self::buildBalanceMessage($invoice),
            self::SOURCE_MANUAL
        );
    }

    public function queueSms(Invoice $invoice, string $message, string $source): bool
    {
        $student = $invoice->student;
        if (! $student?->phone) {
            return false;
        }

        $smsLog = SmsLog::create([
            'school_id'  => $invoice->school_id,
            'to'         => self::formatPhone($student->phone),
            'message'    => $message,
            'status'     => 'pending',
            'source'     => $source,
            'student_id' => $student->id,
            'invoice_id' => $invoice->id,
        ]);

        dispatch(new SendSmsJob($smsLog->id, $invoice->school_id));

        return true;
    }

    public static function buildBalanceMessage(Invoice $invoice): string
    {
        $student = $invoice->student;

        return sprintf(
            'Dear Parent, %s has an outstanding balance of KES %s. Kindly clear the balance. Thank you.',
            $student->full_name,
            number_format((float) $invoice->balance, 2)
        );
    }

    public static function buildOverdueMessage(Invoice $invoice, int $daysOutstanding, ?string $schoolName = null): string
    {
        $student = $invoice->student;
        $prefix = $schoolName ? "{$schoolName}: " : '';

        return $prefix.sprintf(
            'Dear Parent, %s school fees are overdue (%d days). Outstanding balance: KES %s. Kindly pay promptly. Thank you.',
            $student->full_name,
            $daysOutstanding,
            number_format((float) $invoice->balance, 2)
        );
    }

    public static function formatPhone(string $phone): string
    {
        $phone = trim($phone);
        if (preg_match('/^0[1-9]/', $phone)) {
            return preg_replace('/^0/', '+254', $phone);
        }
        if (preg_match('/^\d{9,}/', $phone) && substr($phone, 0, 1) !== '+') {
            return '+'.$phone;
        }

        return $phone;
    }

    private function recentAutoReminderExists(
        int $schoolId,
        int $invoiceId,
        Carbon $asOf,
        int $intervalDays
    ): bool {
        $since = $asOf->copy()->subDays(max(1, $intervalDays))->startOfDay();

        return SmsLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('invoice_id', $invoiceId)
            ->where('source', self::SOURCE_OVERDUE_AUTO)
            ->where('created_at', '>=', $since)
            ->exists();
    }

    private function notifyStaff(int $schoolId, int $count): void
    {
        $recipients = User::query()
            ->where('school_id', $schoolId)
            ->whereIn('role', ['admin', 'accountant', 'Admin', 'Accountant'])
            ->get();

        foreach ($recipients as $user) {
            $user->notify(new OverdueFeesNotification($count));
        }
    }
}
