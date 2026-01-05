# WhatsApp Notification Integration - Payment Gateway

## 📱 Overview

WhatsApp notifications telah diintegrasikan dengan iPaymu payment gateway untuk memberikan update real-time kepada customer di setiap step pembayaran.

## 🔔 Notification Types

### 1. **Order Created** (Suruh Bayar)
**Trigger:** Saat customer membuat order baru
**Controller:** `UserOrderController@store()`
**Method:** `sendOrderCreatedNotification()`

**Message Content:**
- Order ID
- Product name
- Quantity
- Total amount
- Payment link
- Deadline reminder (24 jam)

**Example:**
```
Assalamu'alaikum Shofwan,

Terima kasih telah melakukan Pre-Order! 🎉

📦 Detail Pesanan
━━━━━━━━━━━━━━━━
• Order ID: #1
• Produk: Jam Bulan Sabit Angka Arab
• Jumlah: 1 pcs
• Total: Rp 200.000

💳 Langkah Selanjutnya:
Silakan lakukan pembayaran untuk mengamankan slot PO Anda.

Klik link berikut untuk melanjutkan pembayaran:
https://toko.mutekar.com/my/orders/1

⏰ Selesaikan pembayaran dalam 24 jam agar pesanan tidak dibatalkan otomatis.

Jazakumullah khairan! 🙏
```

---

### 2. **Payment Success** (Pembayaran Berhasil)
**Trigger:** Saat iPaymu callback dengan status = 1 (Berhasil)
**Controller:** `IPaymuCallbackController@callback()`
**Method:** `sendPaymentSuccessNotification()`

**Message Content:**
- Konfirmasi pembayaran diterima
- Total amount
- Timeline estimasi
- Link tracking order
- Next steps

**Example:**
```
Alhamdulillah Shofwan! 🎊

✅ PEMBAYARAN BERHASIL
━━━━━━━━━━━━━━━━

Pembayaran Anda untuk Jam Bulan Sabit Angka Arab telah kami terima dengan total Rp 200.000.

📋 Status Pesanan:
Pesanan Anda sedang kami proses. Anda akan menerima update melalui WhatsApp saat:
• Kuota PO terpenuhi
• Produksi dimulai
• Produk dalam pengiriman

🎯 Timeline Estimasi:
• Menunggu kuota: 7-14 hari
• Produksi: 7-10 hari
• Pengiriman: 2-5 hari

Track pesanan Anda:
https://toko.mutekar.com/my/orders/1

Jazakumullah khairan atas kepercayaannya! 🙏✨

Kami akan bekerja dengan sepenuh hati untuk produk terbaik Anda.
```

---

### 3. **Payment Expired** (Link Expired)
**Trigger:** Saat iPaymu callback dengan status = 7 atau -2 (Expired)
**Controller:** `IPaymuCallbackController@callback()`
**Method:** `sendPaymentExpiredNotification()`

**Message Content:**
- Info link pembayaran expired
- Instruksi contact admin
- Link kontak

**Example:**
```
Halo Shofwan,

⏰ Link Pembayaran Expired

Link pembayaran untuk Jam Bulan Sabit Angka Arab (Order #1) telah expired.

📌 Tindakan yang diperlukan:
Silakan hubungi admin kami untuk membuat link pembayaran baru atau melakukan order ulang.

Contact Admin:
https://toko.mutekar.com/kontak

Mohon maaf atas ketidaknyamanannya. 🙏
```

---

### 4. **Payment Refunded** (Refund)
**Trigger:** Saat iPaymu callback dengan status = 6 (Refund)
**Controller:** `IPaymuCallbackController@callback()`
**Method:** `sendPaymentRefundedNotification()`

**Message Content:**
- Info refund diproses
- Total amount yang direfund
- Estimasi waktu dana kembali

**Example:**
```
Halo Shofwan,

💰 Status Refund

Pembayaran untuk Jam Bulan Sabit Angka Arab (Order #1) telah di-refund sebesar Rp 200.000.

Dana akan kembali ke rekening/metode pembayaran Anda dalam 3-7 hari kerja.

Jika ada pertanyaan, silakan hubungi admin kami.

Terima kasih atas pengertiannya. 🙏
```

---

### 5. **Payment Failed** (Manual Trigger)
**Method:** `sendPaymentFailedNotification($order, $reason)`
**Note:** Bisa dipanggil manual dari admin panel jika diperlukan

**Message Content:**
- Info pembayaran gagal
- Reason (optional)
- Link untuk retry pembayaran

---

### 6. **Payment Reminder** (Manual Trigger)
**Method:** `sendPaymentReminderNotification($order)`
**Note:** Bisa dijadwalkan via cron job untuk pengingat otomatis

**Message Content:**
- Reminder untuk segera bayar
- Order details
- Payment link
- Urgency message

**Example:**
```
Halo Shofwan,

🔔 Pengingat Pembayaran

Kami menunggu pembayaran untuk:
• Order ID: #1
• Produk: Jam Bulan Sabit Angka Arab
• Total: Rp 200.000

Yuk selesaikan pembayaran sekarang:
https://toko.mutekar.com/my/orders/1

Slot PO terbatas, jangan sampai kehabisan! ⏰

Butuh bantuan? Chat admin kami.
```

---

### 7. **Order Confirmed** (Admin Action)
**Method:** `sendOrderConfirmedNotification($order)`
**Note:** Dipanggil saat admin konfirmasi order

**Message Content:**
- Konfirmasi pesanan
- Next steps info

---

## 🔄 Integration Flow

