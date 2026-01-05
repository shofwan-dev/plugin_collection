<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Single Site',
                'slug' => 'single-site',
                'description' => 'Perfect for single website owners',
                'price' => 49.00,
                'max_domains' => 1,
                'features' => [
                    'License for 1 domain',
                    '1 year of updates',
                    '1 year of support',
                    'WhatsApp integration',
                    'Contact Form 7 integration',
                ],
                'sort_order' => 1,
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => '5 Sites',
                'slug' => '5-sites',
                'description' => 'Great for agencies and developers',
                'price' => 99.00,
                'max_domains' => 5,
                'features' => [
                    'License for 5 domains',
                    '1 year of updates',
                    '1 year of support',
                    'WhatsApp integration',
                    'Contact Form 7 integration',
                    'Priority support',
                ],
                'sort_order' => 2,
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'name' => 'Unlimited Sites',
                'slug' => 'unlimited',
                'description' => 'Best value for large agencies',
                'price' => 199.00,
                'max_domains' => -1, // -1 means unlimited
                'features' => [
                    'Unlimited domains',
                    '1 year of updates',
                    '1 year of support',
                    'WhatsApp integration',
                    'Contact Form 7 integration',
                    'Priority support',
                    'White label option',
                ],
                'sort_order' => 3,
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
