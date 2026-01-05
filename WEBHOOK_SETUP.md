# Paddle Webhook Setup Guide

## 🔗 Webhook URL

Your Paddle webhook URL is:
```
https://store.mutekar.com/paddle/webhook
```

This is automatically created by Laravel Cashier.

---

## 📋 Setup Steps

### 1. Configure Paddle Dashboard

**For Sandbox (Testing):**
1. Go to: https://sandbox-vendors.paddle.com
2. **Developer Tools** → **Notifications**
3. Click **"Add Notification Destination"**
4. Fill in:
   - **Destination URL:** `https://store.mutekar.com/paddle/webhook`
   - **Description:** "Production Webhook"
   - **Active:** ✅ Yes

**Select these events:**
- ✅ `transaction.completed` - Payment successful
- ✅ `transaction.updated` - Transaction status changed
- ✅ `subscription.created` - New subscription (if using subscriptions)
- ✅ `subscription.updated` - Subscription changed
- ✅ `subscription.canceled` - Subscription cancelled

5. Click **Save**

---

### 2. Get Webhook Secret

1. In Paddle Dashboard → **Developer Tools** → **Notifications**
2. Click on your webhook destination
3. Copy the **Webhook Secret Key**
4. Add to `.env`:
   ```env
   PADDLE_WEBHOOK_SECRET=your_webhook_secret_here
   ```

5. Clear config:
   ```bash
   php artisan config:clear
   ```

---

### 3. Register Event Listener

Add to `bootstrap/app.php` or `routes/web.php`:

```php
use Laravel\Paddle\Events\TransactionCompleted;
use App\Listeners\HandlePaddleTransactionCompleted;

// In bootstrap/app.php
Event::listen(
    TransactionCompleted::class,
    HandlePaddleTransactionCompleted::class
);
```

Or create `app/Providers/EventServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Paddle\Events\TransactionCompleted;
use App\Listeners\HandlePaddleTransactionCompleted;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TransactionCompleted::class => [
            HandlePaddleTransactionCompleted::class,
        ],
    ];
}
```

Then register in `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\EventServiceProvider::class,
],
```

---

## 🧪 Testing Webhook

### Test in Sandbox:

1. **Make a test purchase** using sandbox mode
2. **Check Paddle Dashboard:**
   - Developer Tools → Events
   - Look for `transaction.completed` event
   - Check if webhook was sent successfully

3. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   
   You should see:
   ```
   [timestamp] local.INFO: Paddle transaction completed {"order_id":1,"transaction_id":"txn_xxx"}
   ```

4. **Check Database:**
   ```bash
   php artisan tinker
   Order::latest()->first()
   License::latest()->first()
   ```

---

## 🔍 Debugging Webhook

### If webhook not working:

1. **Check URL is accessible:**
   ```bash
   curl -X POST https://store.mutekar.com/paddle/webhook
   ```
   Should return 200 or 405 (method not allowed is OK)

2. **Check Paddle logs:**
   - Paddle Dashboard → Developer Tools → Events
   - Click on event → View webhook attempts
   - Check response code and error message

3. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Verify webhook secret:**
   - Make sure `PADDLE_WEBHOOK_SECRET` in `.env` matches Paddle Dashboard

5. **Test webhook manually:**
   Use Paddle's webhook testing tool in Dashboard

---

## 📝 Webhook Events Available

Laravel Cashier Paddle fires these events:

| Event | Description | When to Use |
|-------|-------------|-------------|
| `TransactionCompleted` | Payment successful | Create order & license |
| `TransactionUpdated` | Transaction changed | Update order status |
| `SubscriptionCreated` | New subscription | Create subscription record |
| `SubscriptionUpdated` | Subscription changed | Update subscription |
| `SubscriptionCanceled` | Subscription ended | Deactivate license |

---

## 🎯 What Happens in Webhook Handler

When payment is successful:

1. ✅ Receive `transaction.completed` event
2. ✅ Extract custom data (product_id, user_id, whatsapp_number)
3. ✅ Create/Update Order in database
4. ✅ Generate License Key (format: XXXX-XXXX-XXXX-XXXX)
5. ✅ Save License to database
6. ✅ Log success
7. 📧 Send email (TODO)
8. 💬 Send WhatsApp notification (TODO)

---

## 🚀 Next Steps

1. ✅ Setup webhook URL in Paddle Dashboard
2. ✅ Add webhook secret to `.env`
3. ✅ Register event listener
4. ✅ Test with sandbox purchase
5. ⏳ Add email notifications
6. ⏳ Add WhatsApp notifications
7. ⏳ Add download link generation

---

## 📞 Support

If webhook not working:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Paddle event logs in Dashboard
3. Verify URL is publicly accessible
4. Check webhook secret matches
5. Contact Paddle Support if needed
