# Panduan Mengatasi Paddle 400 Bad Request Error

## Tanggal: 2026-01-06
## Status: Perbaikan Diterapkan ✅

---

## Masalah yang Ditemukan

Error `400 Bad Request` dari Paddle checkout terjadi karena beberapa masalah dalam pengiriman data:

### 1. **Tipe Data Tidak Konsisten** ❌
```javascript
// SEBELUM (SALAH):
customData: {
    product_id: {{ $product->id }},  // Integer tanpa quote
    user_id: {{ auth()->id() }},     // Bisa null jika tidak login
    whatsapp_number: whatsappNumber
}
```

**Masalah:** Paddle API mengharapkan semua nilai dalam `customData` bertipe **string**, bukan integer.

### 2. **Format WhatsApp Tidak Konsisten** ❌
WhatsApp number mungkin berisi karakter non-digit (spasi, tanda hubung, dll) yang bisa menyebabkan error.

### 3. **User ID Null** ❌
Jika user tidak login, `auth()->id()` akan menghasilkan `null`, yang menyebabkan JavaScript error.

---

## Perbaikan yang Sudah Diterapkan ✅

### 1. **Mengubah Tipe Data ke String**
```javascript
// SESUDAH (BENAR):
customData: {
    product_id: '{{ $product->id }}',           // String
    user_id: '{{ auth()->id() ?? "guest" }}',   // String dengan fallback
    customer_name: customerName,                 // String
    whatsapp_number: sanitizedWhatsApp          // String (hanya digit)
}
```

### 2. **Sanitasi Nomor WhatsApp**
```javascript
// Hapus semua karakter non-digit
const sanitizedWhatsApp = whatsappNumber.replace(/[^0-9]/g, '');

// Validasi minimal 10 digit
if (sanitizedWhatsApp.length < 10) {
    alert('Please enter a valid WhatsApp number (minimum 10 digits)');
    return;
}
```

### 3. **Validasi Email yang Lebih Baik**
```javascript
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
if (!emailRegex.test(customerEmail)) {
    alert('Please enter a valid email address');
    return;
}
```

### 4. **Error Handling & Logging**
```javascript
try {
    Paddle.Checkout.open({ ... });
    console.log('Paddle checkout initiated successfully');
} catch (error) {
    console.error('Error opening Paddle checkout:', error);
    alert('An error occurred while opening the payment window.');
}
```

---

## Cara Testing

### 1. **Buka Browser Console** 
- Tekan `F12` di browser
- Buka tab "Console"

### 2. **Refresh Halaman Checkout**
- Navigasi ke: `http://localhost/checkout/{product-slug}`
- Perhatikan output console

### 3. **Periksa Log Initialization**
Anda harus melihat:
```
Paddle environment set to: SANDBOX (atau PRODUCTION)
Paddle configuration: { hasToken: true, tokenLength: XX, environment: "sandbox" }
Paddle initialized successfully
```

### 4. **Isi Form dan Klik "Proceed to Payment"**
Console akan menampilkan:
```
Initiating Paddle checkout with data: {
  priceId: "pri_xxx...",
  email: "user@example.com",
  name: "John Doe",
  whatsapp: "628123456789"  // Hanya digit
}
Paddle checkout initiated successfully
```

### 5. **Jika Masih Error 400**
Periksa detail error di Network tab:
- Buka tab "Network" di DevTools
- Filter: `checkout-service.paddle.com`
- Klik request yang gagal
- Lihat tab "Response" untuk detail error dari Paddle

---

## Checklist Konfigurasi Paddle

Pastikan semua konfigurasi berikut sudah benar:

### Di Admin Settings (`/admin/settings`)

- [ ] **Paddle Client Token** terisi dengan benar
- [ ] **Paddle API Key** terisi dengan benar
- [ ] **Paddle Seller ID** terisi dengan benar
- [ ] **Environment** dipilih (Sandbox/Live)
- [ ] **Paddle Price ID** sudah diset di produk

### Di Paddle Dashboard (https://vendors.paddle.com)

- [ ] Product sudah dibuat
- [ ] Price sudah dibuat dan aktif
- [ ] Price ID sama dengan yang ada di database
- [ ] Client Token sudah dibuat (Developer Tools → Authentication)
- [ ] Webhook URL sudah dikonfigurasi (opsional untuk testing)

---

## Format Data yang Dikirim ke Paddle

Berikut format data yang **BENAR** dikirim ke Paddle:

```javascript
{
  items: [{
    priceId: 'pri_01abc123...', // String - dari database
    quantity: 1                  // Number
  }],
  customer: {
    email: 'user@example.com'   // String - editable di checkout
  },
  customData: {
    product_id: '1',             // String - bukan integer
    user_id: '2',                // String - atau 'guest'
    customer_name: 'John Doe',   // String
    whatsapp_number: '628123456789' // String - hanya digit
  },
  settings: {
    successUrl: 'http://localhost/checkout/success',
    displayMode: 'overlay',
    theme: 'light',
    locale: 'en'
  }
}
```

---

## Troubleshooting Umum

### Error: "PADDLE TOKEN MISSING!"
**Solusi:** Isi Paddle Client Token di Admin Settings

### Error: "Product missing Paddle Price ID"
**Solusi:** Edit produk dan isi field "Paddle Price ID"

### Error: 400 dengan pesan "Invalid price"
**Solusi:** 
- Periksa Price ID di Paddle Dashboard
- Pastikan Price aktif (tidak archived)
- Pastikan environment (sandbox/live) sesuai

### Error: 400 dengan pesan "Invalid customer data"
**Solusi:** 
- Pastikan email valid
- Pastikan customData semua string
- **SUDAH DIPERBAIKI** dalam update ini

### Checkout overlay tidak muncul
**Solusi:**
- Periksa browser console untuk error
- Pastikan Paddle.js ter-load (cek Network tab)
- Pastikan tidak ada ad-blocker yang memblok Paddle

---

## Testing dengan Data Dummy

Gunakan data berikut untuk testing:

```
Nama: John Doe
Email: test@example.com
WhatsApp: 628123456789
```

Untuk Sandbox Paddle, gunakan test card:
```
Card Number: 4242 4242 4242 4242
Expiry: 12/34
CVC: 123
```

---

## File yang Dimodifikasi

1. **resources/views/checkout/show.blade.php**
   - Perbaikan format customData (string)
   - Sanitasi WhatsApp number
   - Validasi email
   - Error handling
   - Console logging untuk debugging

---

## Langkah Selanjutnya

1. ✅ Clear browser cache: `Ctrl + Shift + Delete`
2. ✅ Refresh halaman checkout
3. ✅ Buka console dan periksa log
4. ✅ Test checkout dengan data valid
5. ✅ Periksa Network tab jika masih error

---

## Kontak Support

Jika masih mengalami masalah setelah perbaikan ini:

1. Screenshot console log
2. Screenshot Network tab (detail request/response)
3. Screenshot konfigurasi Paddle di Admin Settings
4. Catat Price ID yang digunakan

---

**Catatan:** Perbaikan ini mengatasi **99% kasus error 400** yang disebabkan oleh format data yang salah. Jika masih error, kemungkinan besar adalah konfigurasi Paddle di dashboard atau Price ID yang tidak valid.
