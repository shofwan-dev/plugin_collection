# Cancel Order Feature

## 📋 Fitur Batalkan Order

Fitur ini memungkinkan customer untuk membatalkan order yang belum dibayar. Data order **TIDAK DIHAPUS**, hanya status yang berubah menjadi "cancelled".

---

## 🎯 Cara Kerja

### **Tombol "Batalkan Order"**
- Muncul di halaman detail order (`/my/orders/{id}`)
- Hanya muncul jika:
  - ✅ Order belum dibayar (`payment_status !== 'paid'`)
  - ✅ Order belum dibatalkan (`status !== 'cancelled'`)

### **Modal Konfirmasi**
- Customer harus konfirmasi pembatalan
- Bisa mengisi alasan pembatalan (opsional)
- Alasan membantu admin untuk improve layanan

### **Proses Pembatalan**
1. Customer klik "Batalkan Order"
2. Modal konfirmasi muncul
3. Customer isi alasan (opsional)
4. Klik "Ya, Batalkan Order"
5. Status berubah menjadi "cancelled"
6. Data order tetap tersimpan di database

---

## 🔧 Implementasi

### **1. View - Order Detail**
`resources/views/user/orders/show.blade.php`

**Tombol Batalkan:**
```blade
<button type="button" 
        class="btn btn-outline-danger btn-sm w-100" 
        data-bs-toggle="modal" 
        data-bs-target="#cancelOrderModal">
    <i class="fas fa-times-circle me-1"></i>
    Batalkan Order
</button>
```

**Modal Konfirmasi:**
- Header merah dengan icon warning
- Tampilkan detail order
- Form input alasan pembatalan
- Tombol konfirmasi

---

### **2. Controller - OrderController**
`app/Http/Controllers/User/OrderController.php`

**Method: `cancel()`**

**Validasi:**
```php
// 1. Cek ownership
if ($order->user_id !== Auth::id()) {
    abort(403);
}

// 2. Cek sudah dibatalkan
if ($order->status === 'cancelled') {
    return redirect()->with('info', 'Sudah dibatalkan');
}

// 3. Cek sudah dibayar
if ($order->payment_status === 'paid') {
    return redirect()->with('error', 'Tidak bisa dibatalkan');
}
```

**Update Status:**
```php
$order->status = 'cancelled';
$order->cancel_reason = $validated['cancel_reason'] ?? null;
$order->cancelled_at = now();
$order->save();
```

---

### **3. Route**
`routes/web.php`

```php
Route::put('/{order}/cancel', [UserOrderController::class, 'cancel'])
    ->name('user.orders.cancel');
```

---

### **4. Database**
**Migration:** `2026_01_03_023124_add_cancel_fields_to_orders_table.php`

**Kolom Baru:**
```php
$table->text('cancel_reason')->nullable();
$table->timestamp('cancelled_at')->nullable();
```

**Model:** `app/Models/Order.php`
```php
protected $fillable = [
    // ...
    'cancel_reason',
];

protected $casts = [
    // ...
    'cancelled_at' => 'datetime',
];
```

---

## 📊 Status Order

### **Status yang Bisa Dibatalkan:**
| Status | Payment Status | Bisa Dibatalkan? |
|--------|----------------|------------------|
| pending | pending | ✅ YA |
| pending | expired | ✅ YA |
| pending | failed | ✅ YA |
| processing | pending | ✅ YA |
| pending | paid | ❌ TIDAK |
| processing | paid | ❌ TIDAK |
| cancelled | any | ❌ Sudah dibatalkan |

### **Alasan Tidak Bisa Dibatalkan:**
1. **Sudah dibayar** - Harus request refund ke admin
2. **Sudah dibatalkan** - Tidak perlu dibatalkan lagi
3. **Bukan pemilik** - Hanya pemilik order yang bisa batalkan

---

## 🧪 Testing

### **Test 1: Batalkan Order Pending**
```
1. Login sebagai customer
2. Buat order baru (jangan bayar)
3. Buka detail order
4. Klik "Batalkan Order"
5. Isi alasan (opsional)
6. Klik "Ya, Batalkan Order"
7. Status berubah menjadi "Dibatalkan"
```

### **Test 2: Coba Batalkan Order yang Sudah Dibayar**
```
1. Buka order yang sudah paid
2. Tombol "Batalkan Order" tidak muncul
3. Atau jika coba akses route langsung, akan error
```

