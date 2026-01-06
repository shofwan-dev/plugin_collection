<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

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

        Log::info('Processing PaymentCompleted event for WhatsApp notification', [
            'order_id' => $order->id,
            'customer_name' => $order->customer_name,
        ]);

        // Send notification to customer
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

        // Send notification to admin
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
}
