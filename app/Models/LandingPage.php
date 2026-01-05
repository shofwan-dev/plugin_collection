<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LandingPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'content',
        'is_active',
        'is_homepage',
        'meta_title',
        'meta_description',
        'benefits',
        'testimonials',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_homepage' => 'boolean',
        'benefits' => 'array',
        'testimonials' => 'array',
    ];

    /**
     * Get products for this landing page
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'landing_page_products')
                    ->withPivot('sort_order')
                    ->withTimestamps()
                    ->orderBy('landing_page_products.sort_order');
    }

    /**
     * Scope for active landing pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
