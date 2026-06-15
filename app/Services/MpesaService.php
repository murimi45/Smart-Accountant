<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;

    public function __construct()
    {
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->baseUrl = config('services.mpesa.env') === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    /**
     * Get OAuth Access Token
     */
    public function getAccessToken()
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

        return $response->json()['access_token'] ?? null;
    }

    /**
     * Register C2B URLs (Validation + Confirmation)
     */
    public function registerUrls()
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/mpesa/c2b/v1/registerurl', [
                'ShortCode' => config('services.mpesa.shortcode'),
                'ResponseType' => 'Completed',
                'ConfirmationURL' => url('/api/payment/confirm'),
                'ValidationURL' => url('/api/payment/validate'),
            ]);

        return $response->json();
    }
}
