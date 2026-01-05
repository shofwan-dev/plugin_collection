<?php

namespace Database\Seeders;

use App\Models\HomepageSetting;
use Illuminate\Database\Seeder;

class HomepageSettingSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSetting::truncate();
        
        HomepageSetting::create([
            // Hero Section
            'hero_title' => 'Premium WordPress Plugins Collection',
            'hero_subtitle' => 'Transform your website with cutting-edge WhatsApp integration solutions',
            'hero_cta_text' => 'Explore Products',
            'hero_cta_link' => '#products',
            'hero_secondary_cta_text' => 'Documentation',
            'hero_secondary_cta_link' => '/documentation',
            
            // Stats
            'stats_products_label' => 'Products',
            'stats_downloads_label' => 'Downloads',
            'stats_uptime_label' => 'Uptime',
            'stats_support_label' => 'Support',
            
            // Features
            'features_title' => 'Why Choose Us?',
            'features_subtitle' => 'Premium quality, unmatched support',
            'features_items' => [
                ['icon' => 'bi-lightning-charge', 'title' => 'Lightning Fast', 'description' => 'Optimized for maximum performance', 'color' => '#6366f1'],
                ['icon' => 'bi-shield-check', 'title' => 'Secure & Safe', 'description' => 'Enterprise-grade security', 'color' => '#10b981'],
                ['icon' => 'bi-palette', 'title' => 'Customizable', 'description' => 'Fully adaptable to your needs', 'color' => '#f59e0b'],
                ['icon' => 'bi-headset', 'title' => '24/7 Support', 'description' => 'Always here to help you', 'color' => '#8b5cf6'],
                ['icon' => 'bi-arrow-repeat', 'title' => 'Auto Updates', 'description' => 'Always stay up to date', 'color' => '#06b6d4'],
                ['icon' => 'bi-graph-up', 'title' => 'Analytics', 'description' => 'Track everything that matters', 'color' => '#ec4899'],
                ['icon' => 'bi-globe', 'title' => 'Multi-language', 'description' => 'Support for all languages', 'color' => '#14b8a6'],
                ['icon' => 'bi-gem', 'title' => 'Premium Quality', 'description' => 'Built with excellence', 'color' => '#f43f5e'],
            ],
            
            // Products Section
            'products_title' => 'Our Products',
            'products_subtitle' => 'Discover powerful solutions designed to elevate your WordPress experience',
            
            // CTA Section
            'cta_title' => 'Ready to Transform Your Website?',
            'cta_subtitle' => 'Join thousands of satisfied customers worldwide',
            'cta_primary_text' => 'Get Started Now',
            'cta_primary_link' => '#products',
            'cta_secondary_text' => 'Create Account',
            'cta_secondary_link' => '/register',
            
            // SEO
            'meta_title' => 'CF7 to WhatsApp - Premium WordPress Plugins',
            'meta_description' => 'Discover our collection of premium WordPress plugins for WhatsApp integration. Transform your website with cutting-edge solutions.',
        ]);
    }
}