```
┌─────────────────────┐
│  Customer Action    │
└──────────┬──────────┘
           │
           ▼
    ┌──────────────┐
    │ Create Order │
    └──────┬───────┘
           │
           ▼
    ┌─────────────────────────┐
    │ OrderController@store() │
    └──────┬──────────────────┘
           │
           ├──→ Save to DB
           │
           └──→ Send WhatsApp: "Order Created"
                ✉️ Suruh bayar + payment link
           
           
    User clicks "Bayar Sekarang"
           │
           ▼
    Redirect to iPaymu
           │
           ▼
    User completes payment
           │
           ▼
    iPaymu sends callback
           │
           ▼
    ┌──────────────────────────────┐
    │ IPaymuCallbackController     │
    │ @callback()                  │
    └──────┬───────────────────────┘
           │
           ├──→ Update order status
           │
           └──→ Send WhatsApp based on status:
                • Status 1  → "Payment Success" ✅
                • Status 6  → "Payment Refunded" 💰
                • Status 7  → "Payment Expired" ⏰
                • Status -2 → "Payment Expired" ⏰
```

---

## 💻 Code Implementation

### OrderController (Order Created)
```php
// After order creation
try {
    $whatsapp = app(\App\Services\WhatsAppService::class);
    $whatsapp->sendOrderCreatedNotification($order);
    
    \Log::info('WhatsApp notification sent for new order', [
        'order_id' => $order->id
    ]);
} catch (\Exception $e) {
    \Log::error('Failed to send WhatsApp notification', [
        'order_id' => $order->id,
        'error' => $e->getMessage()
    ]);
}
```

### IPaymuCallbackController (Payment Status)
```php
// After payment status check
if ($statusInt == 1) {
    $order->payment_status = 'paid';
    $order->paid_at = now();
    
    // Send WhatsApp notification
    $this->sendPaymentNotification($order, 'success');
}

// Helper method
private function sendPaymentNotification($order, $type)
{
    try {
        $whatsapp = app(\App\Services\WhatsAppService::class);
        
        switch ($type) {
            case 'success':
                $whatsapp->sendPaymentSuccessNotification($order);
                break;
            case 'refunded':
                $whatsapp->sendPaymentRefundedNotification($order);
                break;
            case 'expired':
                $whatsapp->sendPaymentExpiredNotification($order);
                break;
        }
    } catch (\Exception $e) {
        \Log::error('Failed to send WhatsApp notification', [
            'error' => $e->getMessage()
        ]);
    }
}
```

---

## 📊 Notification Log

Setiap notifikasi yang dikirim akan di-log dengan informasi:
- Order ID
- Notification type/stage
- Message content
- Status (sent/failed)
- Timestamp

Log location: `storage/logs/laravel.log`

---

## ⚙️ Configuration

WhatsApp service menggunakan konfigurasi dari:
1. Database settings (prioritas utama)
2. Config file `config/services.php`
3. Environment variables `.env`

**Required Settings:**
- `whatsapp_api_key` - API Key untuk WhatsApp gateway
- `whatsapp_sender` - Sender ID/number
- `whatsapp_endpoint` - API endpoint URL

---

## 🧪 Testing

### Manual Test
```php
// Test payment success notification
$order = Order::find(1);
$whatsapp = new WhatsAppService();
$result = $whatsapp->sendPaymentSuccessNotification($order);

dd($result);
```

### Via Artisan (jika ada command)
```bash
php artisan whatsapp:test-payment-notification 1
```

---

## 🔧 Troubleshooting

### Notification not sent?
1. Check WhatsApp API credentials in admin settings
2. Check log file: `storage/logs/laravel.log`
3. Verify phone number format (must be 62xxx)
4. Test API connection in admin panel

### Wrong message format?
1. Check `WhatsAppService::sendPaymentSuccessNotification()`
2. Verify order has `product` relationship loaded
3. Check if customer_name and customer_phone exist

### Rate limiting?
WhatsApp service includes 1-second delay between bulk messages to avoid rate limits.

---

## 📈 Future Enhancements

### Planned Features:
1. **Scheduled Reminders**
   - Auto-send reminder setelah 12 jam jika belum bayar
   - Auto-send reminder sebelum payment link expired

2. **Production Updates**
   - Notif saat kuota terpenuhi
   - Notif saat produksi dimulai
   - Notif saat QC selesai
   - Notif saat packing

3. **Shipping Updates**
   - Notif saat barang dikirim + resi
   - Notif saat barang dalam perjalanan
   - Notif saat barang tiba

4. **Admin Notifications**
   - Notif ke admin saat ada order baru
   - Notif saat pembayaran diterima
   - Daily summary

---

## 🎯 Message Template Best Practices

✅ **DO:**
- Use first name only (friendly)
- Include order ID for reference
- Use emojis for better engagement
- Provide direct action link
- Keep message concise
- Use proper formatting (bold for important info)

❌ **DON'T:**
- Send too many notifications
- Use full name (too formal)
- Include sensitive payment details
- Make message too long
- Forget to include tracking link

---

## 📝 Status Mapping

| iPaymu Status | Payment Status | WhatsApp Notification |
|--------------|----------------|----------------------|
| 1 | paid | ✅ Payment Success |
| 6 | refunded | 💰 Payment Refunded |
| 7 | expired | ⏰ Payment Expired |
| -2 | expired | ⏰ Payment Expired |
| 0 | pending | (No notification) |
| 2 | cancelled | ❌ Payment Failed |

---

## ✅ Integration Checklist

- [x] WhatsApp Service created
- [x] Payment notification methods added
- [x] Integration in OrderController (order created)
- [x] Integration in IPaymuCallbackController (payment status)
- [x] Error handling implemented
- [x] Logging implemented
- [x] Phone number formatting
- [ ] Scheduled reminders (future)
- [ ] Admin panel to view notification logs (future)
- [ ] Resend notification feature (future)

---

**Last Updated:** 2025-12-23 12:33 WIB  
**Status:** ✅ Production Ready
