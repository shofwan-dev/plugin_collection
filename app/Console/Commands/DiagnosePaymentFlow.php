<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Console\Command;

class DiagnosePaymentFlow extends Command
{
    protected $signature = 'diagnose:payment-flow';
    protected $description = 'Diagnose payment flow untuk troubleshooting';

    public function handle(): int
    {
        $this->info('=== PAYMENT FLOW DIAGNOSTIC ===');
        $this->newLine();

        // 1. Check EventServiceProvider
        $this->info('1. Checking EventServiceProvider...');
        $providers = config('app.providers', []);
        $eventProviderRegistered = in_array(\App\Providers\EventServiceProvider::class, $providers) || 
                                   file_exists(base_path('bootstrap/providers.php')) && 
                                   str_contains(file_get_contents(base_path('bootstrap/providers.php')), 'EventServiceProvider');
        
        if ($eventProviderRegistered) {
            $this->line('   ✅ EventServiceProvider is registered');
        } else {
            $this->error('   ❌ EventServiceProvider NOT registered!');
            $this->error('   Fix: Add to bootstrap/providers.php');
            return 1;
        }

        // 2. Check Events
        $this->newLine();
        $this->info('2. Checking Events Registration...');
        
        $events = \Event::getListeners(\Laravel\Paddle\Events\TransactionCompleted::class);
        if (!empty($events)) {
            $this->line('   ✅ TransactionCompleted has ' . count($events) . ' listener(s)');
            foreach ($events as $listener) {
                $listenerName = is_string($listener) ? $listener : get_class($listener);
                $this->line('      - ' . $listenerName);
            }
        } else {
            $this->error('   ❌ No listeners for TransactionCompleted');
        }

        // 3. Check Database
        $this->newLine();
        $this->info('3. Checking Database...');
        
        $orderCount = Order::count();
        $this->line('   Total Orders: ' . $orderCount);
        
        if ($orderCount > 0) {
            $latestOrder = Order::latest()->first();
            $this->line('   Latest Order:');
            $this->line('      - ID: ' . $latestOrder->id);
            $this->line('      - Order Number: ' . $latestOrder->order_number);
            $this->line('      - User ID: ' . $latestOrder->user_id);
            $this->line('      - Product ID: ' . $latestOrder->product_id);
            $this->line('      - Status: ' . $latestOrder->status);
            $this->line('      - Payment Status: ' . $latestOrder->payment_status);
            $this->line('      - Paddle Transaction ID: ' . ($latestOrder->paddle_transaction_id ?? 'NULL'));
            $this->line('      - Created: ' . $latestOrder->created_at);
        }

        // 4. Check Products
        $this->newLine();
        $this->info('4. Checking Products...');
        $productCount = Product::count();
        $this->line('   Total Products: ' . $productCount);
        
        if ($productCount > 0) {
            $this->line('   Available Products:');
            Product::all()->each(function ($product) {
                $this->line('      - ' . $product->name . ' (ID: ' . $product->id . ')');
            });
        } else {
            $this->warn('   ⚠️  No products found!');
        }

        // 5. Check Users
        $this->newLine();
        $this->info('5. Checking Users...');
        $userCount = User::count();
        $this->line('   Total Users: ' . $userCount);

        // 6. Check WhatsApp Config
        $this->newLine();
        $this->info('6. Checking WhatsApp Configuration...');
        $whatsappEnabled = \App\Models\Setting::get('whatsapp_enabled', false);
        $whatsappApiUrl = \App\Models\Setting::get('whatsapp_api_url');
        $whatsappSender = \App\Models\Setting::get('whatsapp_sender');
        
        $this->line('   Enabled: ' . ($whatsappEnabled ? 'YES' : 'NO'));
        $this->line('   API URL: ' . ($whatsappApiUrl ? '✅ Set' : '❌ Not set'));
        $this->line('   Sender: ' . ($whatsappSender ? '✅ Set' : '❌ Not set'));

        // 7. Check Email Config
        $this->newLine();
        $this->info('7. Checking Email Configuration...');
        $this->line('   Mail Driver: ' . config('mail.default'));
        $this->line('   Mail From: ' . config('mail.from.address'));

        // 8. Check Paddle Config
        $this->newLine();
        $this->info('8. Checking Paddle Configuration...');
        $this->line('   Vendor ID: ' . (config('cashier.paddle.vendor_id') ? '✅ Set' : '❌ Not set'));
        $this->line('   Vendor Auth Code: ' . (config('cashier.paddle.vendor_auth_code') ? '✅ Set' : '❌ Not set'));
        $this->line('   Client Token: ' . (config('cashier.paddle.client_token') ? '✅ Set' : '❌ Not set'));

        // 9. Check Routes
        $this->newLine();
        $this->info('9. Checking Routes...');
        
        // Check webhook route
        try {
            $url = route('webhook.paddle');
            $this->line('   Webhook URL: ' . $url);
        } catch (\Exception $e) {
            $this->error('   ❌ Webhook route not found!');
        }

        // 10. Check Logs
        $this->newLine();
        $this->info('10. Checking Recent Logs...');
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            $log = file_get_contents($logFile);
            $recentLogs = substr($log, -5000); // Last 5000 chars
            
            $hasHandlePaddle = str_contains($recentLogs, 'HandlePaddleTransaction');
            $hasPaymentCompleted = str_contains($recentLogs, 'PaymentCompleted');
            $hasWhatsApp = str_contains($recentLogs, 'WhatsApp');
            
            $this->line('   HandlePaddleTransaction logs: ' . ($hasHandlePaddle ? '✅ Found' : '❌ Not found'));
            $this->line('   PaymentCompleted logs: ' . ($hasPaymentCompleted ? '✅ Found' : '❌ Not found'));
            $this->line('   WhatsApp logs: ' . ($hasWhatsApp ? '✅ Found' : '❌ Not found'));
        } else {
            $this->warn('   ⚠️  Log file not found');
        }

        // Summary
        $this->newLine();
        $this->info('=== DIAGNOSTIC COMPLETE ===');
        $this->newLine();

        if (!$eventProviderRegistered) {
            $this->error('CRITICAL: EventServiceProvider not registered!');
            $this->line('Fix: Add to bootstrap/providers.php');
            return 1;
        }

        if ($orderCount === 0) {
            $this->warn('⚠️  No orders in database. Try making a test payment.');
        }

        if (!$whatsappEnabled || !$whatsappApiUrl) {
            $this->warn('⚠️  WhatsApp not properly configured.');
        }

        $this->info('Run "php artisan test:payment-notification {order_id} completed" to test notifications.');
        
        return 0;
    }
}
