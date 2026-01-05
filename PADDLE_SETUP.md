# Paddle Integration Setup Guide

## Overview
This application uses Paddle as the payment gateway for processing transactions. Paddle provides a complete payment solution with built-in tax calculation, fraud prevention, and global payment methods.

## Prerequisites
1. Paddle account (Sandbox for testing, Live for production)
2. API Key from Paddle Dashboard
3. Client-side Token from Paddle Dashboard

## Configuration Steps

### 1. Environment Variables
Add the following to your `.env` file:

```env
# Paddle Configuration
PADDLE_ENVIRONMENT=sandbox
PADDLE_API_KEY=pdl_sdbx_apikey_your_key_here
PADDLE_CLIENT_TOKEN=test_your_token_here
PADDLE_WEBHOOK_SECRET=pdl_ntfset_your_secret_here
```

**For Production:**
```env
PADDLE_ENVIRONMENT=live
PADDLE_API_KEY=pdl_live_apikey_your_key_here
PADDLE_CLIENT_TOKEN=live_your_token_here
PADDLE_WEBHOOK_SECRET=pdl_ntfset_your_secret_here
```

### 2. Get Paddle Credentials

#### API Key
1. Go to [Paddle Dashboard](https://vendors.paddle.com/) → Developer Tools → Authentication
2. Click "New API Key"
3. Set permissions (at minimum: `transaction.read`, `transaction.write`, `customer.read`, `customer.write`)
4. Copy the API key (starts with `pdl_sdbx_apikey_` for sandbox or `pdl_live_apikey_` for live)

#### Client-side Token
1. Go to Paddle Dashboard → Developer Tools → Authentication
2. Click "Client-side tokens" tab
3. Create a new token or copy existing one
4. Token starts with `test_` for sandbox or `live_` for production

#### Webhook Secret
1. Go to Paddle Dashboard → Developer Tools → Notifications
2. Create a new notification destination
3. Set URL to: `https://yourdomain.com/webhook/paddle`
4. Copy the webhook secret (starts with `pdl_ntfset_`)

### 3. Create Products and Prices in Paddle

Before customers can checkout, you need to create products and prices in Paddle:

#### Via Paddle Dashboard:
1. Go to Paddle Dashboard → Catalog → Products
2. Click "New Product"
3. Fill in product details
4. Create prices for the product
5. Copy the Price ID (starts with `pri_`)

#### Via API (Optional):
```php
use App\Services\PaddleService;

$paddle = app(PaddleService::class);

// Create Product
$product = $paddle->createProduct([
    'name' => 'CF7 to WhatsApp Plugin',
    'description' => 'WordPress plugin for WhatsApp integration',
    'tax_category' => 'standard'
]);

// Create Price
$price = $paddle->createPrice([
    'product_id' => $product['data']['id'],
    'description' => 'Single Site License',
    'unit_price' => [
        'amount' => '9900', // $99.00 in cents
        'currency_code' => 'USD'
    ],
    'billing_cycle' => null // One-time payment
]);
```

### 4. Update Plans Table

Add Paddle Price IDs to your plans:

```php
use App\Models\Plan;

$plan = Plan::where('slug', 'single-site')->first();
$plan->update([
    'paddle_price_id' => 'pri_01abc123xyz', // From Paddle Dashboard
    'paddle_product_id' => 'pro_01def456uvw' // Optional
]);
```

### 5. Test the Integration

#### Sandbox Testing:
1. Use test card numbers from [Paddle Test Cards](https://developer.paddle.com/concepts/payment-methods/credit-debit-card#test-card-numbers)
2. Test card: `4242 4242 4242 4242`
3. Any future expiry date
4. Any 3-digit CVC

#### Webhook Testing:
1. Use [Paddle Webhook Simulator](https://developer.paddle.com/webhooks/overview#test-webhooks)
2. Or use ngrok for local testing:
   ```bash
   ngrok http 8000
   ```
3. Update webhook URL in Paddle Dashboard to ngrok URL

## Checkout Flow

1. **User selects a plan** → Redirected to checkout page
2. **User fills form** → Customer data collected
3. **Click "Proceed to Payment"** → Paddle.js overlay opens
4. **User completes payment** → Paddle processes transaction
5. **Webhook received** → Order status updated
6. **User redirected** → Success page with license key

## Webhook Events

The application listens for these Paddle webhook events:

- `transaction.completed` - Payment successful
- `transaction.paid` - Payment confirmed
- `transaction.payment_failed` - Payment failed
- `subscription.created` - Subscription started
- `subscription.canceled` - Subscription cancelled

## Security

- API keys are stored in `.env` and never exposed to frontend
- Client-side tokens are safe to use in JavaScript
- Webhook signatures are verified before processing
- All payments processed through Paddle's secure infrastructure

## Troubleshooting

### Checkout overlay doesn't open
- Check browser console for errors
- Verify `PADDLE_CLIENT_TOKEN` is correct
- Ensure Paddle.js script is loaded

### Webhook not received
- Verify webhook URL is publicly accessible
- Check webhook secret matches
- Review Paddle Dashboard → Developer Tools → Events

### Transaction not found
- Ensure `paddle_price_id` is set on the plan
- Check Paddle Dashboard for transaction status
- Review application logs

## Resources

- [Paddle Documentation](https://developer.paddle.com/)
- [Paddle.js Reference](https://developer.paddle.com/paddlejs/overview)
- [Webhook Reference](https://developer.paddle.com/webhooks/overview)
- [API Reference](https://developer.paddle.com/api-reference/overview)
