<?php


namespace App\Services;


use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;


class SendSmsService
{
protected $sms;


public function __construct()
{
$username = env('AT_USERNAME');
$apiKey = env('AT_API_KEY');


$AT = new AfricasTalking($username, $apiKey);
$this->sms = $AT->sms();
}


// Immediate send (used by job). Returns the API response array/object.
public function sendNow(string $phone, string $message)
{
Log::info('SendSmsService.sendNow reached with phone: ' . $phone);


// Ensure phone is in international format
$phone = $this->formatToInternational($phone);


$from = env('AT_SENDER_ID', '');


$response = $this->sms->send([
'to' => $phone,
'message' => $message,
'from' => $from,
]);


Log::info('SMS Response: ' . json_encode($response));


return $response;
}


// Convenience wrapper for synchronous local dev (keeps old method name)
public function send(string $phone, string $message)
{
return $this->sendNow($phone, $message);
}


protected function formatToInternational(string $phone)
{
$phone = trim($phone);
if (preg_match('/^0[1-9]/', $phone)) {
return preg_replace('/^0/', '+254', $phone);
}
if (preg_match('/^\d{9,}/', $phone) && substr($phone, 0, 1) !== '+') {
return '+' . $phone;
}
return $phone;
}
}