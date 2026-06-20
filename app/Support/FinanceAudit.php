<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FinanceAudit
{
    public const LOG_NAME = 'finance';

    /** @var array<string, string> */
    public const EVENT_LABELS = [
        'class_fee.created'      => 'Class fee created',
        'class_fee.updated'      => 'Class fee updated',
        'class_fee.deleted'      => 'Class fee deleted',
        'extra_fee.created'      => 'Extra fee created',
        'extra_fee.updated'      => 'Extra fee updated',
        'extra_fee.deleted'      => 'Extra fee deleted',
        'payment.recorded'       => 'Payment recorded',
        'payment.reversed'       => 'Payment reversed',
        'invoice.voided'         => 'Invoice voided',
        'fees.bulk_import'       => 'Class fees bulk import',
        'payments.bulk_import'   => 'Payments bulk import',
    ];

    /**
     * @param  array<string, mixed>  $properties
     */
    public static function log(
        string $event,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?int $schoolId = null,
        ?User $causer = null,
    ): void {
        $schoolId ??= self::resolveSchoolId($subject);
        $causer ??= Auth::user();

        $logger = activity(self::LOG_NAME)
            ->event($event)
            ->withProperties(array_merge($properties, [
                'school_id' => $schoolId,
                'ip'        => request()?->ip(),
            ]));

        if ($causer instanceof User) {
            $logger->causedBy($causer);
        }

        if ($subject instanceof Model) {
            $logger->performedOn($subject);
        }

        $logger->tap(function ($activity) use ($schoolId) {
            $activity->school_id = $schoolId;
        })->log($description);
    }

    public static function labelFor(?string $event): string
    {
        if (! $event) {
            return 'Finance action';
        }

        return self::EVENT_LABELS[$event] ?? str_replace(['.', '_'], [' — ', ' '], $event);
    }

    private static function resolveSchoolId(?Model $subject): ?int
    {
        if ($subject && isset($subject->school_id) && $subject->school_id) {
            return (int) $subject->school_id;
        }

        $user = Auth::user();

        return $user?->school_id ? (int) $user->school_id : null;
    }
}
