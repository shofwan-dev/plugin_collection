# Admin Notification Events

## 📨 Notifikasi WhatsApp ke Admin

Sistem sekarang mengirim notifikasi WhatsApp ke admin untuk 2 event penting:

---

## 🎯 Event 1: Order Baru Dibuat

### **Trigger:**
Ketika customer membuat order baru (belum bayar)

### **Notifikasi ke Admin:**
```
🔔 *ORDER BARU MASUK!*

━━━━━━━━━━━━━━━━
📋 *Detail Order*
• Order ID: #18
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
https://yoursite.com/admin/orders/18

_Notifikasi otomatis dari sistem PO Kaligrafi_
```

### **Tujuan:**
- Admin tahu ada order baru masuk
- Bisa monitor apakah customer akan bayar
- Bisa follow up jika perlu

---

## 💰 Event 2: Pembayaran Berhasil (BARU!)

### **Trigger:**
Ketika pembayaran customer berhasil diverifikasi oleh iPaymu

### **Notifikasi ke Admin:**
```
💰 *PEMBAYARAN DITERIMA!*

━━━━━━━━━━━━━━━━
✅ *Order Telah Dibayar*

📋 *Detail Order*
• Order ID: #18
• Customer: *Ahmad Rizki*
• Phone: 081234567890
• Produk: *Kaligrafi Allah Muhammad*
• Jumlah: 2 pcs
• Total: *Rp 300.000*
• Kota: Jakarta

💳 *Pembayaran:*
• Status: LUNAS ✅
• Dibayar: 03 Jan 2026, 09:15
• Transaction ID: ec2d85d1-e978-4634-bbc5-a7bb82878485

📍 *Alamat Pengiriman:*
Jl. Contoh No. 123, Jakarta Selatan

📝 *Catatan:*
Tolong kirim secepatnya

🎯 *Action Required:*
• Konfirmasi pembayaran
• Update status produksi
• Siapkan untuk proses

🔗 *Kelola Order:*
https://yoursite.com/admin/orders/18

_Notifikasi otomatis dari sistem PO Kaligrafi_
```

### **Tujuan:**
- Admin langsung tahu ada pembayaran masuk
- Bisa segera konfirmasi dan proses order
- Tidak perlu cek manual di admin panel

---

## 📊 Comparison: Customer vs Admin Notification

### **Event: Order Baru**

| Recipient | Message Focus | Action |
|-----------|---------------|--------|
| **Customer** | Terima kasih, link pembayaran | Bayar sekarang |
| **Admin** | Detail order, menunggu pembayaran | Monitor & follow up |

### **Event: Pembayaran Berhasil**

| Recipient | Message Focus | Action |
|-----------|---------------|--------|
| **Customer** | Konfirmasi pembayaran, timeline | Tunggu update produksi |
| **Admin** | Pembayaran diterima, action required | Konfirmasi & proses order |

---

## 🔄 Alur Lengkap Notifikasi

### **Step 1: Customer Buat Order**
```
Customer Submit Order
    ↓
📱 Notifikasi ke Customer: "Order dibuat, silakan bayar"
📱 Notifikasi ke Admin: "Order baru masuk, menunggu pembayaran"
```

### **Step 2: Customer Bayar**
```
Customer Klik "Bayar Sekarang"
    ↓
Redirect ke iPaymu
    ↓
Customer Selesaikan Pembayaran
```

### **Step 3: iPaymu Callback**
```
iPaymu Send Callback: "Payment Success"
    ↓
Sistem Update Order Status: "paid"
    ↓
📱 Notifikasi ke Customer: "Pembayaran berhasil, terima kasih"
📱 Notifikasi ke Admin: "Pembayaran diterima, action required" ⭐ BARU!
```

---

## ⚙️ Konfigurasi

### **Nomor Penerima:**
Semua notifikasi admin dikirim ke:
```
Settings → Website → WhatsApp Penerima Notifikasi
```

**Format yang Didukung:**
- Personal: `6281234567890`
- Group: `120363166537946168@g.us` ✅ Recommended
- Contact ID: `6281234567890@c.us`

