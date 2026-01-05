<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'CF7 to WhatsApp Gateway',
                'slug' => 'cf7-to-whatsapp-gateway',
                'description' => 'Connect your Contact Form 7 forms directly to WhatsApp. Send form submissions to WhatsApp numbers instantly.',
                'version' => '2.1.0',
                'type' => 'plugin',
                'is_active' => true,
                'changelog' => "- Added multi-number support\n- Improved message formatting\n- Bug fixes and performance improvements\n- Added custom field mapping",
                'requirements' => 'WordPress 5.0+, PHP 7.4+, Contact Form 7 5.0+',
            ],
            [
                'name' => 'CF7 WhatsApp Pro',
                'slug' => 'cf7-whatsapp-pro',
                'description' => 'Premium version with advanced features including analytics, templates, and automation.',
                'version' => '3.0.5',
                'type' => 'plugin',
                'is_active' => true,
                'changelog' => "- New analytics dashboard\n- WhatsApp message templates\n- Automation workflows\n- Multi-language support",
                'requirements' => 'WordPress 5.5+, PHP 8.0+, Contact Form 7 5.5+',
            ],
            [
                'name' => 'WhatsApp Business Integration',
                'slug' => 'whatsapp-business-integration',
                'description' => 'Complete WhatsApp Business API integration for WordPress websites.',
                'version' => '1.5.2',
                'type' => 'plugin',
                'is_active' => true,
                'changelog' => "- WhatsApp Business API support\n- Message broadcasting\n- Contact management\n- Automated responses",
                'requirements' => 'WordPress 5.8+, PHP 8.0+, WooCommerce 6.0+ (optional)',
            ],
            [
                'name' => 'Landing Page Template',
                'slug' => 'landing-page-template',
                'description' => 'Modern landing page template optimized for conversions with WhatsApp integration.',
                'version' => '1.0.0',
                'type' => 'website',
                'is_active' => true,
                'changelog' => "- Initial release\n- Responsive design\n- WhatsApp chat widget\n- Contact form integration",
                'requirements' => 'WordPress 5.0+, PHP 7.4+',
            ],
            [
                'name' => 'WhatsApp Chat Widget',
                'slug' => 'whatsapp-chat-widget',
                'description' => 'Floating WhatsApp chat button for your website with customizable design.',
                'version' => '2.3.1',
                'type' => 'addon',
                'is_active' => true,
                'changelog' => "- New design options\n- Mobile optimization\n- Custom greeting messages\n- Analytics tracking",
                'requirements' => 'WordPress 5.0+, PHP 7.4+',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('✅ Created 5 dummy products');
    }
}
