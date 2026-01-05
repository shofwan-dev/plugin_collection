<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use App\Models\License;

class DummyOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test customer
        $customer = User::where('email', 'customer@example.com')->first();
        
        if (!$customer) {
            $customer = User::factory()->create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]);
        }

        // Get plans
        $plans = Plan::all();

        // Create 5 dummy orders with licenses
        $dummyOrders = [
            [
                'customer_name' => 'John Doe',
                'customer_email' => 'john@example.com',
                'plan_index' => 0, // Single Site
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now()->subDays(10),
            ],
            [
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane@example.com',
                'plan_index' => 1, // 5 Sites
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now()->subDays(7),
            ],
            [
                'customer_name' => 'Bob Johnson',
                'customer_email' => 'bob@example.com',
                'plan_index' => 2, // Unlimited
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now()->subDays(5),
            ],
            [
                'customer_name' => 'Alice Williams',
                'customer_email' => 'alice@example.com',
                'plan_index' => 0, // Single Site
                'status' => 'pending',
                'payment_status' => 'pending',
                'paid_at' => null,
            ],
            [
                'customer_name' => 'Charlie Brown',
                'customer_email' => 'charlie@example.com',
                'plan_index' => 1, // 5 Sites
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now()->subDays(2),
            ],
        ];

        foreach ($dummyOrders as $index => $dummyOrder) {
            $plan = $plans[$dummyOrder['plan_index']];
            
            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'user_id' => $customer->id,
                'plan_id' => $plan->id,
                'customer_name' => $dummyOrder['customer_name'],
                'customer_email' => $dummyOrder['customer_email'],
                'amount' => $plan->price,
                'currency' => 'USD',
                'status' => $dummyOrder['status'],
                'payment_status' => $dummyOrder['payment_status'],
                'stripe_session_id' => 'cs_test_' . uniqid(),
                'stripe_payment_intent' => $dummyOrder['payment_status'] === 'paid' ? 'pi_' . uniqid() : null,
                'paid_at' => $dummyOrder['paid_at'],
                'created_at' => $dummyOrder['paid_at'] ?? now(),
            ]);

            // Create license for completed orders
            if ($order->status === 'completed' && $order->payment_status === 'paid') {
                $licenseKey = $this->generateLicenseKey();
                
                $license = License::create([
                    'license_key' => $licenseKey,
                    'plan_id' => $plan->id,
                    'order_id' => $order->id,
                    'user_id' => $customer->id,
                    'status' => 'active',
                    'max_domains' => $plan->max_domains,
                    'activated_domains' => $this->generateActivatedDomains($plan->max_domains),
                    'expires_at' => now()->addYear(),
                ]);

                $this->command->info("Created order #{$order->order_number} with license {$licenseKey}");
            } else {
                $this->command->info("Created pending order #{$order->order_number}");
            }
        }

        $this->command->info('✅ Created 5 dummy orders (4 completed with licenses, 1 pending)');
    }

    /**
     * Generate unique license key
     */
    private function generateLicenseKey(): string
    {
        do {
            $key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
            $formatted = implode('-', str_split($key, 4));
        } while (License::where('license_key', $formatted)->exists());

        return $formatted;
    }

    /**
     * Generate some activated domains for demo
     */
    private function generateActivatedDomains(int $maxDomains): array
    {
        if ($maxDomains === -1) {
            // Unlimited - add 3 domains
            return [
                'example.com',
                'demo.com',
                'test.com',
            ];
        }

        if ($maxDomains === 1) {
            return ['mysite.com'];
        }

        if ($maxDomains === 5) {
            return [
                'site1.com',
                'site2.com',
            ];
        }

        return [];
    }
}
