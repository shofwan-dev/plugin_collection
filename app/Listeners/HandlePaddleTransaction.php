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
        $payload = $event->payload;
        $transaction = $event->transaction;
        $billable = $event->billable;
        
        $customData = $payload['data']['custom_data'] ?? [];
        $productId = $customData['product_id'] ?? null;
        $whatsappNumber = $customData['whatsapp_number'] ?? null;
        
        if (!$productId) {
            Log::warning('Paddle Transaction missing product_id in custom_data', ['payload' => $payload]);
            return;
        }

        $product = \App\Models\Product::find($productId);
        if (!$product) {
            Log::error('Paddle Transaction: Product not found', ['product_id' => $productId]);
            return;
        }

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

            // Send WhatsApp notifications
            try {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $whatsapp->sendPaymentSuccessNotification($order);
                $whatsapp->sendAdminPaymentSuccessNotification($order);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notifications after Paddle payment', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send Email notification
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\LicenseActivatedMail($license));
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderCreatedMail($order));
            } catch (\Exception $e) {
                Log::error('Failed to send email notifications after Paddle payment', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            Log::info('Paddle Payment Processed: Order and License created', [
                'order_id' => $order->id,
                'license_id' => $license->id,
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
