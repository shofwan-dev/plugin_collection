# Admin Guide - CF7 WhatsApp Gateway

## Overview

This guide covers all administrative functions for managing the CF7 WhatsApp Gateway license system.

## 📋 Table of Contents

1. [Accessing Admin Panel](#accessing-admin-panel)
2. [Dashboard Overview](#dashboard-overview)
3. [Managing Licenses](#managing-licenses)
4. [Managing Orders](#managing-orders)
5. [System Settings](#system-settings)
6. [WhatsApp Configuration](#whatsapp-configuration)
7. [Monitoring & Logs](#monitoring--logs)
8. [Best Practices](#best-practices)

---

## Accessing Admin Panel

### Login

1. Go to `/login`
2. Enter admin credentials
3. You'll be redirected to `/admin`

### Default Credentials

**⚠️ IMPORTANT**: Change these immediately after first login!

- Email: `admin@cf7whatsapp.com`
- Password: `password`

### Changing Admin Password

1. Click your name in top-right
2. Select **"Profile"**
3. Update password
4. Save changes

---

## Dashboard Overview

The admin dashboard (`/admin`) shows:

### Statistics Cards

- **Total Orders**: All orders in the system
- **Total Revenue**: Sum of all completed orders
- **Active Licenses**: Currently active licenses
- **Total Licenses**: All licenses generated

### Recent Activity

- **Recent Orders**: Last 10 orders with status
- **Recent Licenses**: Last 10 licenses generated

### Quick Actions

- View all licenses
- View all orders
- Access settings

---

## Managing Licenses

### View All Licenses

**Path**: `/admin/licenses`

**Features**:
- Search by license key
- Filter by status (active, suspended, expired)
- Pagination (20 per page)
- Sort by date

### License Details

Click any license to view:

- **License Information**
  - License key
  - Plan name
  - Status
  - Creation date
  - Expiration date

- **Domain Information**
  - Activated domains list
  - Activation count vs. limit
  - Last activation date

- **Order Information**
  - Related order number
  - Customer details
  - Purchase date

### License Actions

#### Suspend License

Use when:
- Customer violated terms
- Payment dispute
- Fraud detected

**Steps**:
1. Go to license details
2. Click **"Suspend License"**
3. Confirm action
4. License status changes to "suspended"
5. Plugin will stop working on all domains

#### Activate License

Use to restore suspended license:

1. Go to license details
2. Click **"Activate License"**
3. License status changes to "active"
4. Plugin resumes working

#### Deactivate Domain

Remove a specific domain from license:

1. Go to license details
2. Find the domain in "Activated Domains"
3. Click **"Deactivate"** next to domain
4. Confirm action
5. Domain is removed from license

**Use Cases**:
- Customer changed domains
- Domain no longer in use
- Free up activation slot

---

## Managing Orders

### View All Orders

**Path**: `/admin/orders`

**Features**:
- Search by order number or email
- Filter by status
- Filter by payment status
- Pagination

### Order Details

Click any order to view:

- **Order Information**
  - Order number
  - Customer name & email
  - Plan purchased
  - Amount
  - Currency
  - Order date

- **Payment Information**
  - Payment status
  - Stripe session ID
  - Payment intent ID
  - Paid date

- **License Information**
  - Generated license key
  - License status
  - Activation details

### Order Actions

#### Update Order Status

**Path**: Order details page

**Available Statuses**:
- **Pending**: Awaiting action
- **Confirmed**: Order confirmed
- **Processing**: Being processed
- **Completed**: Finished
- **Cancelled**: Cancelled by customer or admin

**Steps**:
1. Select new status from dropdown
2. Click **"Update Status"**
3. WhatsApp notification sent automatically (if enabled)

#### Update Payment Status

**Available Statuses**:
- **Pending**: Awaiting payment
- **Partial**: Partially paid
- **Paid**: Fully paid
- **Failed**: Payment failed
- **Expired**: Payment link expired
- **Refunded**: Payment refunded

**Steps**:
1. Select new payment status
2. Click **"Update Payment Status"**
3. WhatsApp notification sent based on status:
   - **Paid**: Success notification
   - **Refunded**: Refund notification
   - **Expired**: Expiration notification

**⚠️ Important**: Changing to "Paid" will NOT generate a license automatically. Use Stripe webhooks for automatic license generation.

---

## System Settings

### Access Settings

**Path**: `/admin/settings`

### General Settings

Configure basic site information:

- **Site Name**: Display name for your site
- **Site Description**: Brief description
- **Contact Email**: Support email address
- **Contact Phone**: Support phone number

### WhatsApp Settings

Configure WhatsApp API integration:

#### Required Fields

1. **API URL**
   - Your WhatsApp API endpoint
   - Example: `https://api.whatsapp-service.com/send`

2. **API Key**
   - Your API authentication key
   - Keep this secure!

3. **Sender Number**
   - WhatsApp number for sending messages
   - Format: `6281234567890` (no + or spaces)

4. **Admin Number**
   - Number to receive admin notifications
   - Can be personal number or group ID
   - **Personal**: `6281234567890`
   - **Group**: `120363166537946168@g.us`

5. **Enable WhatsApp**
   - Toggle to enable/disable notifications
   - Useful for testing or maintenance

#### Getting WhatsApp Group ID

1. Add bot to WhatsApp group
2. Send any message in group
3. Check bot logs for group ID
4. Format: `[numbers]@g.us`

#### Test WhatsApp Connection

After configuration:

1. Click **"Send Test Message"**
2. Check admin WhatsApp for test message
3. If successful, you'll see success notification
4. If failed, check:
   - API URL is correct
   - API key is valid
   - Admin number is correct format
   - WhatsApp is enabled

### Email Settings

Configure email notifications:

- **From Address**: Email sender address
- **From Name**: Email sender name

**Note**: SMTP settings are in `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

---

## WhatsApp Configuration

### Notification Types

#### Customer Notifications

1. **Order Created**
   - Sent when customer places order
   - Includes order details and payment link
   - Reminds to complete payment in 24 hours

2. **Payment Success**
   - Sent when payment is confirmed
   - Includes license key
   - Provides activation instructions

3. **Payment Expired**
   - Sent when payment link expires
   - Instructs to contact admin

4. **Payment Refunded**
   - Sent when refund is processed
   - Explains refund timeline

5. **Order Status Update**
   - Sent when order status changes
   - Custom message per status

#### Admin Notifications

1. **New Order**
   - Sent when customer creates order
   - Includes customer and order details
   - Link to admin panel

2. **Payment Received**
   - Sent when payment is successful
   - Includes payment details
   - Confirms license generation

### Message Customization

Messages are defined in `WhatsAppService.php`. To customize:

1. Edit `app/Services/WhatsAppService.php`
2. Find the relevant method (e.g., `sendOrderCreatedNotification`)
3. Modify the `$message` variable
4. Save and test

### Supported WhatsApp Formats

- **Bold**: `*text*`
- **Italic**: `_text_`
- **Strikethrough**: `~text~`
- **Monospace**: `` `text` ``
- **Line break**: `\n`

---

## Monitoring & Logs

### Application Logs

**Location**: `storage/logs/laravel.log`

**What's Logged**:
- API requests (IP, endpoint, response time)
- License activations/validations
- Payment events
- WhatsApp notifications
- Errors and exceptions

### Viewing Logs

```bash
# View last 100 lines
tail -n 100 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Search for errors
grep "ERROR" storage/logs/laravel.log
```

### API Monitoring

Each API request logs:
- Method and URL
- IP address
- User agent
- Request data (sanitized)
- Response code
- Response time (ms)

### Key Metrics to Monitor

1. **API Response Time**
   - Should be < 500ms
   - Check `X-Response-Time` header

2. **Payment Success Rate**
   - Track completed vs. failed payments
   - Investigate failures

3. **WhatsApp Delivery Rate**
   - Check logs for failed notifications
   - Verify API connectivity

4. **License Activation Rate**
   - Monitor successful activations
   - Track validation failures

---

## Best Practices

### Security

1. **Change Default Password**
   - Immediately after first login
   - Use strong, unique password

2. **Regular Backups**
   - Database: Daily
   - Files: Weekly
   - Test restore procedure

3. **Monitor Suspicious Activity**
   - Multiple failed login attempts
   - Unusual API usage patterns
   - Suspicious order patterns

4. **Keep Software Updated**
   - Laravel framework
   - PHP version
   - Dependencies

### License Management

1. **Regular Audits**
   - Review active licenses monthly
   - Check for expired licenses
   - Verify domain activations

2. **Handle Disputes Promptly**
   - Respond to customer issues quickly
   - Document all actions
   - Communicate clearly

3. **Fraud Prevention**
   - Monitor for duplicate orders
   - Check for suspicious patterns
   - Verify high-value orders

### Customer Support

1. **Response Time**
   - Aim for < 24 hours
   - Prioritize payment issues
   - Be professional and helpful

2. **Common Issues**
   - License activation problems
   - Payment failures
   - Domain limit reached
   - Expired licenses

3. **Documentation**
   - Keep user guide updated
   - Document common solutions
   - Create FAQ section

### System Maintenance

1. **Regular Tasks**
   - Clear old logs (monthly)
   - Optimize database (monthly)
   - Review error logs (weekly)
   - Test backups (monthly)

2. **Performance Optimization**
   ```bash
   # Clear and rebuild caches
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Database Optimization**
   ```bash
   # Optimize tables
   php artisan db:optimize
   ```

---

## Troubleshooting

### WhatsApp Not Sending

1. Check settings are saved
2. Verify "Enable WhatsApp" is ON
3. Test connection
4. Check API logs
5. Verify API credits/balance

### License Not Generated

1. Check Stripe webhook is configured
2. Verify webhook secret in `.env`
3. Check webhook logs in Stripe dashboard
4. Review Laravel logs for errors

### Payment Issues

1. Verify Stripe keys are correct
2. Check Stripe dashboard for events
3. Review webhook delivery
4. Check order status in database

---

## Support Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Stripe Documentation**: https://stripe.com/docs
- **System Logs**: `storage/logs/laravel.log`
- **Database**: Use phpMyAdmin or Adminer

---

**Need Help?**  
Contact: admin@cf7whatsapp.com

**Last Updated**: 2026-01-04  
**Version**: 1.0.0
