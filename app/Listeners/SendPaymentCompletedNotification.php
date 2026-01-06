<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Services\WhatsAppService;
use App\Mail\PaymentCompletedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentCompletedNotification
{
    protected WhatsAppService $whatsappService;

    /**
     * Create the event listener.
     */
    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentCompleted $event): void
    {
        $order = $event->order;

        Log::info('Processing PaymentCompleted event for notifications', [
            'order_id' => $order->id,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
        ]);

        // Get or create license
        $license = $order->license;
        if (!$license && $order->product) {
            // Generate license if not exists
            $license = \App\Models\License::create([
                'license_key' => $this->generateLicenseKey(),
                'product_id' => $order->product_id,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'status' => 'active',
                'max_domains' => $order->product->max_domains,
                'activated_domains' => [],
                'expires_at' => now()->addYear(),
            ]);
            
            Log::info('License generated for order', [
                'order_id' => $order->id,
                'license_key' => $license->license_key,
            ]);
        }

        // Send Email Notification
        try {
            Mail::to($order->customer_email)
                ->send(new PaymentCompletedMail($order, $license));
            
            Log::info('Payment completed email sent successfully', [
                'order_id' => $order->id,
                'email' => $order->customer_email,
                'has_attachment' => $order->product && $order->product->file_path ? 'yes' : 'no',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment completed email', [
                'order_id' => $order->id,
                'email' => $order->customer_email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Send WhatsApp notification to customer
        try {
            $result = $this->whatsappService->sendPaymentSuccessNotification($order);
            
            if ($result) {
                Log::info('WhatsApp notification sent to customer', [
                    'order_id' => $order->id,
                    'whatsapp_number' => $order->whatsapp_number,
                ]);
            } else {
                Log::warning('Failed to send WhatsApp notification to customer', [
                    'order_id' => $order->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp to customer', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Send WhatsApp notification to admin
        try {
            $result = $this->whatsappService->sendAdminPaymentSuccessNotification($order);
            
            if ($result) {
                Log::info('WhatsApp notification sent to admin', [
                    'order_id' => $order->id,
                ]);
            } else {
                Log::warning('Failed to send WhatsApp notification to admin', [
                    'order_id' => $order->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp to admin', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate unique license key
     */
    protected function generateLicenseKey(): string
    {
        do {
            $key = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8) . '-' .
                   substr(md5(uniqid(rand(), true)), 0, 8) . '-' .
                   substr(md5(uniqid(rand(), true)), 0, 8) . '-' .
                   substr(md5(uniqid(rand(), true)), 0, 8));
        } while (\App\Models\License::where('license_key', $key)->exists());

        return $key;
    }
}
