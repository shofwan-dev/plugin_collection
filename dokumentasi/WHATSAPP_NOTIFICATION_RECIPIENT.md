# WhatsApp Notification Recipient Feature

## 📱 Fitur Baru: WhatsApp Penerima Notifikasi

### Deskripsi
Fitur ini memungkinkan admin untuk mengatur nomor WhatsApp khusus yang akan menerima notifikasi ketika ada **order baru** masuk ke sistem. Nomor ini bisa berbeda dengan nomor WhatsApp publik yang ditampilkan di website.

---

## 🎯 Kegunaan

### **WhatsApp Publik** (Field: `whatsapp`)
- Ditampilkan di website (footer, contact page, dll)
- Untuk customer menghubungi admin
- Bisa diakses siapa saja

### **WhatsApp Penerima Notifikasi** (Field: `whatsapp_notification`)
- **HANYA** untuk menerima notifikasi order baru
- Tidak ditampilkan di website
- Bisa nomor pribadi owner/manager
- Otomatis menerima alert saat ada order baru

---

## ⚙️ Cara Mengatur

### 1. Akses Pengaturan Website
```
Login sebagai Admin → Settings → Website Settings
```

### 2. Isi Field WhatsApp
Anda akan melihat 2 field:

#### **WhatsApp (Publik)**
- Label: "WhatsApp (Publik)"
- Fungsi: Nomor yang ditampilkan di website
- Contoh: `6281234567890`

#### **WhatsApp Penerima Notifikasi**
- Label: "WhatsApp Penerima Notifikasi"
- Fungsi: Nomor yang menerima notifikasi order baru
- Contoh: `6287654321098`
- **Default**: Jika tidak diisi, akan menggunakan nomor WhatsApp Publik

### 3. Simpan Pengaturan
Klik tombol **"Simpan Perubahan"**

---

## 📨 Notifikasi yang Dikirim ke Admin

Ketika ada **order baru**, admin akan menerima WhatsApp dengan format:

```
🔔 *ORDER BARU MASUK!*

━━━━━━━━━━━━━━━━
📋 *Detail Order*
• Order ID: #17
• Customer: *Ahmad Rizki*
• Phone: 081234567890
• Produk: *Kaligrafi Allah Muhammad*
• Jumlah: 2 pcs
• Total: *Rp 300.000*
• Kota: Jakarta

📍 *Alamat Pengiriman:*
Jl. Contoh No. 123, Jakarta Selatan

📝 *Catatan:*
Tolong kirim secepatnya

💳 *Status:*
Menunggu pembayaran dari customer

🔗 *Lihat Detail:*
https://yoursite.com/admin/orders/17

_Notifikasi otomatis dari sistem PO Kaligrafi_
```

---

## 🔄 Alur Notifikasi

### Saat Customer Membuat Order:

1. **Customer** mengisi form order dan submit
2. **Sistem** menyimpan order ke database
3. **Notifikasi ke Customer**:
   - Dikirim ke nomor customer
   - Berisi detail order & link pembayaran
4. **Notifikasi ke Admin**:
   - Dikirim ke nomor `whatsapp_notification`
   - Berisi detail lengkap order
   - Link ke admin panel untuk kelola order

---

## 📋 File yang Dimodifikasi

### 1. **View - Admin Settings**
`resources/views/admin/settings/website.blade.php`
- Menambahkan field `whatsapp_notification`
- Label yang jelas membedakan kedua field

### 2. **Controller - Settings**
`app/Http/Controllers/Admin/SettingController.php`
- Validasi field `whatsapp_notification`
- Menyimpan ke database dengan fallback ke `whatsapp` jika kosong

### 3. **Service - WhatsApp**
`app/Services/WhatsAppService.php`
- Method baru: `sendNewOrderNotificationToAdmin()`
- Otomatis dipanggil saat order dibuat
- Mengambil nomor dari setting `whatsapp_notification`

### 4. **Controller - Order**
`app/Http/Controllers/User/OrderController.php`
- Sudah memanggil `sendOrderCreatedNotification()`
- Otomatis mengirim ke customer DAN admin

---

## 🧪 Testing

### Test Notifikasi Admin:

1. **Set Nomor Notifikasi**
   ```
   Admin → Settings → Website
   WhatsApp Penerima Notifikasi: 6281234567890
   ```

2. **Buat Order Baru**
   ```
   Login sebagai user → Create Order → Submit
   ```

3. **Cek WhatsApp**
   - Nomor `6281234567890` harus menerima notifikasi
   - Format sesuai template di atas

### Jika Tidak Menerima:

**Check 1: Nomor Sudah Diisi?**
```sql
SELECT value FROM settings 
WHERE `key` = 'whatsapp_notification' 
AND `group` = 'website';
```

**Check 2: WhatsApp Service Aktif?**
```
Admin → Settings → WhatsApp
Pastikan API Key dan Endpoint sudah diisi
```

**Check 3: Cek Log**
```bash
tail -f storage/logs/laravel.log | grep "admin_new_order"
```

---

## 💡 Use Cases

### **Scenario 1: Owner Ingin Notifikasi Langsung**
```
WhatsApp Publik: 6281111111111 (CS Team)
WhatsApp Notifikasi: 6282222222222 (Owner pribadi)
```
✅ Customer chat ke CS Team
✅ Owner langsung tahu ada order baru

### **Scenario 2: Satu Nomor untuk Semua**
```
WhatsApp Publik: 6281111111111
WhatsApp Notifikasi: (kosong/sama)
```
✅ Satu nomor untuk semua keperluan

### **Scenario 3: Multiple Admin**
```
WhatsApp Publik: 6281111111111 (CS)
WhatsApp Notifikasi: 6282222222222 (Manager)
```
✅ CS handle customer service
✅ Manager monitor order masuk

---

## 🔐 Keamanan & Privacy

### ✅ **Aman**
- Nomor notifikasi **TIDAK** ditampilkan di website
- Hanya disimpan di database
- Hanya admin yang bisa lihat/edit

### ⚠️ **Perhatian**
- Pastikan nomor notifikasi adalah nomor yang aktif
- Jangan gunakan nomor yang sering berganti
- Backup nomor di tempat aman

---

## 📊 Database Schema

### Table: `settings`
```sql
| key                    | value          | group   |
|------------------------|----------------|---------|
| whatsapp               | 6281111111111  | website |
| whatsapp_notification  | 6282222222222  | website |
```

---

## 🚀 Fitur Mendatang

- [ ] Multiple notification recipients (array of numbers)
- [ ] Notification preferences (pilih jenis notifikasi)
- [ ] Email notification sebagai backup
- [ ] Telegram notification integration

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek log: `storage/logs/laravel.log`
2. Test WhatsApp service: Admin → Settings → WhatsApp → Test Connection
3. Verifikasi nomor format: harus `62xxx` tanpa spasi/tanda

---

**Created:** 2026-01-03  
**Feature:** WhatsApp Notification Recipient  
**Status:** ✅ Production Ready