### **Rekomendasi:**
Gunakan **WhatsApp Group** agar:
- ✅ Multiple admin bisa lihat
- ✅ Shared history
- ✅ Team collaboration
- ✅ No single point of failure

---

## 🧪 Testing

### **Test Event 1: Order Baru**
```
1. Login sebagai customer
2. Create new order
3. Submit
4. Cek WhatsApp admin → Harus ada notifikasi "ORDER BARU MASUK"
```

### **Test Event 2: Pembayaran Berhasil**
```
1. Dari order yang sudah dibuat, klik "Bayar Sekarang"
2. Selesaikan pembayaran di iPaymu (atau simulasi callback)
3. Cek WhatsApp admin → Harus ada notifikasi "PEMBAYARAN DITERIMA"
```

### **Simulasi Callback (untuk testing):**
```
POST http://localhost/ipaymu/callback
Content-Type: application/json

{
  "sid": "ec2d85d1-e978-4634-bbc5-a7bb82878485",
  "status": "1"
}
```

---

## 📋 Checklist Notifikasi Admin

### **Event: Order Baru**
- [x] ✅ Notifikasi ke customer
- [x] ✅ Notifikasi ke admin
- [x] ✅ Include detail order
- [x] ✅ Include link ke admin panel

### **Event: Pembayaran Berhasil**
- [x] ✅ Notifikasi ke customer
- [x] ✅ Notifikasi ke admin ⭐ BARU!
- [x] ✅ Include payment info
- [x] ✅ Include transaction ID
- [x] ✅ Include action required
- [x] ✅ Include link ke admin panel

---

## 🔍 Monitoring

### **Cek Log Notifikasi:**
```bash
# Cek notifikasi order baru
tail -f storage/logs/laravel.log | grep "admin_new_order"

# Cek notifikasi pembayaran berhasil
tail -f storage/logs/laravel.log | grep "admin_payment_success"

# Cek semua notifikasi admin
tail -f storage/logs/laravel.log | grep "admin_"
```

### **Expected Log:**
```
[2026-01-03 09:25:00] local.INFO: WhatsApp notification sent
{
  "order_id": 18,
  "type": "admin_new_order",
  "status": "sent"
}

[2026-01-03 09:30:00] local.INFO: WhatsApp notification sent
{
  "order_id": 18,
  "type": "admin_payment_success",
  "status": "sent"
}
```

---

## 💡 Tips untuk Admin

### **Tip 1: Quick Response**
```
Begitu dapat notifikasi "PEMBAYARAN DITERIMA":
1. Klik link ke admin panel
2. Konfirmasi pembayaran
3. Update status produksi
4. Inform team production
```

### **Tip 2: Use WhatsApp Group**
```
Buat group: "PO Kaligrafi - Orders"
Members:
- Owner
- Manager
- Production Lead
- CS Lead

→ Semua langsung tahu ada order baru & pembayaran masuk!
```

### **Tip 3: Set Notification Sound**
```
WhatsApp Group Settings
→ Custom Notifications
→ Set unique sound untuk group ini
→ Langsung tahu ada notifikasi penting
```

---

## 🚀 Future Enhancements

Notifikasi admin yang bisa ditambahkan:
- [ ] Order status changed (processing, shipping, etc)
- [ ] Order cancelled
- [ ] Payment expired
- [ ] Refund requested
- [ ] Customer inquiry via WhatsApp
- [ ] Low stock alert
- [ ] Daily sales summary

---

## 📞 Support

### **Jika Notifikasi Tidak Masuk:**

**Check 1: Nomor Admin Sudah Diisi?**
```sql
SELECT value FROM settings 
WHERE `key` = 'whatsapp_notification';
```

**Check 2: WhatsApp Service Active?**
```
Admin → Settings → WhatsApp
→ Test Connection
```

**Check 3: Cek Log Error**
```bash
tail -100 storage/logs/laravel.log | grep -i "Failed to send admin"
```

---

**Created:** 2026-01-03  
**Feature:** Admin Notification for Payment Success  
**Status:** ✅ Production Ready
