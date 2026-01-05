# Paddle Troubleshooting Guide

## Common Errors & Solutions

### 1. "Something went wrong" Error

**Symptoms:**
- Paddle overlay shows "Something went wrong"
- Console errors: `Cannot read properties of undefined (reading 'find')`
- 400 error from `checkout-service.paddle.com`

**Causes:**
- ❌ Invalid or placeholder client token
- ❌ Missing paddle_price_id on plan
- ❌ Invalid price ID format

**Solutions:**

#### A. Configure Real Paddle Credentials
1. Go to `/admin/settings`
2. Scroll to "Paddle Payment Gateway"
3. Enter **real** credentials from Paddle Dashboard
4. Click "Test Connection" to verify

#### B. Set Paddle Price IDs
```sql
-- Update plan with real Paddle price ID
UPDATE plans 
SET paddle_price_id = 'pri_01abc123xyz'  -- Get from Paddle Dashboard
WHERE slug = '5-sites';
```

Or via Tinker:
```bash
php artisan tinker
```
```php
$plan = App\Models\Plan::where('slug', '5-sites')->first();
$plan->update(['paddle_price_id' => 'pri_01abc123xyz']);
```

### 2. Livewire.js 404 Error

**Error:** `Failed to load resource: livewire.js:1 404 (Not Found)`

**Cause:** Livewire is not installed (not required for Paddle)

**Solution:** This error can be ignored - it doesn't affect Paddle functionality.

### 3. Client Token Not Configured

**Warning:** "Paddle client token is not configured"

**Solution:**
1. Get client token from Paddle Dashboard
2. Go to `/admin/settings`
3. Enter token in "Client Token" field
4. Save settings

### 4. Plan Missing Price ID

**Warning:** "This plan does not have a Paddle price ID"

**Solution:**
1. Create product & price in Paddle Dashboard
2. Copy the Price ID (starts with `pri_`)
3. Update plan in database (see solution 1B above)

### 5. Paddle Initialization Failed

**Error:** `Paddle initialization error` in console

**Causes:**
- Invalid client token format
- Token from wrong environment (sandbox vs live)
- Network connectivity issues

**Solutions:**
1. Verify token format: `test_xxx` (sandbox) or `live_xxx` (production)
2. Check environment matches token type
3. Test connection in admin settings

## Step-by-Step Setup

### For Testing (Sandbox)

1. **Create Paddle Sandbox Account**
   - Go to https://sandbox-vendors.paddle.com/signup
   - Complete registration

2. **Get Credentials**
   ```
   Developer Tools → Authentication
   
   ✅ API Key: pdl_sdbx_apikey_xxx
   ✅ Client Token: test_xxx
   ✅ Webhook Secret: pdl_ntfset_xxx
   ```

3. **Configure in Admin**
   - Navigate to `/admin/settings`
   - Fill in Paddle section
   - Environment: `sandbox`
   - Save settings
   - Click "Test Connection"

4. **Create Test Product**
   ```
   Paddle Dashboard → Catalog → Products
   
   - Name: "Test Product"
   - Price: $1.00 (for testing)
   - Copy Price ID: pri_xxx
   ```

5. **Update Plan**
   ```bash
   php artisan tinker
   ```
   ```php
   App\Models\Plan::where('slug', '5-sites')
       ->update(['paddle_price_id' => 'pri_xxx']);
   ```

6. **Test Checkout**
   - Go to `/checkout/5-sites`
   - Fill form
   - Click "Proceed to Payment"
   - Use test card: `4242 4242 4242 4242`

### For Production

1. **Switch to Live Account**
   - Get live credentials from Paddle
   - Update environment to `live`
   - Update all credentials

2. **Create Real Products**
   - Create products with real prices
   - Update all plans with live price IDs

3. **Configure Webhook**
   - Set webhook URL: `https://yourdomain.com/webhook/paddle`
   - Copy webhook secret
   - Update in settings

4. **Test with Real Card**
   - Use small amount first
   - Verify entire flow
   - Check webhook events

## Validation Checklist

Before going live, verify:

- [ ] Environment set to `live`
- [ ] Live API key configured
- [ ] Live client token configured
- [ ] Webhook secret configured
- [ ] Webhook URL accessible
- [ ] All plans have live price IDs
- [ ] Test checkout completes successfully
- [ ] Webhooks are received
- [ ] Orders are created
- [ ] Licenses are generated
- [ ] Emails are sent

## Debug Mode

To see detailed errors in console:

```javascript
// Add to checkout page temporarily
console.log('Client Token:', clientToken);
console.log('Price ID:', priceId);
console.log('Paddle object:', window.Paddle);
```

## Getting Help

1. **Check Logs**
   - Laravel: `storage/logs/laravel.log`
   - Browser Console (F12)
   - Paddle Dashboard → Events

2. **Test Connection**
   - `/admin/settings` → Test Connection button
   - Should return success with environment

3. **Verify Configuration**
   ```bash
   php artisan tinker
   ```
   ```php
   // Check settings
   App\Models\Setting::where('group', 'paddle')->get();
   
   // Check plan
   App\Models\Plan::where('slug', '5-sites')
       ->first(['paddle_price_id', 'paddle_product_id']);
   ```

4. **Contact Support**
   - Paddle Support: https://paddle.com/support
   - Check Paddle Status: https://status.paddle.com

## Quick Fixes

### Reset Paddle Settings
```bash
php artisan tinker
```
```php
App\Models\Setting::where('group', 'paddle')->delete();
```

### Set Test Credentials
```bash
php artisan tinker
```
```php
App\Models\Setting::set('paddle_environment', 'sandbox', 'string', 'paddle');
App\Models\Setting::set('paddle_client_token', 'test_your_token', 'string', 'paddle');
App\Models\Setting::set('paddle_api_key', 'pdl_sdbx_apikey_xxx', 'string', 'paddle');
```

### Clear Config Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## Resources

- [Paddle Documentation](https://developer.paddle.com/)
- [Paddle.js Reference](https://developer.paddle.com/paddlejs/overview)
- [Test Cards](https://developer.paddle.com/concepts/payment-methods/credit-debit-card#test-card-numbers)
- [Webhook Events](https://developer.paddle.com/webhooks/overview)
