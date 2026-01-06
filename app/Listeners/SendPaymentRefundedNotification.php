<?php

namespace App\Listeners;

use App\Events\PaymentRefunded;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendPaymentRefundedNotification
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
    public function handle(PaymentRefunded $event): void
    {
        $order = $event->order;

        Log::info('Processing PaymentRefunded event for WhatsApp notification', [
            'order_id' => $order->id,
        ]);

        // Send notification to customer
        try {
            $result = $this->whatsappService->sendPaymentRefundedNotification($order);

            if ($result) {
                Log::info('Payment refunded WhatsApp notification sent', [
                    'order_id' => $order->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending payment refunded notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
