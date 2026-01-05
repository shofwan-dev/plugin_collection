# CF7 to WhatsApp Gateway - License Management System

A comprehensive WordPress plugin license management system with Stripe payment integration and WhatsApp notifications.

## 🚀 Features

- **License Management** - Generate, activate, and manage WordPress plugin licenses
- **Stripe Integration** - Secure payment processing with automatic license generation
- **WhatsApp Notifications** - Real-time notifications for orders and payments
- **Admin Dashboard** - Manage licenses, orders, and system settings
- **Customer Dashboard** - View licenses, orders, and download plugins
- **API Endpoints** - RESTful API for WordPress plugin integration
- **Rate Limiting** - API protection with 60 requests/minute
- **Order Cancellation** - Allow customers to cancel unpaid orders

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js & NPM (for asset compilation)
- Stripe Account
- WhatsApp API Service (optional)

## 🔧 Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/cf7whatsapp-website.git
cd cf7whatsapp-website
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file:

```env
APP_NAME="CF7 WhatsApp Gateway"
APP_URL=https://yourdomain.com

DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 4. Database Setup

```bash
php artisan migrate --seed
```

This will create:
- Admin user: `admin@cf7whatsapp.com` / `password`
- Test customer: `customer@example.com` / `password`
- 3 pricing plans ($49, $99, $199)
- Default settings

### 5. Build Assets

```bash
npm run build
```

### 6. Configure Stripe Webhook

1. Go to Stripe Dashboard → Webhooks
2. Add endpoint: `https://yourdomain.com/webhook/stripe`
3. Select events:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
4. Copy webhook secret to `.env`

### 7. Configure WhatsApp (Optional)

Login as admin and go to `/admin/settings`:

- **API URL**: Your WhatsApp API endpoint
- **API Key**: Your API key
- **Sender Number**: WhatsApp sender number
- **Admin Number**: Admin WhatsApp number or group ID
- **Enable**: Toggle WhatsApp notifications

Test connection using "Send Test Message" button.

## 🎯 Usage

### For Customers

1. **Browse Plans**: Visit `/pricing`
2. **Purchase**: Click "Buy Now" and complete Stripe checkout
3. **View License**: Check `/dashboard/licenses` for license key
4. **Download Plugin**: Download from customer dashboard
5. **Activate**: Use license key in WordPress plugin settings

### For Admins

1. **Dashboard**: View statistics at `/admin`
2. **Manage Licenses**: `/admin/licenses`
   - View all licenses
   - Suspend/activate licenses
   - Deactivate domains
3. **Manage Orders**: `/admin/orders`
   - View all orders
   - Update order status
   - Update payment status
4. **Settings**: `/admin/settings`
   - Configure WhatsApp
   - Configure email
   - General settings

## 🔌 API Documentation

### Base URL
```
https://yourdomain.com/api/v1
```

### Rate Limiting
- 60 requests per minute per IP
- Headers: `X-RateLimit-Limit`, `X-Response-Time`

### Endpoints

#### 1. Activate License
```http
POST /licenses/activate
Content-Type: application/json

{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "example.com",
  "plugin_version": "1.0.0"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "License activated successfully",
  "license": {
    "key": "XXXX-XXXX-XXXX-XXXX",
    "status": "active",
    "expires_at": "2027-01-04"
  }
}
```

#### 2. Validate License
```http
POST /licenses/validate
Content-Type: application/json

{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "example.com"
}
```

**Response (200):**
```json
{
  "valid": true,
  "status": "active",
  "expires_at": "2027-01-04"
}
```

#### 3. Deactivate License
```http
POST /licenses/deactivate
Content-Type: application/json

{
  "license_key": "XXXX-XXXX-XXXX-XXXX",
  "domain": "example.com"
}
```

#### 4. Check License
```http
GET /licenses/check/{license_key}
```

**Response (200):**
```json
{
  "status": "active",
  "plan": "5 Sites",
  "max_domains": 5,
  "activated_domains": 2,
  "remaining_activations": 3,
  "expires_at": "2027-01-04",
  "is_active": true
}
```

### Error Responses

**Validation Error (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "license_key": ["The license key field is required."]
  }
}
```

**Rate Limit (429):**
```json
{
  "message": "Too Many Requests"
}
```

## 📁 Project Structure

```
cf7whatsapp-website/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   ├── Api/            # API controllers
│   │   │   ├── Customer/       # Customer controllers
│   │   │   ├── CheckoutController.php
│   │   │   ├── HomeController.php
│   │   │   └── WebhookController.php
│   │   └── Middleware/
│   │       ├── ApiLogger.php   # API logging
│   │       └── IsAdmin.php     # Admin check
│   ├── Models/
│   │   ├── License.php
│   │   ├── Order.php
│   │   ├── Plan.php
│   │   ├── Setting.php
│   │   └── User.php
│   └── Services/
│       └── WhatsAppService.php # WhatsApp integration
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/              # Admin views
│       ├── customer/           # Customer views
│       ├── checkout/           # Checkout views
│       └── layouts/            # Layout templates
└── routes/
    ├── api.php                 # API routes
    └── web.php                 # Web routes
```

## 🔒 Security

- **Rate Limiting**: 60 requests/minute on API
- **CSRF Protection**: Enabled on all forms
- **SQL Injection**: Protected via Eloquent ORM
- **XSS Protection**: Blade template escaping
- **API Logging**: All requests logged with IP tracking
- **Validation**: Strict input validation on all endpoints

## 🚀 Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions.

### Quick Deploy Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set Stripe live keys
- [ ] Configure WhatsApp API
- [ ] Run `php artisan migrate --seed`
- [ ] Run `npm run build`
- [ ] Set up SSL certificate
- [ ] Configure Stripe webhook
- [ ] Test payment flow
- [ ] Test WhatsApp notifications

## 📝 License

This project is proprietary software. All rights reserved.

## 🤝 Support

For support, email support@cf7whatsapp.com or visit our documentation.

## 🔄 Updates

- **v1.0.0** (2026-01-04) - Initial release
  - License management system
  - Stripe integration
  - WhatsApp notifications
  - Admin & customer dashboards
  - API endpoints
