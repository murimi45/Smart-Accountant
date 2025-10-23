<?php

namespace App\Jobs;


use App\Models\SmsLog;
use App\Services\SendSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


public $smsLogId;


// Allow 3 attempts
public $tries = 3;
public $backoff = [10, 30, 60];


public function __construct(int $smsLogId)
{
$this->smsLogId = $smsLogId;
}


public function handle(SendSmsService $smsService)
{
$smsLog = SmsLog::find($this->smsLogId);


if (! $smsLog) {
return;
}


try {
$response = $smsService->sendNow($smsLog->to, $smsLog->message);


$smsLog->update([
'status' => 'sent',
'response' => json_encode($response),
]);


} catch (Exception $e) {
$smsLog->update([
'status' => 'failed',
'response' => $e->getMessage(),
]);


// Let the job fail so Laravel can retry according to $tries
throw $e;
}
}
}
