# Sistem Notifikasi WhatsApp untuk Status Pembayaran

## 📋 Overview

Sistem ini mengimplementasikan notifikasi WhatsApp otomatis untuk berbagai status pembayaran dari Paddle tanpa mengganggu flow Paddle yang sudah ada. Menggunakan Laravel Events & Listeners untuk memisahkan logic notifikasi dari proses pembayaran utama.

## 🏗️ Arsitektur

### 1. **Events** (`app/Events/`)
Event-event yang di-trigger saat status pembayaran berubah:
- `PaymentCompleted` - Pembayaran berhasil
- `PaymentFailed` - Pembayaran gagal
- `PaymentPending` - Pembayaran sedang diproses
- `PaymentRefunded` - Pembayaran di-refund

### 2. **Listeners** (`app/Listeners/`)
Listener yang menangani events dan mengirim notifikasi WhatsApp:
- `SendPaymentCompletedNotification` - Kirim notif saat pembayaran berhasil
- `SendPaymentFailedNotification` - Kirim notif saat pembayaran gagal
- `SendPaymentPendingNotification` - Kirim notif saat pembayaran pending
- `SendPaymentRefundedNotification` - Kirim notif saat pembayaran di-refund

### 3. **Webhook Handler** (`app/Http/Controllers/WebhookController.php`)
Menangani webhook dari Paddle:
- `paddle()` - Main handler untuk Paddle webhooks
- `handlePaddlePaymentSuccess()` - Handle pembayaran sukses
- `handlePaddlePaymentFailed()` - Handle pembayaran gagal
- `handlePaddlePaymentPending()` - Handle pembayaran pending
- `handlePaddlePaymentRefunded()` - Handle pembayaran refund

### 4. **WhatsApp Service** (`app/Services/WhatsAppService.php`)
Service yang sudah ada untuk mengirim pesan WhatsApp menggunakan MPWA API.

## 🔄 Flow Diagram

```
Paddle Payment → Webhook → WebhookController → Event Dispatched
                                                      ↓
                                              EventServiceProvider
                                                      ↓
                                                  Listener
                                                      ↓
                                              WhatsAppService
                                                      ↓
                                            Customer & Admin
```

## ⚙️ Konfigurasi

### 1. Webhook URL
Tambahkan webhook URL di Paddle Dashboard:
```
https://yourdomain.com/webhook/paddle
```

### 2. Event Types yang Didukung
Configure di Paddle Dashboard untuk mengirim event berikut:
- `transaction.completed` atau `payment_succeeded`
- `transaction.payment_failed` atau `payment_failed`
- `transaction.created` atau `payment_created`
- `transaction.refunded` atau `payment_refunded`

### 3. WhatsApp Configuration
Pastikan WhatsApp service sudah dikonfigurasi di Admin Settings:
- WhatsApp API URL
- WhatsApp API Key
- WhatsApp Sender Number
- WhatsApp Admin Number (untuk notifikasi admin)
- WhatsApp Enabled: Yes

## 📱 Format Notifikasi

### Pembayaran Berhasil (Customer)
```
*Alhamdulillah! Pembayaran Berhasil* 🎊

✅ *PEMBAYARAN DITERIMA*
━━━━━━━━━━━━━━━━

Pembayaran Anda untuk *[Product Name]* telah kami terima dengan total [Amount].

📋 *License Key Anda:*
`XXXX-XXXX-XXXX-XXXX`

🎯 *Langkah Selanjutnya:*
1. Download plugin dari dashboard
2. Install di WordPress Anda
3. Aktivasi dengan license key di atas

Track license Anda:
[Dashboard URL]

Terima kasih atas kepercayaannya! 🙏✨
```

### Pembayaran Gagal (Customer)
```
*Pembayaran Gagal* ❌

Halo *[Customer Name]*,

Pembayaran untuk *[Product Name]* (Order #[ID]) gagal diproses.

📌 *Alasan:*
[Failure Reason]

💡 *Apa yang harus dilakukan?*
1. Periksa saldo atau limit kartu Anda
2. Coba metode pembayaran lain
3. Hubungi bank Anda jika masalah berlanjut

🔗 Coba lagi di:
[Order URL]

Butuh bantuan? Hubungi admin kami 🙏
```

### Pembayaran Pending (Customer)
```
*Pembayaran Sedang Diproses* ⏳

Halo *[Customer Name]*,

Pembayaran Anda untuk *[Product Name]* sedang dalam proses verifikasi.

📋 *Detail Order:*
• Order ID: #[ID]
• Total: [Amount]

⏰ *Estimasi:*
Proses verifikasi biasanya memakan waktu 5-10 menit.

Kami akan mengirimkan notifikasi segera setelah pembayaran dikonfirmasi.

Track status di:
[Order URL]

Terima kasih atas kesabaran Anda! 🙏
```

### Pembayaran Berhasil (Admin)
```
💰 *PEMBAYARAN DITERIMA!*

━━━━━━━━━━━━━━━━
✅ *Order Telah Dibayar*

📋 *Detail Order*
• Order ID: #[ID]
• Customer: *[Name]*
• Email: [Email]
• WhatsApp: [Number]
• Produk: *[Product]*
• Total: *[Amount]*

💳 *Pembayaran:*
• Status: LUNAS ✅
• Dibayar: [Date Time]

🎯 *Action Required:*
• License telah digenerate otomatis
• Customer sudah menerima notifikasi

🔗 *Kelola Order:*
[Admin Order URL]

_Notifikasi otomatis dari sistem CF7 WhatsApp Gateway_
```

