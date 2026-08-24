<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSensyService
{
    const CAMPAIGN_API = 'https://backend.aisensy.com/campaign/t1/api';

    /**
     * Send a single WhatsApp message via AiSensy Campaign API.
     *
     * @param  string  $phone       Recipient phone (10 digits or with country code)
     * @param  string  $contactName Recipient name
     * @param  string  $campaignName AiSensy campaign name (must match approved template name)
     * @param  array   $templateParams Array of template variable values e.g. ['Rahul', '450', '112.50']
     * @return array   ['success' => bool, 'message' => string]
     */
    public static function sendCampaignMessage(
        string $phone,
        string $contactName,
        string $campaignName,
        array  $templateParams = []
    ): array {
        $apiKey = config('services.aisensy.campaign_key');

        if (!$apiKey) {
            return ['success' => false, 'message' => 'AiSensy API key not configured.'];
        }

        // Normalize phone number to E.164 format with India code
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        } elseif (!str_starts_with($phone, '91')) {
            $phone = '91' . ltrim($phone, '0');
        }

        $payload = [
            'apiKey'         => $apiKey,
            'campaignName'   => $campaignName,
            'destination'    => $phone,
            'userName'       => $contactName,
            'templateParams' => $templateParams,
            'source'         => 'Harahori CRM',
            'media'          => (object)[],
            'buttons'        => [],
            'carouselCards'  => [],
            'location'       => (object)[],
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::CAMPAIGN_API, $payload);

            $body = $response->json();

            if ($response->successful() && isset($body['success']) && $body['success']) {
                return ['success' => true, 'message' => 'Sent successfully'];
            }

            $errMsg = $body['message'] ?? $body['error'] ?? 'Unknown error from AiSensy';
            Log::warning("AiSensy send failed for {$phone}: {$errMsg}", $body ?? []);
            return ['success' => false, 'message' => $errMsg];

        } catch (\Throwable $e) {
            Log::error("AiSensy exception for {$phone}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send bulk campaign messages to a list of contacts.
     * Returns a summary array with sent/failed counts.
     */
    public static function sendBulk(
        array  $contacts,       // array of ['name', 'phone', 'params' => [...]]
        string $campaignName,
        int    $delayMs = 300   // delay between messages in milliseconds
    ): array {
        $sent   = 0;
        $failed = 0;
        $errors = [];

        foreach ($contacts as $contact) {
            $result = self::sendCampaignMessage(
                phone:          $contact['phone'],
                contactName:    $contact['name'],
                campaignName:   $campaignName,
                templateParams: $contact['params'] ?? []
            );

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
                $errors[] = $contact['name'] . ': ' . $result['message'];
            }

            // Small delay to avoid rate limiting
            if ($delayMs > 0) {
                usleep($delayMs * 1000);
            }
        }

        return [
            'sent'   => $sent,
            'failed' => $failed,
            'total'  => $sent + $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Test the API connection.
     */
    public static function testConnection(): array
    {
        $apiKey = config('services.aisensy.campaign_key');
        if (!$apiKey) return ['success' => false, 'message' => 'API key not set in .env'];

        // Send a minimal request to check auth
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::CAMPAIGN_API, [
                    'apiKey'       => $apiKey,
                    'campaignName' => 'test_ping',
                    'destination'  => '919007362203',
                    'userName'     => 'Test',
                ]);
            // Any response (even error) means API is reachable
            return ['success' => true, 'message' => 'API reachable. Status: ' . $response->status()];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Cannot reach AiSensy: ' . $e->getMessage()];
        }
    }
}
