<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\SendSmsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public int $smsLogId,
        public int $schoolId
    ) {}

    public function handle(SendSmsService $smsService): void
    {
        $smsLog = SmsLog::findForSchool($this->schoolId, $this->smsLogId);

        if (! $smsLog) {
            return;
        }

        try {
            $response = $smsService->sendNow($smsLog->to, $smsLog->message);
            $messageId = self::extractProviderMessageId($response);

            $smsLog->update([
                'status' => 'sent',
                'response' => is_string($response) ? $response : json_encode($response),
                'provider_message_id' => $messageId,
            ]);
        } catch (Exception $e) {
            $smsLog->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private static function extractProviderMessageId(mixed $response): ?string
    {
        if (is_string($response)) {
            $decoded = json_decode($response, true);

            if (is_array($decoded)) {
                $response = $decoded;
            } else {
                return null;
            }
        }

        if (! is_array($response)) {
            return null;
        }

        $recipients = $response['SMSMessageData']['Recipients'] ?? null;

        if (is_array($recipients) && isset($recipients[0]['messageId'])) {
            return (string) $recipients[0]['messageId'];
        }

        if (isset($response['messageId'])) {
            return (string) $response['messageId'];
        }

        return null;
    }
}
