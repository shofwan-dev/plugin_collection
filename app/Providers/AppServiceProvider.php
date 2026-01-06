<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Laravel\Paddle\Events\TransactionCompleted;
use App\Listeners\HandlePaddleTransaction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Load Paddle Settings into Cashier Config
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $paddleSettings = \App\Models\Setting::getGroup('paddle');
                
                if (!empty($paddleSettings)) {
                    $sandbox = ($paddleSettings['paddle_environment'] ?? 'sandbox') === 'sandbox';
                    
                    config([
                        'cashier.seller_id' => $paddleSettings['paddle_seller_id'] ?? env('PADDLE_SELLER_ID'),
                        'cashier.api_key' => $paddleSettings['paddle_api_key'] ?? env('PADDLE_API_KEY'),
                        'cashier.client_side_token' => $paddleSettings['paddle_client_token'] ?? env('PADDLE_CLIENT_TOKEN'),
                        'cashier.webhook_secret' => $paddleSettings['paddle_webhook_secret'] ?? env('PADDLE_WEBHOOK_SECRET'),
                        'cashier.sandbox' => $sandbox,
                    ]);

                    // Only log warning once per day to avoid log spam
                    if (empty(config('cashier.seller_id')) || empty(config('cashier.api_key'))) {
                        $cacheKey = 'paddle_config_warning_logged';
                        if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                            \Log::warning('Paddle integration is not fully configured. Please set Seller ID and API Key in the admin panel.');
                            \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addDay());
                        }
                    }
                }

                // Load Email Settings into Mail Config
                $emailSettings = \App\Models\Setting::getGroup('email');
                
                if (!empty($emailSettings)) {
                    // Only apply if email settings are configured
                    if (!empty($emailSettings['email_host'])) {
                        config([
                            'mail.mailers.smtp.host' => $emailSettings['email_host'],
                            'mail.mailers.smtp.port' => $emailSettings['email_port'] ?? 587,
                            'mail.mailers.smtp.encryption' => $emailSettings['email_encryption'] ?? 'tls',
                            'mail.mailers.smtp.username' => $emailSettings['email_username'] ?? '',
                            'mail.mailers.smtp.password' => $emailSettings['email_password'] ?? '',
                            'mail.from.address' => $emailSettings['email_from_address'] ?? env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                            'mail.from.name' => $emailSettings['email_from_name'] ?? env('MAIL_FROM_NAME', config('app.name')),
                        ]);

                        \Log::info('Email settings loaded from database', [
                            'host' => $emailSettings['email_host'],
                            'port' => $emailSettings['email_port'] ?? 587,
                            'from' => $emailSettings['email_from_address'] ?? 'not set',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error loading settings: ' . $e->getMessage());
        }

        // Register Paddle Event Listener
        Event::listen(
            TransactionCompleted::class,
            HandlePaddleTransaction::class,
        );
    }
}
