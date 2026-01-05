<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    protected $fillable = [
        'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_cta_link', 'hero_secondary_cta_text', 'hero_secondary_cta_link',
        'stats_products_label', 'stats_downloads_label', 'stats_uptime_label', 'stats_support_label',
        'features_title', 'features_subtitle', 'features_items',
        'products_title', 'products_subtitle',
        'cta_title', 'cta_subtitle', 'cta_primary_text', 'cta_primary_link', 'cta_secondary_text', 'cta_secondary_link',
        'featured_landing_page_ids', 'meta_title', 'meta_description'
    ];

    protected $casts = [
        'features_items' => 'array',
        'featured_landing_page_ids' => 'array',
    ];

    public function featuredLandingPages()
    {
        if (!$this->featured_landing_page_ids) return collect();
        return \App\Models\LandingPage::whereIn('id', $this->featured_landing_page_ids)
                      ->active()
                      ->with('products.plans')
                      ->get();
    }

    public static function current()
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
