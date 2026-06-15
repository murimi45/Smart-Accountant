<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsDeliveryController extends Controller
{
    public function receive(Request $request)
    {
        Log::info('DLR payload: ' . json_encode($request->all()));

        $providerMessageId = $request->input('id')
            ?? $request->input('messageId')
            ?? $request->input('message_id');

        if (! $providerMessageId) {
            Log::warning('SMS delivery report missing provider message id — skipped update');

            return response('OK', 200);
        }

        $smsLog = SmsLog::withoutGlobalScopes()
            ->where('provider_message_id', $providerMessageId)
            ->first();

        if (! $smsLog) {
            Log::warning('SMS delivery report: no log matched provider id', [
                'provider_message_id' => $providerMessageId,
            ]);

            return response('OK', 200);
        }

        $status = $request->input('status') ?? 'delivered';

        $smsLog->update([
            'status'   => $status,
            'response' => json_encode($request->all()),
        ]);

        return response('OK', 200);
    }
}
