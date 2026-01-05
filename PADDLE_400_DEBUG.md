# Paddle 400 Error Troubleshooting Guide

## Current Issue
Paddle returns 400 Bad Request when trying to open checkout overlay.

## Possible Causes & Solutions

### 1. Invalid Paddle Price ID
**Check:**
```bash
# In your .env file
PADDLE_SELLER_ID=your_seller_id
PADDLE_API_KEY=your_api_key
PADDLE_CLIENT_TOKEN=your_client_token
```

**Verify in Paddle Dashboard:**
- Go to: https://sandbox-vendors.paddle.com/products
- Check if Price ID exists and is active
- Make sure it's a SANDBOX price ID (starts with `pri_`)

### 2. Paddle Environment Mismatch
**Check:**
```php
// config/cashier.php
'sandbox' => env('PADDLE_SANDBOX', true), // Should be true for testing
```

**Verify:**
- Using sandbox credentials with sandbox=true
- Using production credentials with sandbox=false
- NOT mixing sandbox and production

### 3. Customer Email Already Exists
Paddle might reject if customer email already has transactions.

**Solution:**
- Use a different email for testing
- Or clear test data in Paddle dashboard

### 4. Price/Product Configuration
**Check in Paddle Dashboard:**
- Product is active
- Price is active
- Currency is set correctly
- No minimum/maximum quantity restrictions

### 5. API Credentials
**Test credentials:**
```bash
# Run this in tinker
php artisan tinker

# Then:
$user = User::first();
$checkout = $user->checkout(['pri_01jf6xxxxxxx']);
dd($checkout);
```

### 6. Simplest Test
Create a minimal test:

```php
// In CheckoutController
public function show(Product $product) {
    $user = Auth::user();
    
    // Absolute minimum
    $checkout = $user->checkout([$product->paddle_price_id]);
    
    return view('checkout.show', compact('product', 'checkout'));
}
```

## Debug Steps

### Step 1: Verify Price ID
```bash
# Check product in database
php artisan tinker
Product::first()->paddle_price_id
```

### Step 2: Check Paddle Dashboard
- Login to https://sandbox-vendors.paddle.com
- Products → Your Product → Prices
- Copy the Price ID
- Make sure it's active

### Step 3: Update Product
```bash
php artisan tinker
$product = Product::first();
$product->paddle_price_id = 'pri_01jf6xxxxxxx'; // Your actual price ID
$product->save();
```

### Step 4: Test with Different User
```bash
# Create new test user
php artisan tinker
$user = User::create([
    'name' => 'Test User',
    'email' => 'test' . time() . '@example.com',
    'password' => bcrypt('password'),
]);
```

### Step 5: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Step 6: Check Paddle Logs
- Paddle Dashboard → Developer Tools → Logs
- Look for failed requests

## Common Fixes

### Fix 1: Reset Paddle Configuration
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Fix 2: Verify .env
```env
PADDLE_SELLER_ID=12345
PADDLE_API_KEY=your_api_key
PADDLE_CLIENT_TOKEN=your_client_token
PADDLE_SANDBOX=true
```

### Fix 3: Reinstall Cashier
```bash
composer require laravel/cashier-paddle
php artisan vendor:publish --tag=cashier-migrations
```

## Testing Checklist

- [ ] Paddle credentials are correct
- [ ] Using sandbox mode for testing
- [ ] Price ID exists in Paddle dashboard
- [ ] Price ID is active
- [ ] Product is active
- [ ] User email is valid
- [ ] No custom data conflicts
- [ ] CSP headers allow Paddle iframe
- [ ] JavaScript console shows no errors (except the 400)

## If Still Failing

Contact Paddle Support with:
1. Seller ID
2. Price ID
3. Error message
4. Request payload (from Network tab)
5. Timestamp of failed request
