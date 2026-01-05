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
        $planId = $customData['plan_id'] ?? null;
        
        if (!$planId) {
            Log::warning('Paddle Transaction missing plan_id in custom_data', ['payload' => $payload]);
            return;
        }

        $plan = Plan::find($planId);
        if (!$plan) {
            Log::error('Paddle Transaction: Plan not found', ['plan_id' => $planId]);
            return;
        }

        // Create or update order
        $order = Order::updateOrCreate(
            ['paddle_transaction_id' => $transaction->paddle_id],
            [
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $billable->id,
                'plan_id' => $plan->id,
                'customer_name' => $billable->name,
                'customer_email' => $billable->email,
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
                'plan_id' => $plan->id,
                'order_id' => $order->id,
                'user_id' => $billable->id,
                'status' => 'active',
                'max_domains' => $plan->max_domains,
                'activated_domains' => [],
                'expires_at' => now()->addYear(),
            ]);

            // Send WhatsApp notifications
            try {
                $whatsapp = app(WhatsAppService::class);
                $whatsapp->sendPaymentSuccessNotification($order);
                $whatsapp->sendAdminPaymentSuccessNotification($order);
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notifications after Paddle payment', [
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
