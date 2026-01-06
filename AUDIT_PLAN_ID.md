# Audit: Files Using plan_id

## 📊 Summary

Total files found using `plan_id`: **20 files**

### Categories:

#### 🟢 **OK - Database & Model (Keep as is)**
These are database schema and should remain:
1. ✅ `database/migrations/2026_01_03_144928_create_orders_table.php` - Schema definition
2. ✅ `database/migrations/2026_01_03_144921_create_licenses_table.php` - Schema definition
3. ✅ `database/migrations/2026_01_05_120654_add_product_id_to_licenses_table.php` - Migration
4. ✅ `app/Models/Order.php` - Fillable field (for backward compatibility)
5. ✅ `app/Models/License.php` - Fillable field (for backward compatibility)

#### 🟢 **OK - Seeders (Can keep for old data)**
6. ✅ `database/seeders/DummyOrderSeeder.php` - Dummy data seeder

#### 🟡 **LEGACY FEATURES (Consider deprecating)**
7. ⚠️ `resources/views/envato/verify.blade.php` - Envato verification (if still used)
8. ⚠️ `app/Http/Controllers/EnvatoVerificationController.php` - Envato feature

#### 🟡 **STRIPE (Legacy payment gateway)**
9. ⚠️ `app/Services/StripeService.php` - Old Stripe integration
10. ⚠️ `app/Services/LicenseGenerator.php` - Old service
11. ⚠️ `app/Http/Controllers/WebhookController.php` - Stripe webhook (generateLicense method)

#### 🔴 **NEEDS FIX - Views using $order->plan**
These need to be updated to use `$order->product`:
12. ❌ `resources/views/customer/orders/show.blade.php` (line 64)
13. ❌ `resources/views/customer/dashboard.blade.php` (line 86)
14. ❌ `resources/views/admin/users/show.blade.php` (line 96)
15. ❌ `resources/views/admin/orders/show.blade.php` (line 117, 118)
16. ❌ `resources/views/admin/orders/index.blade.php` (line 61)

## ✅ Action Plan

### IMMEDIATE FIX (Critical)
Fix all views that use `$order->plan`:
- ❌ Customer order detail page
- ❌ Customer dashboard
- ❌ Admin user detail page
- ❌ Admin order detail page
- ❌ Admin order list page

### OPTIONAL (Consider for future)
- Review Envato verification feature (still needed?)
- Review Stripe service (still used or deprecated?)
- Clean up old services if not used

## 🔧 Files to Fix Now

1. `resources/views/customer/orders/show.blade.php`
2. `resources/views/customer/dashboard.blade.php`
3. `resources/views/admin/users/show.blade.php`
4. `resources/views/admin/orders/show.blade.php`
5. `resources/views/admin/orders/index.blade.php`

All should change from:
```blade
{{ $order->plan->name }}
```

To:
```blade
{{ $order->product ? $order->product->name : ($order->plan ? $order->plan->name : 'N/A') }}
```

This provides backward compatibility while prioritizing product.
