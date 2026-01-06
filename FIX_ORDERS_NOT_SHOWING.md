# Troubleshooting: Order Tidak Muncul di My Orders

## 🐛 Masalah

Order berhasil dibayar (Paddle transaction completed) tapi tidak muncul di halaman "My Orders" customer.

## 🔍 Root Causes

### 1. **Mismatch Relationship: plan vs product**
- View menggunakan `$order->plan` 
- Tapi HandlePaddleTransaction menyimpan `product_id` bukan `plan_id`
- Hasil: Error saat load relationship, order tidak ditampilkan

### 2. **User ID Mismatch**
- Order dibuat dengan user yang berbeda 
- Customer login dengan user lain
- Order tidak muncul karena query `where('user_id', Auth::id())`

## ✅ Solusi yang Diterapkan

### 1. **Fixed Controller**
File: `app/Http/Controllers/Customer/OrderController.php`

```php
// BEFORE
$orders = Order::where('user_id', Auth::id())
    ->with(['plan', 'license']) // ❌ Wrong: plan doesn't exist
    ...

// AFTER
$orders = Order::where('user_id', Auth::id())
    ->with(['product', 'license']) // ✅ Correct: using product
    ...
```

### 2. **Fixed View**
File: `resources/views/customer/orders/index.blade.php`

```blade
{{-- BEFORE --}}
<td>{{ $order->plan->name }}</td> {{-- ❌ Error if plan_id is null --}}

{{-- AFTER --}}
<td>{{ $order->product ? $order->product->name : 'N/A' }}</td> {{-- ✅ Safe --}}
```

### 3. **Enhanced Layout**
- ✅ Responsive design (mobile cards, desktop table)
- ✅ Better status badges with icons
- ✅ Improved empty state
- ✅ Better typography and spacing

### 4. **Added Logging**
Controller sekarang log setiap kali customer membuka halaman orders:
```php
\Log::info('Customer viewing orders', ['user_id' => $userId]);
\Log::info('Orders retrieved', ['count' => $orders->total()]);
```

## 🧪 Debug: Kenapa Order Tidak Muncul?

### Check 1: Apakah Order Ada di Database?
```bash
php artisan tinker
>>> App\Models\Order::count()
>>> App\Models\Order::latest()->first()
```

### Check 2: User ID di Order vs Login User
```bash
php artisan tinker
>>> $order = App\Models\Order::latest()->first()
>>> echo "Order User ID: " . $order->user_id
>>> $user = App\Models\User::where('email', 'customer@example.com')->first()
>>> echo "Login User ID: " . $user->id
```

**Jika berbeda**, order tidak akan muncul karena:
```php
Order::where('user_id', Auth::id()) // Only orders for logged in user
```

### Check 3: Lihat Log
```bash
tail -f storage/logs/laravel.log | grep -i "customer viewing orders"
```

Expected log:
```
[timestamp] Customer viewing orders {"user_id": 1}
[timestamp] Orders retrieved for customer {"user_id": 1, "count": 5}
```

### Check 4: Test Query Manually
```bash
php artisan tinker
>>> $userId = 1; // Ganti dengan user ID yang login
>>> $orders = App\Models\Order::where('user_id', $userId)->get();
>>> echo "Orders found: " . $orders->count();
>>> foreach($orders as $order) { echo $order->order_number . " - " . $order->product->name . "\n"; }
```

## 🔧 Fix Jika Order User ID Salah

Jika order dibuat dengan user_id yang salah, update manual:

```bash
php artisan tinker
>>> $correctUserId = 1; // ID user yang benar
>>> $order = App\Models\Order::where('paddle_transaction_id', 'txn_xxx')->first();
>>> $order->update(['user_id' => $correctUserId]);
>>> echo "Updated order {$order->id} to user {$correctUserId}";
```

## ⚠️ Mencegah Issue di Masa Depan

### Pastikan HandlePaddleTransaction Correct
File: `app/Listeners/HandlePaddleTransaction.php`

```php
$order = Order::updateOrCreate(
    ['paddle_transaction_id' => $transaction->paddle_id],
    [
        'user_id' => $billable->id, // ✅ Harus billable user (yang login)
        'product_id' => $product->id, // ✅ Bukan plan_id
        ...
    ]
);
```

### Log Setiap Order Creation
Di HandlePaddleTransaction sudah ada logging:
```php
Log::info('HandlePaddleTransaction: Order created/updated', [
    'order_id' => $order->id,
    'user_id' => $order->user_id,
    'customer_email' => $order->customer_email,
]);
```

Check log ini setelah payment untuk verifikasi user_id correct.

## ✅ Verifikasi Fix

### 1. Test di Browser
1. Login sebagai customer
2. Buka `/dashboard/orders`
3. Pastikan order muncul

### 2. Check Logs
```bash
tail -f storage/logs/laravel.log
```

Seharusnya muncul:
```
[timestamp] Customer viewing orders {"user_id": 1}
[timestamp] Orders retrieved for customer {"user_id": 1, "count": X}
```

### 3. Test Payment Flow
1. Login sebagai customer
2. Beli product via Paddle
3. Setelah payment success:
   - Check log: `HandlePaddleTransaction: Order created/updated`
   - Check user_id di log
   - Refresh `/dashboard/orders`
   - Order harus langsung muncul

## 📊 Common Scenarios

### Scenario 1: Order Ada Tapi Tidak Muncul
**Cause**: User ID mismatch
**Solution**: 
```bash
# Check user_id di order
php artisan tinker
>>> App\Models\Order::latest()->first()->user_id

# Check logged in user
>>> Auth::id() // or $user->id
```

### Scenario 2: Error "plan not found"
**Cause**: View menggunakan `$order->plan` tapi plan_id null
**Solution**: Sudah fixed dengan `$order->product`

### Scenario 3: Order Muncul Tapi Nama Product "N/A"
**Cause**: product_id null atau product deleted
**Solution**: 
```bash
# Check product_id
php artisan tinker
>>> $order = App\Models\Order::latest()->first()
>>> echo $order->product_id
>>> echo $order->product ? $order->product->name : 'Product deleted'
```

## 🎯 Files Modified

1. ✅ `app/Http/Controllers/Customer/OrderController.php`
   - Changed `plan` to `product` in relationships
   - Added logging

2. ✅ `resources/views/customer/orders/index.blade.php`
   - Complete redesign
   - Changed `$order->plan` to `$order->product`
   - Mobile responsive
   - Better UI/UX

3. ✅ `app/Listeners/HandlePaddleTransaction.php` (already correct)
   - Creates order with `product_id`
   - Logs user_id

## 📝 Next Steps

Setelah deploy:
1. Monitor logs untuk setiap payment
2. Verify user_id correct di log
3. Test dengan actual payment
4. Check customer dapat melihat order mereka

## 🚀 Deploy Commands

```bash
git pull origin master
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```
