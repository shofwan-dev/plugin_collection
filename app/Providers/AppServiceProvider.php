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

                    if (empty(config('cashier.seller_id')) || empty(config('cashier.api_key'))) {
                        \Log::warning('Paddle integration is not fully configured. Please set Seller ID and API Key in the admin panel.');
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error loading Paddle settings: ' . $e->getMessage());
        }

        // Register Paddle Event Listener
        Event::listen(
            TransactionCompleted::class,
            HandlePaddleTransaction::class,
        );
    }
}
