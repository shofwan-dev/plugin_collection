<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendPaymentFailedNotification
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
    public function handle(PaymentFailed $event): void
    {
        $order = $event->order;
        $reason = $event->reason;

        Log::info('Processing PaymentFailed event for WhatsApp notification', [
            'order_id' => $order->id,
            'reason' => $reason,
        ]);

        // Send notification to customer
        try {
            $message = "*Pembayaran Gagal* ❌\n\n";
            $message .= "Halo *{$order->customer_name}*,\n\n";
            $message .= "Pembayaran untuk *{$order->product->name}* (Order #{$order->id}) gagal diproses.\n\n";
            
            if ($reason) {
                $message .= "📌 *Alasan:*\n{$reason}\n\n";
            }
            
            $message .= "💡 *Apa yang harus dilakukan?*\n";
            $message .= "1. Periksa saldo atau limit kartu Anda\n";
            $message .= "2. Coba metode pembayaran lain\n";
            $message .= "3. Hubungi bank Anda jika masalah berlanjut\n\n";
            $message .= "🔗 Coba lagi di:\n";
            $message .= url('/dashboard/orders/' . $order->id) . "\n\n";
            $message .= "Butuh bantuan? Hubungi admin kami 🙏";

            $result = $this->whatsappService->sendMessage(
                $order->whatsapp_number ?? $order->customer_email,
                $message
            );

            if ($result) {
                Log::info('Payment failed WhatsApp notification sent', [
                    'order_id' => $order->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending payment failed notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