## 🧪 Testing

### 1. Test Menggunakan Artisan Command (Paling Mudah)
```bash
# List orders yang ada
php artisan tinker
>>> App\Models\Order::all()->pluck('id', 'customer_name')

# Test payment completed notification
php artisan test:payment-notification {order_id} completed

# Test payment failed notification
php artisan test:payment-notification {order_id} failed

# Test payment pending notification
php artisan test:payment-notification {order_id} pending

# Test payment refunded notification
php artisan test:payment-notification {order_id} refunded

# Contoh:
php artisan test:payment-notification 1 completed
```

### 2. Test Manual
Gunakan Paddle Sandbox untuk test:
1. Lakukan test checkout menggunakan Paddle test card
2. Periksa log di `storage/logs/laravel.log`
3. Verifikasi notifikasi WhatsApp terkirim

### 3. Test Webhook
Gunakan Paddle Webhook Simulator:
1. Login ke Paddle Dashboard
2. Navigate ke Developer Tools > Webhooks
3. Pilih event type (e.g., transaction.completed)
4. Kirim test webhook
5. Periksa response dan log

### 4. Monitoring
Log file yang perlu dimonitor:
```bash
tail -f storage/logs/laravel.log | grep -i "paddle\|whatsapp\|payment"
```

## 🔍 Troubleshooting

### Notifikasi Tidak Terkirim

**Cek 1: Konfigurasi WhatsApp**
```bash
# Periksa settings
php artisan tinker
>>> App\Models\Setting::get('whatsapp_enabled')
>>> App\Models\Setting::get('whatsapp_api_url')
>>> App\Models\Setting::get('whatsapp_sender')
```

**Cek 2: Event Listener**
```bash
# List registered listeners
php artisan event:list
```

**Cek 3: Log**
```bash
# Periksa error di log
tail -n 100 storage/logs/laravel.log | grep ERROR
```

### Webhook Tidak Diterima

**Cek 1: URL Accessible**
```bash
# Test webhook URL dari external
curl -X POST https://yourdomain.com/webhook/paddle \
  -H "Content-Type: application/json" \
  -d '{"alert_name":"test"}'
```

**Cek 2: CSRF Exception**
Pastikan `webhook/paddle` ada di CSRF exception di `bootstrap/app.php`

**Cek 3: Paddle Dashboard**
- Verifikasi webhook URL di Paddle Dashboard
- Periksa webhook delivery logs di Paddle
- Pastikan webhook active

## 📊 Monitoring & Logs

### Event Logs
Setiap event akan mencatat:
```
[timestamp] Processing PaymentCompleted event for WhatsApp notification
    order_id: 123
    customer_name: John Doe
```

### Webhook Logs
```
[timestamp] Paddle Webhook Received
    payload: {...}
    headers: {...}

[timestamp] Processing Paddle webhook
    event_type: transaction.completed

[timestamp] Paddle payment success processed
    order_id: 123
    transaction_id: txn_xxx
```

### WhatsApp Service Logs
```
[timestamp] Sending WhatsApp message
    to: 6281234567890
    is_group: false
    sender: 628123456789
    api_url: https://api.example.com/send

[timestamp] WhatsApp message sent successfully
    to: 6281234567890
    response: {...}
```

## 🚀 Deployment

### 1. Update Production
```bash
# Upload semua file yang dibuat
git add .
git commit -m "Add WhatsApp notification system for payment status"
git push origin main
```

### 2. Deploy ke Server
```bash
# SSH ke server
ssh user@server

# Navigate ke project
cd /path/to/project

# Pull changes
git pull origin main

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan event:clear
```

### 3. Verify
```bash
# Test event listener
php artisan event:list | grep Payment

# Output should show:
# App\Events\PaymentCompleted
#   App\Listeners\SendPaymentCompletedNotification
# App\Events\PaymentFailed
#   App\Listeners\SendPaymentFailedNotification
# ...
```

## 🔐 Security

1. **Webhook Verification**: Paddle webhooks diverifikasi otomatis oleh Laravel Cashier Paddle
2. **CSRF Protection**: Webhook endpoint dikecualikan dari CSRF protection
3. **Error Handling**: Semua error di-catch dan di-log, webhook tetap return 200 OK
4. **Non-Blocking**: Event dispatching tidak memblokir proses Paddle

## 📝 Notes

- Sistem ini **tidak mengganggu** flow Paddle yang sudah ada
- Notifikasi dikirim **secara asynchronous** menggunakan event system
- Jika WhatsApp service gagal, error akan di-log tapi proses pembayaran tetap berjalan
- Mendukung multiple payment status (completed, failed, pending, refunded)
- Admin akan menerima notifikasi untuk setiap pembayaran berhasil

## 🔗 Related Files

- Events: `app/Events/Payment*.php`
- Listeners: `app/Listeners/SendPayment*Notification.php`
- Webhook Controller: `app/Http/Controllers/WebhookController.php`
- Event Service Provider: `app/Providers/EventServiceProvider.php`
- WhatsApp Service: `app/Services/WhatsAppService.php`
- Routes: `routes/web.php`
- Bootstrap: `bootstrap/app.php`
