<?php

namespace App\Helpers;

class WhatsappHelper
{
    public static function sendMessage($phone, $message)
    {

        $url = config('services.whatsapp.api_url');
        $key = config('services.whatsapp.api_key');

        // Payload JSON yang akan dikirim
        $payload = [
            'phone'   => $phone,
            'message' => $message,
            'key'    => $key,
        ];

        // Inisialisasi cURL
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => $error,
            ];
        }

        // Decode JSON response (jika API-nya memang kirim JSON)
        $decoded = json_decode($response, true);

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status' => $statusCode,
            'response' => $decoded ?: $response,
        ];
    }
}
