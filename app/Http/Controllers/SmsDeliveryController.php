<?php
namespace App\Http\Controllers;

use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SmsDeliveryController extends Controller
{
// Africa's Talking will POST delivery receipts here (adjust keys as AT sends)
public function receive(Request $request)
{
Log::info('DLR payload: ' . json_encode($request->all()));


// Example: AT may send "id", "status", "to" in their webhook - adjust mapping
$to = $request->input('to');
$status = $request->input('status') ?? 'delivered';
$external = $request->input('id') ?? null;


// Find latest pending sms log for the 'to' number
$smsLog = SmsLog::where('to', $to)->where('status', 'sent')->latest()->first();


if ($smsLog) {
$smsLog->update([
'status' => $status,
'response' => json_encode($request->all()),
]);
}


return response('OK', 200);
}
}
