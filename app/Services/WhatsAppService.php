<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;
    protected $enabled;

    public function __construct()
    {
        $this->apiUrl = Setting::get('whatsapp_api_url');
        $this->apiKey = Setting::get('whatsapp_api_key');
        $this->sender = Setting::get('whatsapp_sender');
        $this->enabled = Setting::get('whatsapp_enabled', false);
    }

    /**
     * Send WhatsApp message using MPWA API
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->enabled || !$this->apiUrl || !$this->apiKey || !$this->sender) {
            Log::warning('WhatsApp service not configured or disabled', [
                'enabled' => $this->enabled,
                'api_url' => $this->apiUrl ? 'set' : 'not set',
                'api_key' => $this->apiKey ? 'set' : 'not set',
                'sender' => $this->sender ? 'set' : 'not set',
            ]);
            return false;
        }

        try {
            // Check if it's a WhatsApp Group ID or regular number
            $isGroupId = str_contains($to, '@g.us') || str_contains($to, '@c.us');
            
            if ($isGroupId) {
                // For group IDs, use as-is (e.g., 120363166537946168@g.us)
                $cleanNumber = $to;
            } else {
                // Clean phone number (remove + and spaces)
                $cleanNumber = preg_replace('/[^0-9]/', '', $to);
                
                // Ensure number starts with country code
                if (!str_starts_with($cleanNumber, '62')) {
                    // If starts with 0, replace with 62
                    if (str_starts_with($cleanNumber, '0')) {
                        $cleanNumber = '62' . substr($cleanNumber, 1);
                    }
                }
            }

            Log::info('Sending WhatsApp message', [
                'to' => $cleanNumber,
                'is_group' => $isGroupId,
                'sender' => $this->sender,
                'api_url' => $this->apiUrl,
            ]);

            // Get dynamic footer from site name
            $siteName = Setting::get('site_name', 'CF7 to WhatsApp Gateway');
            $footer = $siteName;

            // Send using MPWA API format
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'sender' => $this->sender,
                'number' => $cleanNumber,
                'message' => $message,
                'footer' => $footer,
            ]);

            $responseData = $response->json();

            Log::info('WhatsApp API Response', [
                'status' => $response->status(),
                'body' => $responseData,
            ]);

            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === true) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $cleanNumber,
                    'response' => $responseData,
                ]);
                return true;
            }

            Log::error('WhatsApp API error', [
                'status' => $response->status(),
                'response' => $responseData,
                'to' => $cleanNumber,
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'error' => $e->getMessage(),
                'to' => $to,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send order created notification to customer
     */
    public function sendOrderCreatedNotification(Order $order): bool
    {
        $message = "*Terima kasih telah melakukan order!* 🎉\n\n";
        $message .= "📦 *Detail Pesanan*\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "• Order ID: #{$order->id}\n";
        $message .= "• Produk: {$order->product->name}\n";
        $message .= "• Total: " . $order->formatted_amount . "\n\n";
        $message .= "💳 *Langkah Selanjutnya:*\n";
        $message .= "Silakan lakukan pembayaran untuk mengaktifkan license Anda.\n\n";
        $message .= "Klik link berikut untuk melanjutkan pembayaran:\n";
        $message .= url('/dashboard/orders/' . $order->id) . "\n\n";
        $message .= "⏰ Selesaikan pembayaran dalam 24 jam agar pesanan tidak dibatalkan otomatis.\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->sendMessage($order->whatsapp_number ?? $order->customer_email, $message);
    }

    /**
     * Send payment success notification to customer
     */
    public function sendPaymentSuccessNotification(Order $order): bool
    {
        $message = "*Alhamdulillah! Pembayaran Berhasil* 🎊\n\n";
        $message .= "✅ *PEMBAYARAN DITERIMA*\n";
        $message .= "━━━━━━━━━━━━━━━━\n\n";
        $message .= "Pembayaran Anda untuk *{$order->product->name}* telah kami terima dengan total {$order->formatted_amount}.\n\n";
        $message .= "📋 *License Key Anda:*\n";
        if ($order->license) {
            $message .= "`{$order->license->license_key}`\n\n";
        }
        $message .= "🎯 *Langkah Selanjutnya:*\n";
        $message .= "1. Download plugin dari dashboard\n";
        $message .= "2. Install di WordPress Anda\n";
        $message .= "3. Aktivasi dengan license key di atas\n\n";
        $message .= "Track license Anda:\n";
        $message .= url('/dashboard/licenses') . "\n\n";
        $message .= "Terima kasih atas kepercayaannya! 🙏✨";

        return $this->sendMessage($order->whatsapp_number ?? $order->customer_email, $message);
    }

    /**
     * Send payment expired notification to customer
     */
    public function sendPaymentExpiredNotification(Order $order): bool
    {
        $message = "Halo *{$order->customer_name}*,\n\n";
        $message .= "⏰ *Link Pembayaran Expired*\n\n";
        $message .= "Link pembayaran untuk *{$order->product->name}* (Order #{$order->id}) telah expired.\n\n";
        $message .= "📌 *Tindakan yang diperlukan:*\n";
        $message .= "Silakan hubungi admin kami untuk membuat link pembayaran baru atau melakukan order ulang.\n\n";
        $message .= "Contact Admin:\n";
        $message .= url('/contact') . "\n\n";
        $message .= "Mohon maaf atas ketidaknyamanannya. 🙏";

        return $this->sendMessage($order->whatsapp_number ?? $order->customer_email, $message);
    }

    /**
     * Send payment refunded notification to customer
     */
    public function sendPaymentRefundedNotification(Order $order): bool
    {
        $message = "Halo *{$order->customer_name}*,\n\n";
        $message .= "💰 *Status Refund*\n\n";
        $message .= "Pembayaran untuk *{$order->product->name}* (Order #{$order->id}) telah di-refund sebesar {$order->formatted_amount}.\n\n";
        $message .= "Dana akan kembali ke rekening/metode pembayaran Anda dalam 3-7 hari kerja.\n\n";
        $message .= "Jika ada pertanyaan, silakan hubungi admin kami.\n\n";
        $message .= "Terima kasih atas pengertiannya. 🙏";

        return $this->sendMessage($order->whatsapp_number ?? $order->customer_email, $message);
    }

    /**
     * Send order status update notification to customer
     */
    public function sendOrderStatusUpdateNotification(Order $order, string $status): bool
    {
        $statusMessages = [
            'pending' => '⏳ Pesanan Anda sedang menunggu konfirmasi.',
            'confirmed' => '✅ Pesanan Anda telah dikonfirmasi.',
            'processing' => '⚙️ Pesanan Anda sedang dalam proses.',
            'completed' => '🎉 Pesanan Anda telah selesai. Terima kasih!',
            'cancelled' => '❌ Pesanan Anda telah dibatalkan.',
        ];

        $message = "*Update Status Pesanan #{$order->id}*\n\n";
        $message .= "Halo *{$order->customer_name}*,\n\n";
        $message .= ($statusMessages[$status] ?? 'Status pesanan telah diupdate.') . "\n\n";
        $message .= "*Detail Pesanan:*\n";
        $message .= "Produk: {$order->product->name}\n";
        $message .= "Total: {$order->formatted_amount}\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->sendMessage($order->whatsapp_number ?? $order->customer_email, $message);
    }

    /**
     * Send new order notification to admin
     */
    public function sendAdminNewOrderNotification(Order $order): bool
    {
        $adminNumber = Setting::get('whatsapp_admin_number');
        
        if (!$adminNumber) {
            Log::warning('Admin WhatsApp number not configured');
            return false;
        }

        $message = "🔔 *ORDER BARU MASUK!*\n\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "📋 *Detail Order*\n";
        $message .= "• Order ID: #{$order->id}\n";
        $message .= "• Customer: *{$order->customer_name}*\n";
        $message .= "• Email: {$order->customer_email}\n";
        $message .= "• WhatsApp: " . ($order->whatsapp_number ?? 'Not provided') . "\n";
        $message .= "• Produk: *{$order->product->name}*\n";
        $message .= "• Total: *{$order->formatted_amount}*\n\n";
        $message .= "💳 *Status:*\n";
        $message .= "Menunggu pembayaran dari customer\n\n";
        $message .= "🔗 *Lihat Detail:*\n";
        $message .= url('/admin/orders/' . $order->id) . "\n\n";
        $message .= "_Notifikasi otomatis dari sistem CF7 WhatsApp Gateway_";

        return $this->sendMessage($adminNumber, $message);
    }

    /**
     * Send payment success notification to admin
     */
    public function sendAdminPaymentSuccessNotification(Order $order): bool
    {
        $adminNumber = Setting::get('whatsapp_admin_number');
        
        if (!$adminNumber) {
            Log::warning('Admin WhatsApp number not configured');
            return false;
        }

        $message = "💰 *PEMBAYARAN DITERIMA!*\n\n";
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "✅ *Order Telah Dibayar*\n\n";
        $message .= "📋 *Detail Order*\n";
        $message .= "• Order ID: #{$order->id}\n";
        $message .= "• Customer: *{$order->customer_name}*\n";
        $message .= "• Email: {$order->customer_email}\n";
        $message .= "• WhatsApp: " . ($order->whatsapp_number ?? 'Not provided') . "\n";
        $message .= "• Produk: *{$order->product->name}*\n";
        $message .= "• Total: *{$order->formatted_amount}*\n\n";
        $message .= "💳 *Pembayaran:*\n";
        $message .= "• Status: LUNAS ✅\n";
        $message .= "• Dibayar: " . $order->paid_at->format('d M Y, H:i') . "\n\n";
        $message .= "🎯 *Action Required:*\n";
        $message .= "• License telah digenerate otomatis\n";
        $message .= "• Customer sudah menerima notifikasi\n\n";
        $message .= "🔗 *Kelola Order:*\n";
        $message .= url('/admin/orders/' . $order->id) . "\n\n";
        $message .= "_Notifikasi otomatis dari sistem CF7 WhatsApp Gateway_";

        return $this->sendMessage($adminNumber, $message);
    }
}
