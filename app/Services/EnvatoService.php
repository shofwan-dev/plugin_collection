<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnvatoService
{
    protected $apiToken;
    protected $apiUrl = 'https://api.envato.com/v3';

    public function __construct()
    {
        $this->apiToken = \App\Models\Setting::get('envato_api_token') ?? config('services.envato.api_token');
    }

    /**
     * Verify Envato purchase code
     */
    public function verifyPurchaseCode(string $purchaseCode): array
    {
        if (empty($this->apiToken)) {
            return [
                'success' => false,
                'message' => 'Envato API token not configured',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'User-Agent' => 'Purchase Verification',
            ])->get("{$this->apiUrl}/market/author/sale", [
                'code' => $purchaseCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Envato Purchase Verified', [
                    'purchase_code' => $purchaseCode,
                    'buyer' => $data['buyer'] ?? 'Unknown',
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'buyer' => $data['buyer'] ?? null,
                    'purchase_date' => $data['sold_at'] ?? null,
                    'item_name' => $data['item']['name'] ?? null,
                    'item_id' => $data['item']['id'] ?? null,
                    'license' => $data['license'] ?? 'regular',
                    'supported_until' => $data['supported_until'] ?? null,
                ];
            }

            Log::warning('Envato Purchase Verification Failed', [
                'purchase_code' => $purchaseCode,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => 'Invalid purchase code or already used',
            ];

        } catch (\Exception $e) {
            Log::error('Envato API Error', [
                'error' => $e->getMessage(),
                'purchase_code' => $purchaseCode,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to verify purchase code: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if purchase code is already used
     */
    public function isPurchaseCodeUsed(string $purchaseCode): bool
    {
        return \App\Models\License::where('envato_purchase_code', $purchaseCode)->exists();
    }

    /**
     * Get purchase details
     */
    public function getPurchaseDetails(string $purchaseCode): ?array
    {
        $result = $this->verifyPurchaseCode($purchaseCode);
        
        return $result['success'] ? $result['data'] : null;
    }

    /**
     * Check if support is active
     */
    public function isSupportActive(string $purchaseCode): bool
    {
        $result = $this->verifyPurchaseCode($purchaseCode);
        
        if (!$result['success']) {
            return false;
        }

        if (!isset($result['supported_until'])) {
            return false;
        }

        $supportedUntil = \Carbon\Carbon::parse($result['supported_until']);
        
        return $supportedUntil->isFuture();
    }
}
