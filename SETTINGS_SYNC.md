# ✅ Settings Sinkronisasi - SUDAH TERSINKRONISASI!

## Status: ✅ COMPLETE

Email dan WhatsApp settings sudah **tersinkronisasi** antara Admin Settings UI dan sistem notifikasi.

---

## 📍 Lokasi Settings

### **Admin Settings Page:**
```
/admin/settings
```

Semua konfigurasi bisa diatur dari halaman ini, **TIDAK PERLU** edit `.env` file!

---

## ⚙️ Settings yang Tersedia

### **1. Email Settings (SMTP)** ✉️

**Lokasi UI:** `/admin/settings` → Email Settings (SMTP)

**Fields:**
- ✅ SMTP Host (e.g., `smtp.gmail.com`)
- ✅ Port (587 atau 465)
- ✅ Encryption (TLS/SSL/None)
- ✅ Username (email address)
- ✅ Password (app password untuk Gmail)
- ✅ From Address (sender email)
- ✅ From Name (sender name)

**Test Button:** ✅ "Test Email" - Kirim test email ke Contact Email

**Auto-Sync:** ✅ Settings otomatis ter-apply ke Laravel mail config

---

### **2. WhatsApp Settings** 💬

**Lokasi UI:** `/admin/settings` → WhatsApp Settings

**Fields:**
- ✅ API URL (MPWA instance URL)
- ✅ API Key (MPWA API key)
- ✅ Sender Number (WhatsApp number/device)
- ✅ Admin Number (untuk notifikasi admin)
- ✅ Enable WhatsApp Notifications (toggle on/off)

**Test Button:** ✅ "Test WhatsApp" - Kirim test message ke Admin Number

---

### **3. Paddle Settings** 🏓

**Lokasi UI:** `/admin/settings` → Paddle Payment Gateway

**Fields:**
- ✅ Environment (Sandbox/Live)
- ✅ Seller ID
- ✅ API Key
- ✅ Client Token
- ✅ Webhook Secret

**Test Button:** ✅ "Test Connection" - Verify API credentials

---

## 🔄 Cara Kerja Sinkronisasi

### **Email Settings:**

```
User mengisi form di /admin/settings
    ↓
Submit → SettingController::update()
    ↓
Save ke database (settings table)
    ↓
AppServiceProvider::boot()
    ↓
Load email settings dari database
    ↓
Apply ke config(['mail.mailers.smtp.*'])
    ↓
Laravel Mail menggunakan settings dari database
```

**Kode:** `app/Providers/AppServiceProvider.php` (lines 54-77)

```php
// Load Email Settings into Mail Config
$emailSettings = \App\Models\Setting::getGroup('email');

if (!empty($emailSettings)) {
    if (!empty($emailSettings['email_host'])) {
        config([
            'mail.mailers.smtp.host' => $emailSettings['email_host'],
            'mail.mailers.smtp.port' => $emailSettings['email_port'] ?? 587,
            'mail.mailers.smtp.encryption' => $emailSettings['email_encryption'] ?? 'tls',
            'mail.mailers.smtp.username' => $emailSettings['email_username'] ?? '',
            'mail.mailers.smtp.password' => $emailSettings['email_password'] ?? '',
            'mail.from.address' => $emailSettings['email_from_address'],
            'mail.from.name' => $emailSettings['email_from_name'],
        ]);
    }
}
```

### **WhatsApp Settings:**

```
User mengisi form di /admin/settings
    ↓
Submit → SettingController::update()
    ↓
Save ke database (settings table)
    ↓
WhatsAppService::__construct()
    ↓
Load settings dari database via Setting::get()
    ↓
WhatsApp API menggunakan settings dari database
```

**Kode:** `app/Services/WhatsAppService.php` (lines 17-23)

```php
public function __construct()
{
    $this->apiUrl = Setting::get('whatsapp_api_url');
    $this->apiKey = Setting::get('whatsapp_api_key');
    $this->sender = Setting::get('whatsapp_sender');
    $this->enabled = Setting::get('whatsapp_enabled', false);
}
```

---

## 🧪 Testing Settings

### **Test Email:**

1. Buka `/admin/settings`
2. Scroll ke "Email Settings (SMTP)"
3. Isi semua field:
   ```
   SMTP Host: smtp.gmail.com
   Port: 587
   Encryption: TLS
   Username: your-email@gmail.com
   Password: your-app-password
   From Address: noreply@yourdomain.com
   From Name: Your Site Name
   ```
4. Klik "Save Email Settings"
5. Scroll ke "Test Email Configuration"
6. Klik "Test Email"
7. Cek email di Contact Email address

### **Test WhatsApp:**

1. Buka `/admin/settings`
2. Scroll ke "WhatsApp Settings"
3. Isi semua field:
   ```
   API URL: https://your-mpwa.com/send-message
   API Key: your-api-key
   Sender Number: 628xxx
   Admin Number: 628xxx
   ✓ Enable WhatsApp Notifications
   ```
