<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::first();
        
        if (!$product) return;

        $lp = LandingPage::create([
            'title' => 'Special WhatsApp Bundle',
            'slug' => 'bundle-wa',
            'hero_title' => 'Boost Your Sales with WhatsApp Integration',
            'hero_subtitle' => 'The ultimate solution for Contact Form 7 and WordPress.',
            'content' => "This is a special limited time offer for our premium gateway plugin. 
            
            Benefits of using our plugin:
            - Instant notifications
            - Improved customer lead response time
            - Easy set up within 5 minutes
            
            Don't miss out on this collection of features.",
            'is_active' => true,
            'meta_title' => 'WhatsApp Bundle Offer - CF7 Gateway',
            'meta_description' => 'Special promotion for our premium WhatsApp integration plugin.',
        ]);

        $lp->products()->attach($product->id, ['sort_order' => 1]);
    }
}
