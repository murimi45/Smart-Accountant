<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;

class SendSmsService
{
    protected $sms;

    public function __construct()
    {
        $username = env('AT_USERNAME');
        $apiKey   = env('AT_API_KEY');

        $AT = new AfricasTalking($username, $apiKey);
        $this->sms = $AT->sms();
    }

    public function send($phone, $message)
{
    \Log::info('SendSmsService reached with phone: '.$phone);

    try {
        $recipients = $phone;
        $from = env('AT_SENDER_ID', '');

        $response = $this->sms->send([
            'to'      => $recipients,
            'message' => $message,
            'from'    => $from
        ]);

        \Log::info('SMS Response: ' . json_encode($response));

    } catch (\Exception $e) {
        \Log::error("SMS failed: " . $e->getMessage());
    }
}
}