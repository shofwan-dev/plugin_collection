# Dynamic Site Name Implementation

## 📝 Implementasi Nama Website Dinamis

Semua notifikasi WhatsApp dan referensi nama website sekarang mengambil data dinamis dari **Settings → Website → Nama Website**.

---

## 🎯 File yang Telah Diupdate

### **1. WhatsAppService**
📁 `app/Services/WhatsAppService.php`

**Method Baru:**
```php
private function getSiteName(): string
{
    return Setting::getValue('site_name', 'website') ?: 'PO Kaligrafi';
}
```

**Perubahan:**
- ✅ Test connection message
- ✅ Admin notification - order baru
- ✅ Admin notification - pembayaran berhasil

**Sebelum:**
```php
$message .= "_Notifikasi otomatis dari sistem PO Kaligrafi_";
```

**Sesudah:**
```php
$message .= "_Notifikasi otomatis dari sistem " . $this->getSiteName() . "_";
```

---

### **2. ProductionManagerController**
📁 `app/Http/Controllers/ProductionManagerController.php`

**Perubahan:**
- ✅ Order status update notification

**Sebelum:**
```php
$message .= "Terima kasih telah memesan di PO Kaligrafi Lampu.";
```

**Sesudah:**
```php
$siteName = \App\Models\Setting::getValue('site_name', 'website') ?: 'PO Kaligrafi';
$message .= "Terima kasih telah memesan di " . $siteName . ".";
```

---

### **3. HomeController**
📁 `app/Http/Controllers/HomeController.php`

**Perubahan:**
- ✅ About page title

**Sebelum:**
```php
'title' => 'Tentang PO Kaligrafi Lampu',
```

**Sesudah:**
```php
$siteName = \App\Models\Setting::getValue('site_name', 'website') ?: 'PO Kaligrafi';
'title' => 'Tentang ' . $siteName,
```

---

## 📨 Notifikasi yang Menggunakan Nama Dinamis

### **1. WhatsApp - Order Baru ke Admin**
```
🔔 *ORDER BARU MASUK!*
...
_Notifikasi otomatis dari sistem {NAMA_WEBSITE}_
```

### **2. WhatsApp - Pembayaran Berhasil ke Admin**
```
💰 *PEMBAYARAN DITERIMA!*
...
_Notifikasi otomatis dari sistem {NAMA_WEBSITE}_
```

### **3. WhatsApp - Update Status Order**
```
Assalamu'alaikum Ahmad,

Pesanan Anda sedang diproses
...
Terima kasih telah memesan di {NAMA_WEBSITE}.
```

### **4. WhatsApp - Test Connection**
```
Test koneksi API WhatsApp {NAMA_WEBSITE}
```

---

## ⚙️ Cara Mengubah Nama Website

### **Step 1: Akses Admin Settings**
```
Login sebagai Admin
→ Settings
→ Website Settings
```

### **Step 2: Edit Nama Website**
```
Field: "Nama Website"
Contoh: 
- PO Kaligrafi Lampu
- Kaligrafi Nusantara
- Islamic Art Gallery
- dll.
```

### **Step 3: Simpan**
```
Klik "Simpan Perubahan"
```

### **Step 4: Verifikasi**
```
Buat order baru atau test notifikasi
→ Nama website di notifikasi akan berubah otomatis
```

---

## 🔍 Lokasi Penggunaan Nama Website

### **Notifikasi WhatsApp:**
| Jenis Notifikasi | Lokasi | Status |
|------------------|--------|--------|
| Test Connection | WhatsAppService | ✅ Dinamis |
| Order Baru (Admin) | WhatsAppService | ✅ Dinamis |
| Pembayaran Berhasil (Admin) | WhatsAppService | ✅ Dinamis |
| Update Status Order | ProductionManagerController | ✅ Dinamis |

### **Halaman Web:**
| Halaman | Lokasi | Status |
|---------|--------|--------|
| About Title | HomeController | ✅ Dinamis |
| Page Title | Layouts | ✅ Sudah dinamis (sebelumnya) |
| Footer | Partials | ✅ Sudah dinamis (sebelumnya) |

---

## 📋 Fallback Value

Jika nama website tidak diisi di settings, sistem akan menggunakan fallback:

```php
Setting::getValue('site_name', 'website') ?: 'PO Kaligrafi'
```

**Default:** `PO Kaligrafi`

---

## 🧪 Testing

### **Test 1: Update Nama Website**
```
1. Login sebagai admin
2. Settings → Website
3. Ubah "Nama Website" menjadi "Kaligrafi Nusantara"
4. Simpan
```

### **Test 2: Buat Order Baru**
```
1. Login sebagai customer
2. Buat order baru
3. Cek WhatsApp admin
4. Notifikasi harus menampilkan "Kaligrafi Nusantara"
```

### **Test 3: Simulasi Pembayaran**
```
1. Simulasi callback pembayaran berhasil
2. Cek WhatsApp admin
3. Notifikasi harus menampilkan nama website yang baru
```

### **Test 4: Cek Halaman About**
```
1. Buka /about
2. Title harus "Tentang Kaligrafi Nusantara"
```

---

## 💡 Best Practices

### **Nama Website yang Baik:**
```
✅ PO Kaligrafi Lampu
✅ Kaligrafi Nusantara
✅ Islamic Art Gallery
✅ Cahaya Kaligrafi

❌ PO Kaligrafi Lampu - Official Store (terlalu panjang)
❌ www.pokaligrafi.com (jangan pakai URL)
❌ PO KALIGRAFI (jangan semua kapital)
```

### **Panjang Ideal:**
- **Minimum:** 2 kata
- **Maksimum:** 4 kata
- **Karakter:** 10-30 karakter

---

## 🔧 Troubleshooting

### **Nama Website Tidak Berubah di Notifikasi?**

**Check 1: Sudah Disimpan?**
```
Admin → Settings → Website
Pastikan sudah klik "Simpan Perubahan"
```

**Check 2: Cek Database**
```sql
SELECT `key`, `value`, `group` 
FROM settings 
WHERE `key` = 'site_name' 
AND `group` = 'website';
```

**Check 3: Clear Cache**
```bash
php artisan cache:clear
php artisan config:clear
```

**Check 4: Test Lagi**
```
Buat order baru atau simulasi callback
Cek notifikasi WhatsApp
```

---

## 📊 Summary

### **Total File Diupdate:** 3
- ✅ WhatsAppService.php
- ✅ ProductionManagerController.php
- ✅ HomeController.php

### **Total Notifikasi Dinamis:** 4
- ✅ Test Connection
- ✅ Order Baru (Admin)
- ✅ Pembayaran Berhasil (Admin)
- ✅ Update Status Order

### **Fallback:** `PO Kaligrafi`

---

## 🚀 Future Enhancements

### **1. Email Notifications**
Jika ada email notifications, tambahkan:
```php
$siteName = Setting::getValue('site_name', 'website') ?: 'PO Kaligrafi';
```

### **2. SMS Notifications**
Sama seperti email, gunakan dynamic site name.

### **3. Push Notifications**
Untuk mobile app (future), gunakan dynamic site name.

### **4. Invoice/Receipt**
```php
$siteName = Setting::getValue('site_name', 'website') ?: 'PO Kaligrafi';
// Use in invoice header
```

---

**Created:** 2026-01-03  
**Feature:** Dynamic Site Name  
**Status:** ✅ Production Ready
