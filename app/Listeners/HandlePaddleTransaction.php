<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\License;
use App\Models\Plan;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Events\TransactionCompleted;

class HandlePaddleTransaction
{
    /**
     * Handle the event.
     */
    public function handle(TransactionCompleted $event): void
    {
        Log::info('HandlePaddleTransaction: Starting to process Paddle transaction', [
            'event_type' => 'TransactionCompleted',
        ]);

        $payload = $event->payload;
        $transaction = $event->transaction;
        $billable = $event->billable;
        
        Log::info('HandlePaddleTransaction: Transaction details', [
            'transaction_id' => $transaction->paddle_id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'billable_id' => $billable->id,
            'billable_email' => $billable->email,
        ]);

        $customData = $payload['data']['custom_data'] ?? [];
        $productId = $customData['product_id'] ?? null;
        $whatsappNumber = $customData['whatsapp_number'] ?? null;
        
        Log::info('HandlePaddleTransaction: Custom data', [
            'custom_data' => $customData,
            'product_id' => $productId,
            'whatsapp_number' => $whatsappNumber,
        ]);

        if (!$productId) {
            Log::warning('Paddle Transaction missing product_id in custom_data', ['payload' => $payload]);
            return;
        }

        $product = \App\Models\Product::find($productId);
        if (!$product) {
            Log::error('Paddle Transaction: Product not found', ['product_id' => $productId]);
            return;
        }

        Log::info('HandlePaddleTransaction: Product found', [
            'product_id' => $product->id,
            'product_name' => $product->name,
        ]);

        // Create or update order
        $order = Order::updateOrCreate(
            ['paddle_transaction_id' => $transaction->paddle_id],
            [
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $billable->id,
                'product_id' => $product->id,
                'customer_name' => $customData['customer_name'] ?? $payload['data']['customer']['name'] ?? $billable->name,
                'customer_email' => $customData['customer_email'] ?? $payload['data']['customer']['email'] ?? $billable->email,
                'whatsapp_number' => $whatsappNumber,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]
        );

        // Refresh to ensure we have the latest data
        $order->refresh();

        Log::info('HandlePaddleTransaction: Order created/updated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);

        // Generate license if it doesn't exist
        if (!$order->license) {
            $license = License::create([
                'license_key' => $this->generateLicenseKey(),
                'product_id' => $product->id,
                'order_id' => $order->id,
                'user_id' => $billable->id,
                'status' => 'active',
                'max_domains' => $product->max_domains,
                'activated_domains' => [],
                'expires_at' => $product->valid_days ? now()->addDays($product->valid_days) : null,
            ]);

            // Refresh order to load the license relationship
            $order->refresh();

            Log::info('License created for Paddle payment', [
                'order_id' => $order->id,
                'license_id' => $license->id,
                'license_key' => $license->license_key,
            ]);

            // Dispatch event untuk notifikasi WhatsApp (non-blocking)
            Log::info('Dispatching PaymentCompleted event', [
                'order_id' => $order->id,
            ]);
            \App\Events\PaymentCompleted::dispatch($order);

            // Send Email notification
            try {
                Log::info('Sending email notifications', [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer_email,
                ]);
                
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\LicenseActivatedMail($license));
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderCreatedMail($order));
                
                Log::info('Email notifications sent successfully', [
                    'order_id' => $order->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send email notifications after Paddle payment', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            Log::info('Paddle Payment Processed: Order and License created', [
                'order_id' => $order->id,
                'license_id' => $license->id,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'product_name' => $product->name,
            ]);
        } else {
            Log::info('License already exists for this order', [
                'order_id' => $order->id,
                'license_id' => $order->license->id,
            ]);
        }
    }

    /**
     * Generate unique license key
     */
    protected function generateLicenseKey(): string
    {
        do {
            // Format: XXXX-XXXX-XXXX-XXXX
            $key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
            $formatted = implode('-', str_split($key, 4));
        } while (License::where('license_key', $formatted)->exists());

        return $formatted;
    }
}
