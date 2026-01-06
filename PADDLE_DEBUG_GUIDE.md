# 🔍 Debug: Products & Prices Tidak Terbaca

## Masalah
✅ Paddle connection berhasil  
✅ Product & Price sudah dibuat di Paddle Dashboard  
❌ Test button menunjukkan: Products: 0, Prices: 0

---

## Penyebab Umum

### 1. **Environment Mismatch** (Paling Sering!)
- Product dibuat di **LIVE** tapi API Key dari **SANDBOX**
- Atau sebaliknya: Product di **SANDBOX** tapi API Key **LIVE**

### 2. **API Key Permissions**
- API Key tidak punya permission untuk READ products/prices
- Perlu regenerate dengan permission yang benar

### 3. **Caching/Delay**
- Paddle API kadang delay beberapa menit setelah product dibuat
- Coba refresh setelah 5 menit

---

## 🛠️ Tool Debug yang Sudah Dibuat

Saya sudah membuat file: `public/paddle-debug.php`

### Cara Menggunakan:

#### **Step 1: Edit File**
Buka file: `public/paddle-debug.php`

Cari baris ini (sekitar baris 12-13):
```php
$API_KEY = 'YOUR_API_KEY_HERE';  // Paste your API Key here
$ENVIRONMENT = 'live';  // 'sandbox' or 'live'
```

Ganti dengan:
```php
$API_KEY = 'pdl_live_xxxxx';  // Paste API Key Anda dari Paddle Dashboard
$ENVIRONMENT = 'live';  // Sesuaikan: 'sandbox' atau 'live'
```

#### **Step 2: Akses Tool**
Buka browser:
```
http://localhost/paddle-debug.php
```

#### **Step 3: Lihat Hasil**
Tool akan menampilkan:
- ✅ Connection status
- 📦 List semua products (dengan ID dan nama)
- 💰 List semua prices (dengan Price ID dan amount)
- ⚠️ Diagnosis masalah jika ada

---

## 🎯 Checklist Debugging

### ✅ **Periksa Environment**

**Di Paddle Dashboard:**
1. Klik icon **"..."** di pojok kiri atas
2. Pilih environment: **Sandbox** atau **Live**
3. Catat environment mana yang aktif
4. Periksa apakah ada products di sana

**Di Laravel Settings (`/admin/settings`):**
1. Periksa field **"Environment"**
2. Pastikan SAMA dengan Paddle Dashboard
3. Pastikan API Key & Client Token dari environment yang SAMA

### ✅ **Periksa API Key Permissions**

**Membuat API Key Baru dengan Permission Lengkap:**

1. Login ke Paddle Dashboard
2. Pilih environment yang benar (Sandbox/Live)
3. Buka: **Developer Tools** → **Authentication**
4. Klik **"API Keys"** tab
5. Klik **"Create API Key"** (atau regenerate yang lama)
6. Beri nama: `Laravel Full Access`
7. **PENTING:** Centang SEMUA permissions:
   - ✅ **Read** - Products
   - ✅ **Write** - Products
   - ✅ **Read** - Prices
   - ✅ **Write** - Prices
   - ✅ **Read** - Transactions
   - ✅ (Centang semua yang ada)
8. **Save** dan **Copy** API Key
9. Paste ke Laravel Settings (`/admin/settings`)

### ✅ **Verifikasi Product di Dashboard**

1. Login Paddle Dashboard
2. **PASTIKAN** environment correct (check pojok kiri atas)
3. Buka **Catalog** → **Products**
4. Apakah ada products di sana?
   - **YA** → Good, lanjut step berikutnya
   - **TIDAK** → Anda berada di environment yang salah!

---

## 🔄 Quick Fix Steps

### **Scenario 1: Environment Salah**

**Jika products ada di SANDBOX, tapi Laravel setting = LIVE:**

```bash
1. Buka: /admin/settings
2. Ubah "Environment" ke: Sandbox
3. Paste SANDBOX API Key
4. Paste SANDBOX Client Token
5. Save
6. Test Connection lagi
```

### **Scenario 2: API Key Tidak Punya Permission**

**Regenerate API Key:**

```bash
1. Paddle Dashboard → Developer Tools → Authentication
2. Hapus API Key lama (atau buat baru)
3. Create API Key BARU dengan ALL permissions
4. Copy API Key baru
5. Paste ke /admin/settings
6. Save
7. Test Connection lagi
```

### **Scenario 3: Products Beneran Belum Dibuat**

**Buat Product di Environment yang Benar:**

```bash
1. Paddle Dashboard → Pastikan environment BENAR (check pojok kiri atas)
2. Catalog → Products → Create Product
3. Isi nama, deskripsi, dsb
4. Save
5. Masuk ke product → Prices tab → Create Price
6. Isi amount, currency, dll
7. Save
8. Copy Price ID (format: pri_01xxx)
9. Test lagi dengan paddle-debug.php
```

---

## 📊 Expected Results (Jika Benar)

### Tool Debug (`paddle-debug.php`) akan menampilkan:

```
✓ SUCCESS (HTTP 200) - Products
Total Products: 1 (atau lebih)

Products List:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CF7 to WhatsApp Gateway
ID: pro_01xxx
Status: active
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✓ SUCCESS (HTTP 200) - Prices
Total Prices: 1 (atau lebih)

Prices List:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Standard License
Price ID: pri_01xxx
Amount: 4900 USD
Product ID: pro_01xxx
Status: active
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### Laravel Test Button akan menampilkan:

```
✅ Paddle Connected Successfully!
🌍 Environment: LIVE
🏢 Seller ID: 277312
📦 Products: 1 ←← BUKAN 0!
💰 Prices: 1 ←← BUKAN 0!
🔑 Client Token: ✓ Configured
```

---

## ⚡ Most Likely Solution

**Berdasarkan pengalaman, 90% kasus ini disebabkan oleh:**

**ENVIRONMENT MISMATCH!**

Coba ini:

1. Buka Paddle Dashboard
2. Klik icon **"..."** (pojok kiri atas)
3. Switch ke **SANDBOX**
4. Periksa: Ada products di sana?
   - **YA** → Berarti products Anda di SANDBOX, bukan LIVE!
   - **Solusi:** Ubah Laravel settings ke environment Sandbox

**ATAU:**

Create products di environment LIVE yang baru:
1. Switch Paddle ke **LIVE** (icon "..." pojok kiri atas)
2. Create product baru di sana
3. Create price untuk product tersebut
4. Copy Price ID
5. Test lagi

---

## 📝 Langkah Selanjutnya

1. ✅ Edit `public/paddle-debug.php` → isi API Key
2. ✅ Buka `http://localhost/paddle-debug.php`
3. ✅ Lihat hasil diagnostics
4. ✅ Screenshot hasil & beri tahu saya apa yang muncul

Saya akan bantu analyze hasil debug tool untuk fix masalah ini! 🚀

---

**File Debug:** `public/paddle-debug.php`  
**Created:** 2026-01-06 22:37  
**Purpose:** Diagnose Products/Prices not showing issue
