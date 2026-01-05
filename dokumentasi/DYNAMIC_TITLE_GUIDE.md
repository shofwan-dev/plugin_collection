# Dynamic Site Title Implementation

## Overview
Title website sekarang dinamis berdasarkan setting "Nama Website" yang dapat diubah melalui halaman pengaturan admin.

## Lokasi Setting
**Admin Panel** → **Pengaturan** → **Website** → **Nama Website**

Path: `/admin/settings/website`

## File yang Diupdate

### View Files (Blade Templates)
Semua file view berikut telah diupdate untuk menggunakan `$site_settings['site_name']`:

#### Public Pages
1. `resources/views/home.blade.php` - Halaman beranda
2. `resources/views/about.blade.php` - Halaman tentang kami (title & heading)
3. `resources/views/product-detail.blade.php` - Detail produk

#### Auth Pages
4. `resources/views/auth/login.blade.php` - Halaman login
5. `resources/views/auth/register.blade.php` - Halaman registrasi

#### User Dashboard
6. `resources/views/dashboard.blade.php` - Dashboard user (old)
7. `resources/views/user/dashboard.blade.php` - Dashboard user (new)
8. `resources/views/user/orders/create.blade.php` - Buat order baru
9. `resources/views/user/orders/index.blade.php` - Daftar pesanan
10. `resources/views/user/orders/show.blade.php` - Detail pesanan

### Layout Files (Already Dynamic)
File layout berikut sudah menggunakan `$site_settings` secara dinamis:
- `resources/views/layouts/app.blade.php` - Layout utama
- `resources/views/layouts/admin.blade.php` - Layout admin
- `resources/views/layouts/partials/footer.blade.php` - Footer

## Format Penggunaan

### Untuk Title di @section
```blade
@section('title', ($site_settings['site_name'] ?? 'PO Kaligrafi Lampu') . ' - Beranda')
```

### Untuk Title dengan Variable
```blade
@section('title', 'Detail Order #' . $order->id . ' - ' . ($site_settings['site_name'] ?? 'PO Kaligrafi Lampu'))
```

### Untuk Konten HTML
```blade
<h1>Tentang {{ $site_settings['site_name'] ?? 'PO Kaligrafi Lampu' }}</h1>
```

## Helper Functions
Dibuat helper functions di `app/Helpers/helpers.php` untuk memudahkan akses:

```php
// Get site name
site_name(); // Returns site name or default 'PO Kaligrafi Lampu'

// Get any site setting
site_setting('site_name');
site_setting('tagline');
site_setting('email');
```

## Cara Kerja

1. **SettingsServiceProvider** (`app/Providers/SettingsServiceProvider.php`) memuat settings dari database
2. Settings di-share ke semua view melalui View Composer
3. Variable `$site_settings` tersedia di semua blade templates
4. Jika setting tidak ada, fallback ke 'PO Kaligrafi Lampu'

## Testing

### Sebelum
Title semua halaman: "PO Kaligrafi Lampu - [Page Name]"

### Setelah
1. Login ke admin: `/admin/settings/website`
2. Ubah "Nama Website" menjadi misalnya: "Kaligrafi Nusantara"
3. Simpan perubahan
4. Refresh halaman publik manapun
5. Title browser akan berubah menjadi: "Kaligrafi Nusantara - [Page Name]"

## Rollback Guide
Jika terjadi masalah, settings default di database seeder:
```php
// database/seeders/SettingsSeeder.php
'site_name' => 'PO Kaligrafi Lampu',
```

Run seeder ulang:
```bash
php artisan db:seed --class=SettingsSeeder
```

## Benefits
✅ Satu tempat untuk mengubah nama website
✅ Tidak perlu edit banyak file
✅ Mudah rebrand atau white-label
✅ SEO friendly - title konsisten di semua halaman
✅ Fallback ke default jika setting kosong
