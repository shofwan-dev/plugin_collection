# Quick Paddle Setup for Testing

## For Development/Testing (Sandbox)

Add these to your `.env` file:

```env
PADDLE_ENVIRONMENT=sandbox
PADDLE_API_KEY=test_key_placeholder
PADDLE_CLIENT_TOKEN=test_token_placeholder
PADDLE_WEBHOOK_SECRET=test_secret_placeholder
```

**Note:** These are placeholder values. The checkout page will show a warning that Paddle is not configured, but the application will not crash.

## To Get Real Credentials:

### 1. Create Paddle Account
- Go to https://sandbox-vendors.paddle.com/signup (for sandbox)
- Or https://vendors.paddle.com/signup (for live)

### 2. Get API Key
1. Login to Paddle Dashboard
2. Go to **Developer Tools** → **Authentication**
3. Click **"New API Key"**
4. Name it: "Laravel App"
5. Set permissions:
   - ✅ `transaction.read`
   - ✅ `transaction.write`
   - ✅ `customer.read`
   - ✅ `customer.write`
   - ✅ `price.read`
   - ✅ `product.read`
6. Click **Save**
7. Copy the API key (starts with `pdl_sdbx_apikey_` or `pdl_live_apikey_`)
8. Paste into `.env` as `PADDLE_API_KEY`

### 3. Get Client Token
1. In Paddle Dashboard, go to **Developer Tools** → **Authentication**
2. Click **"Client-side tokens"** tab
3. Click **"New client-side token"**
4. Name it: "Laravel Frontend"
5. Click **Save**
6. Copy the token (starts with `test_` or `live_`)
7. Paste into `.env` as `PADDLE_CLIENT_TOKEN`

### 4. Get Webhook Secret
1. In Paddle Dashboard, go to **Developer Tools** → **Notifications**
2. Click **"New notification destination"**
3. Set:
   - **Description:** "Laravel Webhook"
   - **Destination URL:** `https://yourdomain.com/webhook/paddle`
   - **Subscribe to events:** Select all transaction events
4. Click **Save**
5. Copy the webhook secret (starts with `pdl_ntfset_`)
6. Paste into `.env` as `PADDLE_WEBHOOK_SECRET`

### 5. Create Products & Prices

#### Option A: Via Paddle Dashboard
1. Go to **Catalog** → **Products**
2. Click **"New Product"**
3. Fill in:
   - **Name:** "CF7 to WhatsApp - Single Site"
   - **Description:** "WordPress plugin for single site"
   - **Tax category:** "Standard"
4. Click **Save**
5. Click **"Add price"**
6. Fill in:
   - **Description:** "One-time payment"
   - **Amount:** 99.00 USD
   - **Billing cycle:** One-time
7. Click **Save**
8. Copy the **Price ID** (starts with `pri_`)
9. Update your database:
   ```sql
   UPDATE plans SET paddle_price_id = 'pri_your_price_id_here' WHERE slug = 'single-site';
   ```

#### Option B: Via Artisan Command (Coming Soon)
```bash
php artisan paddle:sync-products
```

## Test the Integration

### Test Cards (Sandbox Only)
- **Success:** `4242 4242 4242 4242`
- **Declined:** `4000 0000 0000 0002`
- **Requires Auth:** `4000 0025 0000 3155`

**Expiry:** Any future date  
**CVC:** Any 3 digits  
**ZIP:** Any 5 digits

### Test Checkout Flow
1. Navigate to `/checkout/single-site`
2. Fill in the form
3. Click "Proceed to Secure Payment"
4. Paddle overlay should open
5. Use test card to complete payment
6. Should redirect to success page

## Troubleshooting

### "Paddle Not Configured" Warning
- Check `.env` has `PADDLE_CLIENT_TOKEN` set
- Run `php artisan config:clear`
- Refresh the page

### Checkout Overlay Doesn't Open
- Open browser console (F12)
- Check for JavaScript errors
- Verify `paddle_price_id` is set on the plan
- Ensure Paddle.js script loaded

### Webhook Not Working
- For local development, use ngrok:
  ```bash
  ngrok http 8000
  ```
- Update webhook URL in Paddle to ngrok URL
- Check webhook secret matches

## Production Checklist

Before going live:

- [ ] Change `PADDLE_ENVIRONMENT` to `live`
- [ ] Get live API key and client token
- [ ] Create products in live Paddle account
- [ ] Update all plans with live price IDs
- [ ] Set up production webhook URL
- [ ] Test with real card (small amount)
- [ ] Verify webhook events are received
- [ ] Check license generation works
- [ ] Test email notifications

## Support

For issues:
1. Check `storage/logs/laravel.log`
2. Review Paddle Dashboard → Developer Tools → Events
3. See `PADDLE_SETUP.md` for detailed guide