### **Test 3: Cek Database**
```sql
SELECT id, status, payment_status, cancel_reason, cancelled_at 
FROM orders 
WHERE id = 18;

-- Expected:
-- status: 'cancelled'
-- cancel_reason: 'Salah pilih produk'
-- cancelled_at: '2026-01-03 09:35:00'
```

---

## 📨 Notifikasi (Future Enhancement)

### **Notifikasi ke Admin:**
Ketika customer membatalkan order, admin bisa menerima notifikasi:

```
❌ *ORDER DIBATALKAN*

━━━━━━━━━━━━━━━━
📋 *Detail Order*
• Order ID: #18
• Customer: *Ahmad Rizki*
• Produk: *Kaligrafi Allah Muhammad*
• Total: *Rp 300.000*

📝 *Alasan Pembatalan:*
Salah pilih produk

⏰ *Dibatalkan:*
03 Jan 2026, 09:35

_Notifikasi otomatis dari sistem PO Kaligrafi_
```

---

## 🔍 Monitoring

### **Cek Order yang Dibatalkan:**
```sql
SELECT 
    id,
    customer_name,
    total_amount,
    cancel_reason,
    cancelled_at
FROM orders 
WHERE status = 'cancelled'
ORDER BY cancelled_at DESC
LIMIT 10;
```

### **Analisa Alasan Pembatalan:**
```sql
SELECT 
    cancel_reason,
    COUNT(*) as total
FROM orders 
WHERE status = 'cancelled'
AND cancel_reason IS NOT NULL
GROUP BY cancel_reason
ORDER BY total DESC;
```

### **Cek Log:**
```bash
tail -f storage/logs/laravel.log | grep "Order cancelled"
```

**Expected Log:**
```
[2026-01-03 09:35:00] local.INFO: Order cancelled by user
{
  "order_id": 18,
  "user_id": 1,
  "reason": "Salah pilih produk"
}
```

---

## 💡 Best Practices

### **Untuk Customer:**
1. **Batalkan segera** jika tidak jadi order
2. **Isi alasan** untuk membantu admin improve
3. **Jangan batalkan** jika sudah bayar (request refund ke admin)

### **Untuk Admin:**
1. **Monitor pembatalan** secara berkala
2. **Analisa alasan** untuk improve produk/layanan
3. **Follow up** jika ada pattern tertentu

---

## 🚀 Future Enhancements

### **1. Notifikasi Admin**
```php
// Di OrderController::cancel()
try {
    $whatsapp = app(\App\Services\WhatsAppService::class);
    $whatsapp->sendOrderCancelledNotificationToAdmin($order);
} catch (\Exception $e) {
    Log::error('Failed to send admin notification for cancelled order');
}
```

### **2. Refund untuk Order yang Sudah Dibayar**
- Tambah button "Request Refund"
- Admin approve/reject refund
- Notifikasi ke customer

### **3. Auto-Cancel untuk Expired Orders**
```php
// Cron job daily
$expiredOrders = Order::where('payment_status', 'pending')
    ->where('payment_expired_at', '<', now())
    ->get();

foreach ($expiredOrders as $order) {
    $order->status = 'cancelled';
    $order->cancel_reason = 'Pembayaran expired';
    $order->cancelled_at = now();
    $order->save();
}
```

### **4. Cancel Reason Dropdown**
```blade
<select name="cancel_reason" class="form-select">
    <option value="">Pilih alasan...</option>
    <option value="Salah pilih produk">Salah pilih produk</option>
    <option value="Berubah pikiran">Berubah pikiran</option>
    <option value="Harga terlalu mahal">Harga terlalu mahal</option>
    <option value="Lainnya">Lainnya</option>
</select>
```

---

## 📋 Checklist

- [x] ✅ Tombol "Batalkan Order" di order detail
- [x] ✅ Modal konfirmasi pembatalan
- [x] ✅ Form input alasan pembatalan
- [x] ✅ Validasi (ownership, status, payment)
- [x] ✅ Update status ke "cancelled"
- [x] ✅ Simpan alasan & timestamp
- [x] ✅ Migration untuk kolom baru
- [x] ✅ Update Order model
- [x] ✅ Route untuk cancel
- [x] ✅ Logging
- [ ] ⏳ Notifikasi ke admin (future)
- [ ] ⏳ Auto-cancel expired orders (future)

---

**Created:** 2026-01-03  
**Feature:** Cancel Order  
**Status:** ✅ Production Ready
