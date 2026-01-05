# Troubleshooting: WhatsApp Notification Not Received

## 🐛 Masalah yang Ditemukan

### 1. ❌ NotificationLog Model - Mass Assignment Error
**Error:**
```
Add [order_id] to fillable property to allow mass assignment on [App\Models\NotificationLog]
```

**Penyebab:**
Model `NotificationLog` tidak memiliki `$fillable` property

**✅ FIXED:**
- Menambahkan `$fillable` property dengan semua field yang diperlukan
- File: `app/Models/NotificationLog.php`

---

### 2. ❌ WhatsApp Number Format Invalid
**Error:**
```
WhatsApp API Response {"to":"120363166537946168","status":400,"response":{"status":false,"msg":"Failed to send message!"}}
```

**Penyebab:**
Nomor `120363166537946168` terlalu panjang (18 digit)
- Nomor Indonesia valid: 10-15 digit (dengan kode 62)
- Contoh valid: `6281234567890` (13 digit)

**✅ FIXED:**
- Menambahkan validasi panjang maksimal (15 digit)
- Menambahkan logging untuk debug
- File: `app/Services/WhatsAppService.php`

---

## 🔍 Cara Cek Nomor WhatsApp di Database

### Via SQL:
```sql
SELECT `key`, `value`, `group` 
FROM settings 
WHERE `key` IN ('whatsapp', 'whatsapp_notification') 
AND `group` = 'website';
```

### Expected Result:
```
| key                    | value          | group   |
|------------------------|----------------|---------|
| whatsapp               | 6281234567890  | website |
| whatsapp_notification  | 6281234567890  | website |
```

---

## ⚙️ Langkah Perbaikan

### Step 1: Cek Nomor di Admin Settings
```
1. Login sebagai Admin
2. Buka: Settings → Website Settings
3. Cek field "WhatsApp Penerima Notifikasi"
4. Pastikan format: 6281234567890 (tanpa spasi, tanda hubung, atau karakter lain)
```

### Step 2: Format Nomor yang Benar

#### ✅ Format BENAR:
```
6281234567890
081234567890
81234567890
```

#### ❌ Format SALAH:
```
+62 812-3456-7890     (ada spasi dan tanda hubung)
62-812-3456-7890      (ada tanda hubung)
(62) 812 3456 7890    (ada kurung dan spasi)
120363166537946168    (terlalu panjang)
```

### Step 3: Update Nomor di Admin
```
1. Hapus nomor yang salah
2. Isi dengan nomor yang benar (hanya angka)
3. Klik "Simpan Perubahan"
```

### Step 4: Test Lagi
```
1. Buat order baru
2. Cek log: tail -f storage/logs/laravel.log
3. Cek WhatsApp admin
```

---

## 📋 Validasi Nomor Otomatis

Sistem sekarang akan:

### ✅ Validasi yang Ditambahkan:
1. **Hapus karakter non-digit** (spasi, tanda hubung, dll)
2. **Cek panjang maksimal** (max 15 digit)
3. **Cek panjang minimal** (min 10 digit)
4. **Format otomatis** (tambah 62 jika perlu)
5. **Logging** untuk debugging

### 🔍 Log yang Akan Muncul:
```
[2026-01-03 09:20:00] local.INFO: Phone number formatted 
{
  "formatted": "6281234567890",
  "length": 13
}
```

---

## 🧪 Testing

### Test 1: Cek Format Nomor
```php
// Di tinker atau test
php artisan tinker

$whatsapp = app(\App\Services\WhatsAppService::class);
$formatted = $whatsapp->formatPhoneNumber('081234567890');
echo $formatted; // Should output: 6281234567890
```

### Test 2: Buat Order Baru
```
1. Login sebagai user
2. Create new order
3. Submit
4. Cek log untuk melihat nomor yang diformat
```

### Test 3: Cek Log
```bash
# Cek error
tail -100 storage/logs/laravel.log | grep -i error

# Cek phone formatting
tail -100 storage/logs/laravel.log | grep "Phone number"

# Cek WhatsApp API response
tail -100 storage/logs/laravel.log | grep "WhatsApp API"
```

---

## 🔧 Manual Fix (Jika Perlu)

### Jika Nomor di Database Salah:

#### Via SQL:
```sql
-- Cek nomor saat ini
SELECT * FROM settings WHERE `key` = 'whatsapp_notification';

-- Update dengan nomor yang benar
UPDATE settings 
SET `value` = '6281234567890' 
WHERE `key` = 'whatsapp_notification' 
AND `group` = 'website';
```

#### Via Admin Panel:
```
Settings → Website → WhatsApp Penerima Notifikasi
Isi: 6281234567890
Simpan
```

---

## 📊 Checklist Troubleshooting

- [ ] NotificationLog model sudah ada $fillable ✅ FIXED
- [ ] Nomor WhatsApp format benar (hanya angka)
- [ ] Panjang nomor 10-15 digit
- [ ] WhatsApp API credentials sudah diisi
- [ ] Test koneksi WhatsApp API berhasil
- [ ] Log tidak ada error "Phone number too long"
- [ ] Log tidak ada error "Mass assignment"

---

## 💡 Tips

### Untuk Menghindari Error:
1. **Selalu gunakan format angka saja** (tanpa spasi/tanda hubung)
2. **Cek panjang nomor** (max 15 digit)
3. **Test dengan nomor sendiri** sebelum production
4. **Monitor log** setelah update nomor

### Format Rekomendasi:
```
Format Input: 081234567890
Sistem Auto Format: 6281234567890
Length: 13 digit ✅
```

---

## 🚀 Next Steps

1. **Cek nomor di admin settings**
2. **Pastikan format benar (hanya angka)**
3. **Simpan perubahan**
4. **Test dengan order baru**
5. **Cek log untuk konfirmasi**

---

**Updated:** 2026-01-03  
**Issues Fixed:** 
- ✅ NotificationLog mass assignment
- ✅ Phone number validation
- ✅ Better error logging
