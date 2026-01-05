<?php

namespace App\Listeners;

use Laravel\Paddle\Events\TransactionCompleted;
use App\Models\Order;
use App\Models\License;
use Illuminate\Support\Str;

class HandlePaddleTransactionCompleted
{
    /**
     * Handle the event.
     */
    public function handle(TransactionCompleted $event): void
    {
        $transaction = $event->transaction;
        
        // Get custom data
        $customData = $transaction->customData ?? [];
        $productId = $customData['product_id'] ?? null;
        $userId = $customData['user_id'] ?? null;
        
        if (!$productId || !$userId) {
            \Log::warning('Paddle webhook missing custom data', [
                'transaction_id' => $transaction->id
            ]);
            return;
        }
        
        // Create or update order
        $order = Order::updateOrCreate(
            ['paddle_transaction_id' => $transaction->id],
            [
                'user_id' => $userId,
                'product_id' => $productId,
                'customer_name' => $customData['customer_name'] ?? $transaction->customer->name ?? '',
                'customer_email' => $transaction->customer->email ?? '',
                'whatsapp_number' => $customData['whatsapp_number'] ?? '',
                'amount' => $transaction->details->totals->total / 100, // Convert from cents
                'currency' => $transaction->currencyCode,
                'status' => 'completed',
                'payment_status' => 'paid',
                'paddle_transaction_id' => $transaction->id,
            ]
        );
        
        // Generate license key if not exists
        if (!$order->license) {
            $license = License::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'product_id' => $productId,
                'license_key' => $this->generateLicenseKey(),
                'status' => 'active',
                'max_activations' => $order->product->max_domains ?? 1,
                'expires_at' => null, // Lifetime license
            ]);
            
            \Log::info('License created for order', [
                'order_id' => $order->id,
                'license_key' => $license->license_key
            ]);
        }
        
        // TODO: Send email notification
        // TODO: Send WhatsApp notification
        
        \Log::info('Paddle transaction completed', [
            'order_id' => $order->id,
            'transaction_id' => $transaction->id,
            'amount' => $order->amount
        ]);
    }
    
    /**
     * Generate unique license key
     */
    private function generateLicenseKey(): string
    {
        do {
            $key = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (License::where('license_key', $key)->exists());
        
        return $key;
    }
}
