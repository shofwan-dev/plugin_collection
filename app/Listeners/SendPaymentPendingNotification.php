<?php

namespace App\Listeners;

use App\Events\PaymentPending;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendPaymentPendingNotification
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
    public function handle(PaymentPending $event): void
    {
        $order = $event->order;

        Log::info('Processing PaymentPending event for WhatsApp notification', [
            'order_id' => $order->id,
        ]);

        // Send notification to customer
        try {
            $message = "*Pembayaran Sedang Diproses* ⏳\n\n";
            $message .= "Halo *{$order->customer_name}*,\n\n";
            $message .= "Pembayaran Anda untuk *{$order->product->name}* sedang dalam proses verifikasi.\n\n";
            $message .= "📋 *Detail Order:*\n";
            $message .= "• Order ID: #{$order->id}\n";
            $message .= "• Total: {$order->formatted_amount}\n\n";
            $message .= "⏰ *Estimasi:*\n";
            $message .= "Proses verifikasi biasanya memakan waktu 5-10 menit.\n\n";
            $message .= "Kami akan mengirimkan notifikasi segera setelah pembayaran dikonfirmasi.\n\n";
            $message .= "Track status di:\n";
            $message .= url('/dashboard/orders/' . $order->id) . "\n\n";
            $message .= "Terima kasih atas kesabaran Anda! 🙏";

            $result = $this->whatsappService->sendMessage(
                $order->whatsapp_number ?? $order->customer_email,
                $message
            );

            if ($result) {
                Log::info('Payment pending WhatsApp notification sent', [
                    'order_id' => $order->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending payment pending notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
