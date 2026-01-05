<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'purchase_source',
        'envato_purchase_code',
        'envato_buyer_username',
        'envato_verified_at',
        'envato_purchase_data',
        'plan_id',
        'order_id',
        'user_id',
        'status',
        'max_domains',
        'activated_domains',
        'expires_at',
        'last_checked_at',
    ];

    protected $casts = [
        'activated_domains' => 'array',
        'envato_purchase_data' => 'array',
        'expires_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'envato_verified_at' => 'datetime',
    ];

    /**
     * Get the plan for this license
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the order for this license
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user for this license
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if license is active
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if license is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if domain can be activated
     */
    public function canActivateDomain(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $activatedCount = count($this->activated_domains ?? []);
        
        if ($this->max_domains === -1) {
            return true; // Unlimited
        }

        return $activatedCount < $this->max_domains;
    }

    /**
     * Check if domain is already activated
     */
    public function isDomainActivated(string $domain): bool
    {
        $domains = $this->activated_domains ?? [];
        
        foreach ($domains as $activatedDomain) {
            if ($activatedDomain['domain'] === $domain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Activate domain
     */
    public function activateDomain(string $domain, string $ip = null): bool
    {
        if ($this->isDomainActivated($domain)) {
            return true; // Already activated
        }

        if (!$this->canActivateDomain()) {
            return false;
        }

        $domains = $this->activated_domains ?? [];
        $domains[] = [
            'domain' => $domain,
            'activated_at' => now()->toDateTimeString(),
            'ip' => $ip,
        ];

        $this->activated_domains = $domains;
        $this->save();

        return true;
    }

    /**
     * Deactivate domain
     */
    public function deactivateDomain(string $domain): bool
    {
        $domains = $this->activated_domains ?? [];
        $filtered = array_filter($domains, function($d) use ($domain) {
            return $d['domain'] !== $domain;
        });

        $this->activated_domains = array_values($filtered);
        $this->save();

        return true;
    }

    /**
     * Get remaining activations
     */
    public function getRemainingActivationsAttribute(): int
    {
        if ($this->max_domains === -1) {
            return -1; // Unlimited
        }

        $activatedCount = count($this->activated_domains ?? []);
        return max(0, $this->max_domains - $activatedCount);
    }

    /**
     * Get days until expiration
     */
    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Scope for active licenses
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for expired licenses
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
