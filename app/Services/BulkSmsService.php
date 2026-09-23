<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkSmsService
{
    protected string $url;
    protected int $batchSize;
    protected int $delayBetweenBatches;

    public function __construct()
    {
        $this->url = config('services.sms.api_url');
        $this->batchSize = 50; // Number of SMS to send in each batch
        $this->delayBetweenBatches = 1; // Seconds to wait between batches
    }

    /**
     * Send bulk SMS using queue jobs
     */
    public function sendBulkSms(array $recipients, string $message): void
    {
        // Split recipients into batches
        $batches = array_chunk($recipients, $this->batchSize);

        foreach ($batches as $batch) {
            dispatch(function () use ($batch, $message) {
                $this->processBatch($batch, $message);
            })->delay(now()->addSeconds($this->delayBetweenBatches));
        }
    }

    /**
     * Process a single batch of SMS
     */
    protected function processBatch(array $batch, string $message): void
    {
        foreach ($batch as $recipient) {
            try {
                $this->sendSingleSms($recipient['mobile'], $message);
            } catch (\Exception $e) {
                Log::error("Failed to send SMS to {$recipient['mobile']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Your existing single SMS method with minor improvements
     */
    public function sendSingleSms(?string $mobile, string $message): array
    {
        if (empty($mobile)) {
            return [
                'success' => false,
                'message' => 'Invalid Phone Number',
                'response' => null
            ];
        }

        // Normalize phone number (remove any non-digit characters)
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        if (strlen($mobile) == 11) {
            $payload = [
                'mobile' => $mobile,
                'sms'    => $message,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->post($this->url, $payload);

            if ($response->failed()) {
                Log::error('SMS API failed', [
                    'mobile' => $mobile,
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS.',
                    'response' => $response->body(),
                ];
            }

            return [
                'success' => true,
                'response' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid Phone Number',
            'response' => null
        ];
    }
}
