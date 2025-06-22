<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $apiKey;
    protected $phoneNumberId;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('WHATSAPP_API_KEY');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $this->baseUrl = "https://graph.facebook.com/v17.0/{$this->phoneNumberId}/messages";
    }

    public function sendMessage(string $to, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->withOptions([
                'verify' => false
            ])->post($this->baseUrl, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => 'Error al enviar mensaje de WhatsApp: ' . $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
} 