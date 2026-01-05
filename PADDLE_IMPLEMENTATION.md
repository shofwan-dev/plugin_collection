# Paddle Integration - Implementation Notes

## Our Implementation vs Laravel Cashier

### What We're Using:
- **Paddle.js** (Frontend) - For checkout overlay
- **Paddle API** (Backend) - For transaction management
- **Custom Implementation** - No Laravel Cashier package

### Why Not Cashier?
Laravel Cashier Paddle is designed for **subscription management**. Our use case is:
- ✅ **One-time payments** for licenses
- ✅ **Simple checkout** without recurring billing
- ✅ **Direct Paddle.js integration**
- ❌ No need for subscription features

## Current Implementation

### 1. Frontend (Paddle.js)
```javascript
// Initialize Paddle
Paddle.Initialize({
    token: 'your_client_token'
});

// Open checkout
Paddle.Checkout.open({
    items: [{
        priceId: 'pri_xxx',
        quantity: 1
    }],
    customer: {
        email: 'customer@example.com'
    },
    settings: {
        successUrl: '/checkout/success',
        closeUrl: '/checkout/cancel'
    }
});
```

### 2. Backend (PaddleService)
- Reads configuration from database settings
- Provides API methods for:
  - Creating transactions
  - Getting transaction details
  - Verifying webhooks

### 3. Webhook Handling
- Receives `transaction.completed` events
- Updates order status
- Generates license keys

## Setup Steps

### 1. Get Paddle Credentials
1. Sign up at https://vendors.paddle.com (or sandbox)
2. Go to Developer Tools → Authentication
3. Create API Key with permissions:
   - `transaction.read`
   - `transaction.write`
   - `customer.read`
   - `customer.write`
4. Create Client-side Token
5. Set up Webhook notification

### 2. Configure in Admin Panel
1. Go to `/admin/settings`
2. Scroll to "Paddle Payment Gateway"
3. Fill in:
   - Environment: `sandbox` or `live`
   - API Key: `pdl_sdbx_apikey_xxx` or `pdl_live_apikey_xxx`
   - Client Token: `test_xxx` or `live_xxx`
   - Webhook Secret: `pdl_ntfset_xxx`
4. Click "Save Paddle Settings"
5. Click "Test Connection" to verify

### 3. Create Products in Paddle
1. Go to Paddle Dashboard → Catalog → Products
2. Create a product (e.g., "CF7 to WhatsApp - Single Site")
3. Add a price (e.g., $99 one-time)
4. Copy the Price ID (starts with `pri_`)
5. Update your plan in database:
   ```sql
   UPDATE plans 
   SET paddle_price_id = 'pri_your_price_id_here' 
   WHERE slug = 'single-site';
   ```

### 4. Test Checkout
1. Navigate to `/checkout/single-site`
2. Fill in customer details
3. Click "Proceed to Secure Payment"
4. Paddle overlay should open
5. Use test card: `4242 4242 4242 4242`
6. Complete payment
7. Should redirect to success page

## Differences from Cashier

| Feature | Cashier | Our Implementation |
|---------|---------|-------------------|
| Package | `laravel/cashier-paddle` | Custom |
| Use Case | Subscriptions | One-time payments |
| Database | Cashier migrations | Custom orders table |
| Billable Trait | Required | Not needed |
| Checkout | `$user->checkout()` | `Paddle.Checkout.open()` |
| Webhooks | Built-in handlers | Custom webhook controller |
| Complexity | High (for subscriptions) | Low (for simple payments) |

## When to Use Cashier

Use Laravel Cashier Paddle if you need:
- ✅ Recurring subscriptions
- ✅ Trial periods
- ✅ Plan swapping
- ✅ Subscription pausing
- ✅ Proration handling
- ✅ Multiple products per subscription

## Our Use Case

We only need:
- ✅ One-time checkout
- ✅ License generation
- ✅ Order tracking
- ✅ Simple payment flow

Therefore, **direct Paddle.js integration is perfect** for our needs!

## Testing

### Sandbox Test Cards
- **Success:** `4242 4242 4242 4242`
- **Declined:** `4000 0000 0000 0002`
- **Requires Auth:** `4000 0025 0000 3155`

**Expiry:** Any future date  
**CVC:** Any 3 digits

### Test Flow
1. Set environment to `sandbox`
2. Use sandbox credentials
3. Create test products in Paddle sandbox
4. Test checkout with test cards
5. Verify webhooks are received
6. Check order status updates

## Production Checklist

- [ ] Change environment to `live`
- [ ] Update API key to live key
- [ ] Update client token to live token
- [ ] Create products in live Paddle account
- [ ] Update plans with live price IDs
- [ ] Configure production webhook URL
- [ ] Test with real card (small amount)
- [ ] Verify license generation
- [ ] Test email notifications

## Support

For issues:
1. Check `/admin/settings` - Test Connection
2. Review `storage/logs/laravel.log`
3. Check Paddle Dashboard → Events
4. See Paddle documentation: https://developer.paddle.com/