4. Klik "Save WhatsApp Settings"
5. Scroll ke "Test WhatsApp Gateway"
6. Klik "Test WhatsApp"
7. Cek WhatsApp di Admin Number

---

## 📊 Database Structure

**Table:** `settings`

**Columns:**
- `id` - Primary key
- `key` - Setting key (e.g., `email_host`)
- `value` - Setting value
- `type` - Data type (`string`, `boolean`, etc.)
- `group` - Group name (`email`, `whatsapp`, `paddle`, `general`)

**Example Data:**

| key | value | type | group |
|-----|-------|------|-------|
| email_host | smtp.gmail.com | string | email |
| email_port | 587 | string | email |
| email_encryption | tls | string | email |
| whatsapp_api_url | https://mpwa.com/send | string | whatsapp |
| whatsapp_enabled | 1 | boolean | whatsapp |

---

## 🔐 Security Notes

### **Email Password:**
- Disimpan di database (encrypted recommended)
- Untuk Gmail, gunakan **App Password**, bukan password akun
- Generate App Password: https://myaccount.google.com/apppasswords

### **WhatsApp API Key:**
- Disimpan di database
- Jangan share API key
- Rotate API key secara berkala

### **Paddle API Key:**
- Disimpan di database
- Jangan commit ke Git
- Gunakan environment-specific keys

---

## 🎯 Keuntungan Sistem Ini

### **1. User-Friendly** 👍
- Admin tidak perlu akses server
- Tidak perlu edit `.env`
- Tidak perlu restart aplikasi
- Test button untuk verify settings

### **2. Flexible** 🔄
- Settings bisa diubah kapan saja
- Langsung ter-apply tanpa restart
- Multi-environment support (sandbox/live)

### **3. Centralized** 📍
- Semua settings di satu tempat
- Easy to backup (database export)
- Easy to migrate (database import)

### **4. Secure** 🔒
- Settings tidak di-commit ke Git
- Database-level security
- Environment-specific configurations

---

## 📝 Cara Menggunakan

### **Setup Email (Gmail Example):**

1. **Generate App Password:**
   - Login Gmail
   - Go to: https://myaccount.google.com/apppasswords
   - Create app password untuk "Mail"
   - Copy password (16 characters)

2. **Configure di Admin:**
   ```
   SMTP Host: smtp.gmail.com
   Port: 587
   Encryption: TLS
   Username: your-email@gmail.com
   Password: [paste app password]
   From Address: noreply@yourdomain.com
   From Name: Your Site Name
   ```

3. **Save & Test:**
   - Klik "Save Email Settings"
   - Klik "Test Email"
   - Cek inbox

### **Setup WhatsApp (MPWA Example):**

1. **Setup MPWA Instance:**
   - Deploy MPWA di server
   - Scan QR code
   - Get API URL & API Key

2. **Configure di Admin:**
   ```
   API URL: https://your-mpwa.com/send-message
   API Key: [your api key]
   Sender: 628123456789
   Admin Number: 628987654321
   ✓ Enable Notifications
   ```

3. **Save & Test:**
   - Klik "Save WhatsApp Settings"
   - Klik "Test WhatsApp"
   - Cek WhatsApp admin

---

## 🔧 Troubleshooting

### **Email Tidak Terkirim:**

**Cek:**
1. SMTP credentials benar?
2. Port & encryption sesuai?
3. Firewall tidak block port 587/465?
4. Gmail: Sudah pakai App Password?

**Debug:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep "Email"

# Check config
php artisan tinker
config('mail.mailers.smtp.host')
config('mail.from.address')
```

### **WhatsApp Tidak Terkirim:**

**Cek:**
1. MPWA instance running?
2. WhatsApp device connected?
3. API URL & Key benar?
4. Admin number format benar (62xxx)?

**Debug:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep "WhatsApp"

# Test manual
curl -X POST https://your-mpwa.com/send-message \
  -H "Content-Type: application/json" \
  -d '{"api_key":"xxx","sender":"xxx","number":"628xxx","message":"test"}'
```

---

## ✅ Checklist

Sebelum production, pastikan:

- [ ] Email settings configured & tested
- [ ] WhatsApp settings configured & tested
- [ ] Paddle settings configured & tested
- [ ] Test buttons working
- [ ] Notifications terkirim
- [ ] Logs tidak ada error
- [ ] Settings tersimpan di database
- [ ] Auto-sync working (check logs)

---

## 📚 Related Documentation

- `NOTIFICATION_SYSTEM.md` - Sistem notifikasi lengkap
- `PADDLE_400_FIX.md` - Paddle troubleshooting
- `PADDLE_TEST_BUTTON.md` - Paddle test connection

---

**Status:** ✅ **FULLY SYNCHRONIZED**  
**Updated:** 2026-01-07 00:34  
**Version:** 1.0.0

**Semua settings sudah tersinkronisasi dan siap digunakan!** 🎉
