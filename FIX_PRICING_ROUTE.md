# Quick Fix: Route [pricing] not defined

## ❌ Error di Production
```
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [pricing] not defined.
URL: https://store.mutekar.com/dashboard/orders
```

## ✅ Fix Applied

### File Modified: `routes/web.php`

**Added:**
```php
Route::get('/pricing', [HomeController::class, 'index'])->name('pricing');
```

Route `pricing` sekarang adalah **alias untuk home page**, yang menampilkan products/pricing.

## 🚀 Deploy ke Production

Setelah push ke GitHub, SSH ke server production dan jalankan:

```bash
# SSH ke server
ssh user@store.mutekar.com

# Navigate ke project directory
cd /path/to/project

# Pull latest changes
git pull origin master

# Clear route cache
php artisan route:clear
php artisan route:cache

# Clear all caches (optional tapi recommended)
php artisan cache:clear
php artisan config:clear

# Restart PHP-FPM (jika diperlukan)
sudo systemctl restart php8.2-fpm
# atau
sudo service php-fpm restart
```

## ✅ Verifikasi

Setelah deploy, test:

1. **Check route exists:**
```bash
php artisan route:list --name=pricing
```

Expected output:
```
GET|HEAD  pricing .......... pricing › HomeController@index
```

2. **Test URL di browser:**
- https://store.mutekar.com/pricing (should load home page)
- https://store.mutekar.com/dashboard/orders (should not error anymore)

3. **Test semua links:**
- Dashboard → "Purchase a License" button
- Orders page → "Browse Products" button
- Licenses page → "Purchase License" button
- Checkout cancel page → "Back to Pricing" button

## 📝 What This Fixed

Route `pricing` digunakan di beberapa view:
- `resources/views/customer/orders/index.blade.php` (line 76)
- `resources/views/customer/licenses.blade.php` (line 188)
- `resources/views/customer/dashboard.blade.php` (line 59)
- `resources/views/checkout/cancel.blade.php` (line 17)

Semua link ini sekarang akan redirect ke home page yang menampilkan products/pricing.

## 🎯 Commands Summary

```bash
# One-liner untuk deploy fix ini
git pull origin master && php artisan route:clear && php artisan route:cache && php artisan cache:clear && php artisan config:clear
```

## ⚠️ Important Notes

1. **Route cache** di production harus di-clear setelah pull
2. Jika pakai **load balancer/multiple servers**, clear cache di semua server
3. Jika pakai **OPcache**, restart PHP-FPM untuk clear OPcache
4. Test URL setelah deploy untuk memastikan fix berhasil

## 🔗 Related Commit

- Fix commit: `616552a` - "fix: add pricing route alias for backward compatibility"
- Previous commit: `c09e759` - "perbaikan notif wa dan sinkron order"
