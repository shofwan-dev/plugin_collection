<?php

namespace App\Services;

use App\Models\License;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Support\Str;

class LicenseGenerator
{
    /**
     * Generate a unique license key
     */
    public function generateKey(): string
    {
        do {
            $key = $this->formatKey(Str::upper(Str::random(16)));
        } while (License::where('license_key', $key)->exists());

        return $key;
    }

    /**
     * Format license key with dashes (XXXX-XXXX-XXXX-XXXX)
     */
    private function formatKey(string $key): string
    {
        return implode('-', str_split($key, 4));
    }

    /**
     * Create license for order
     */
    public function createForOrder(Order $order): License
    {
        $plan = $order->plan;

        $license = License::create([
            'license_key' => $this->generateKey(),
            'plan_id' => $plan->id,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => 'active',
            'max_domains' => $plan->max_domains,
            'expires_at' => now()->addYear(), // 1 year from now
        ]);

        return $license;
    }

    /**
     * Validate license key format
     */
    public function isValidFormat(string $key): bool
    {
        // Format: XXXX-XXXX-XXXX-XXXX
        return preg_match('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $key);
    }

    /**
     * Validate license key and domain
     */
    public function validate(string $licenseKey, string $domain): array
    {
        if (!$this->isValidFormat($licenseKey)) {
            return [
                'valid' => false,
                'message' => 'Invalid license key format',
            ];
        }

        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return [
                'valid' => false,
                'message' => 'License key not found',
            ];
        }

        if (!$license->isActive()) {
            return [
                'valid' => false,
                'message' => 'License is not active',
            ];
        }

        if ($license->isExpired()) {
            return [
                'valid' => false,
                'message' => 'License has expired',
            ];
        }

        if (!$license->isDomainActivated($domain)) {
            return [
                'valid' => false,
                'message' => 'Domain is not activated for this license',
            ];
        }

        // Update last checked timestamp
        $license->update(['last_checked_at' => now()]);

        return [
            'valid' => true,
            'expires_at' => $license->expires_at?->toDateString(),
            'plan' => $license->plan->name,
        ];
    }

    /**
     * Activate license for domain
     */
    public function activate(string $licenseKey, string $domain, string $ip = null): array
    {
        if (!$this->isValidFormat($licenseKey)) {
            return [
                'success' => false,
                'message' => 'Invalid license key format',
            ];
        }

        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return [
                'success' => false,
                'message' => 'License key not found',
            ];
        }

        if (!$license->isActive()) {
            return [
                'success' => false,
                'message' => 'License is not active',
            ];
        }

        if ($license->isExpired()) {
            return [
                'success' => false,
                'message' => 'License has expired',
            ];
        }

        if ($license->isDomainActivated($domain)) {
            return [
                'success' => true,
                'message' => 'Domain is already activated',
                'activation_id' => $license->id,
                'expires_at' => $license->expires_at?->toDateString(),
                'plan' => $license->plan->name,
            ];
        }

        if (!$license->canActivateDomain()) {
            return [
                'success' => false,
                'message' => 'Maximum number of domains reached for this license',
            ];
        }

        $license->activateDomain($domain, $ip);

        return [
            'success' => true,
            'message' => 'License activated successfully',
            'activation_id' => $license->id,
            'expires_at' => $license->expires_at?->toDateString(),
            'plan' => $license->plan->name,
        ];
    }

    /**
     * Deactivate license from domain
     */
    public function deactivate(string $licenseKey, string $domain): array
    {
        $license = License::where('license_key', $licenseKey)->first();

        if (!$license) {
            return [
                'success' => false,
                'message' => 'License key not found',
            ];
        }

        if (!$license->isDomainActivated($domain)) {
            return [
                'success' => false,
                'message' => 'Domain is not activated for this license',
            ];
        }

        $license->deactivateDomain($domain);

        return [
            'success' => true,
            'message' => 'License deactivated successfully',
        ];
    }
}
