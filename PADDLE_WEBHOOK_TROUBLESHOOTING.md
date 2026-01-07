# Paddle Webhook Integration - Troubleshooting Guide

## ✅ Masalah yang Sudah Diperbaiki

### 1. Database Schema Issues
**Problem:** Field `plan_id` tidak nullable di tabel `orders` dan `licenses`, menyebabkan error saat webhook membuat order baru.

**Solution:** 
- Created migration: `2026_01_07_013122_make_plan_id_nullable_in_orders_table.php`
- Created migration: `2026_01_07_013353_make_plan_id_nullable_in_licenses_table.php`
- Field `plan_id` sekarang nullable karena sistem menggunakan `product_id` sebagai primary reference

### 2. Webhook Processing
**Status:** ✅ **WORKING**

Webhook dari Paddle sekarang berhasil:
- ✅ Menerima webhook `transaction.completed`
- ✅ Membuat order baru dari webhook data
- ✅ Generate license key otomatis
- ✅ Dispatch `PaymentCompleted` event
- ✅ Mengirim email notification dengan attachment plugin

**Log Evidence:**
```
[2026-01-07 01:34:16] local.INFO: Paddle Webhook Received
[2026-01-07 01:34:16] local.INFO: Processing Paddle webhook {"event_type":"transaction.completed"}
[2026-01-07 01:34:16] local.INFO: Order created from webhook successfully
[2026-01-07 01:34:17] local.INFO: Dispatching PaymentCompleted event
[2026-01-07 01:34:17] local.INFO: Processing PaymentCompleted event for notifications
[2026-01-07 01:34:17] local.INFO: License generated for order
[2026-01-07 01:34:19] local.INFO: Payment completed email sent successfully
```

## ⚠️ WhatsApp Notification - Requires Configuration

**Status:** ⚠️ **NOT CONFIGURED**

**Error:**
```
WhatsApp API error: "Invali api_key or sender,please check again (2)"
```

**Root Cause:** 
WhatsApp credentials belum dikonfigurasi di database settings.

### How to Configure WhatsApp Notifications:

1. **Login to Admin Dashboard**
2. **Navigate to Settings**
3. **Fill in WhatsApp Configuration:**

| Setting Key | Value | Example |
|------------|-------|---------|
| `whatsapp_enabled` | `true` | Enable WhatsApp notifications |
| `whatsapp_api_url` | Your API URL | `https://mpwa.mutekar.com/send-message` |
| `whatsapp_api_key` | Your API Key | Get from your WhatsApp Gateway provider |
| `whatsapp_sender` | Sender Number | `6282117501815` |
| `whatsapp_admin_number` | Admin Number/Group | `120363166537946168@g.us` (for group) or `628123456789` (for individual) |

4. **Save Settings**
5. **Test with a new transaction**

### WhatsApp Notification Features:

When configured, the system will automatically send WhatsApp messages for:

**To Customer:**
- ✅ Payment success notification
- ✅ License key
- ✅ Download instructions
- ✅ Activation steps

**To Admin:**
- ✅ New payment received
- ✅ Order details
- ✅ Customer information

## 🔧 Paddle Webhook Configuration

### Webhook URL:
```
https://yourdomain.com/webhook/paddle
```

### Required Events:
- ✅ `transaction.completed` - When payment is successful
- ⚠️ `transaction.payment_failed` - When payment fails (optional)
- ⚠️ `transaction.refunded` - When payment is refunded (optional)

### Webhook Payload Structure:
The webhook expects this structure from Paddle:
```json
{
  "event_type": "transaction.completed",
  "data": {
    "id": "txn_xxx",
    "customer": {
      "email": "customer@example.com",
      "name": "Customer Name"
    },
    "custom_data": {
      "product_id": "1",
      "user_id": "2",
      "customer_name": "Customer Name",
      "whatsapp_number": "628123456789"
    },
    "details": {
      "totals": {
        "total": 4900
      }
    },
    "currency_code": "USD"
  }
}
```

## 🧪 Testing

### Local Testing Script:
Use `test-paddle-webhook.php` to simulate Paddle webhook:

```bash
php test-paddle-webhook.php
```

**Expected Result:**
```
Response Code: 200
Response Body: Webhook received
```

### Check Logs:
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Paddle Webhook Received`
- `Order created from webhook successfully`
- `Payment completed email sent successfully`
- `WhatsApp notification sent` (if configured)

## 📊 Current Status Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Webhook Reception | ✅ Working | Receives Paddle webhooks |
| Order Creation | ✅ Working | Creates order from webhook data |
| License Generation | ✅ Working | Auto-generates license key |
| Email Notification | ✅ Working | Sends email with plugin attachment |
| WhatsApp to Customer | ⚠️ Needs Config | Requires WhatsApp settings |
| WhatsApp to Admin | ⚠️ Needs Config | Requires WhatsApp settings |

## 🚀 Next Steps

1. **Configure Paddle Webhook URL** in Paddle Dashboard
2. **Configure WhatsApp Settings** in Admin Dashboard (optional but recommended)
3. **Test with Real Transaction** from Paddle
4. **Monitor Logs** for any issues

## 📝 Notes

- CSRF protection is disabled for `/webhook/paddle` route
- Webhook returns 200 even on errors to prevent Paddle retries
- All errors are logged in `storage/logs/laravel.log`
- Email uses configured SMTP settings
- WhatsApp uses MPWA API format

## 🔍 Troubleshooting

### If webhook is not received:
1. Check Paddle Dashboard webhook configuration
2. Verify webhook URL is accessible from internet
3. Check firewall/security settings
4. Review `storage/logs/laravel.log` for errors

### If email is not sent:
1. Check SMTP configuration in `.env`
2. Verify `MAIL_FROM_ADDRESS` is set
3. Check email logs in `storage/logs/laravel.log`

### If WhatsApp is not sent:
1. Verify WhatsApp settings in Admin Dashboard
2. Check API key and sender number are correct
3. Test API endpoint manually
4. Review WhatsApp API logs

---

**Last Updated:** 2026-01-07
**Status:** Webhook Integration Working ✅
**Pending:** WhatsApp Configuration ⚠️
