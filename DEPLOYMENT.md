# Deployment Checklist - CF7 WhatsApp Gateway

## Pre-Deployment

### 1. Code Preparation
- [ ] All features tested locally
- [ ] No debug code or console.logs
- [ ] All TODO comments resolved
- [ ] Code reviewed and optimized

### 2. Environment Configuration
- [ ] Create production `.env` file
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Configure production database credentials
- [ ] Set production `APP_URL`

### 3. Dependencies
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm install`
- [ ] Run `npm run build`

## Server Setup

### 1. Server Requirements
- [ ] PHP 8.2+ installed
- [ ] MySQL/MariaDB installed
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] SSL certificate configured

### 2. Web Server Configuration

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/cf7whatsapp-website/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 3. File Permissions
```bash
sudo chown -R www-data:www-data /var/www/cf7whatsapp-website
sudo chmod -R 755 /var/www/cf7whatsapp-website
sudo chmod -R 775 /var/www/cf7whatsapp-website/storage
sudo chmod -R 775 /var/www/cf7whatsapp-website/bootstrap/cache
```

## Database Setup

### 1. Create Database
```sql
CREATE DATABASE cf7whatsapp_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cf7user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON cf7whatsapp_prod.* TO 'cf7user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 3. Change Default Passwords
- [ ] Change admin password from default
- [ ] Delete test customer account

## Third-Party Services

### 1. Stripe Configuration
- [ ] Create Stripe production account
- [ ] Get live API keys (pk_live_... and sk_live_...)
- [ ] Add to `.env`: `STRIPE_KEY` and `STRIPE_SECRET`
- [ ] Create webhook endpoint
- [ ] Add webhook URL: `https://yourdomain.com/webhook/stripe`
- [ ] Select events:
  - `checkout.session.completed`
  - `payment_intent.succeeded`
  - `payment_intent.payment_failed`
- [ ] Copy webhook secret to `.env`: `STRIPE_WEBHOOK_SECRET`
- [ ] Test webhook with Stripe CLI

### 2. WhatsApp API Configuration
- [ ] Choose WhatsApp API provider (Twilio, Fonnte, etc.)
- [ ] Get API credentials
- [ ] Configure in admin panel (`/admin/settings`)
- [ ] Test connection with "Send Test Message"

### 3. Email Configuration
- [ ] Configure SMTP settings in `.env`
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Optimization

### 1. Laravel Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 2. Composer Optimization
```bash
composer dump-autoload --optimize
```

### 3. Asset Optimization
```bash
npm run build
```

## Security

### 1. SSL Certificate
- [ ] Install SSL certificate (Let's Encrypt recommended)
```bash
sudo certbot --nginx -d yourdomain.com
```

### 2. Security Headers
Add to Nginx/Apache config:
```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
add_header X-XSS-Protection "1; mode=block";
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";
```

### 3. Environment Security
- [ ] Ensure `.env` is not publicly accessible
- [ ] Set proper file permissions
- [ ] Disable directory listing

## Monitoring & Logging

### 1. Error Logging
- [ ] Configure log rotation
```bash
# /etc/logrotate.d/laravel
/var/www/cf7whatsapp-website/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
}
```

### 2. Application Monitoring
- [ ] Set up uptime monitoring (UptimeRobot, Pingdom)
- [ ] Configure error tracking (Sentry, Bugsnag)
- [ ] Set up performance monitoring

### 3. Backup Strategy
- [ ] Database backup (daily)
```bash
# Cron job for daily backup
0 2 * * * mysqldump -u cf7user -p'password' cf7whatsapp_prod > /backups/db_$(date +\%Y\%m\%d).sql
```
- [ ] File backup (weekly)
- [ ] Test restore procedure

## Testing

### 1. Functional Testing
- [ ] Test user registration
- [ ] Test login/logout
- [ ] Test password reset
- [ ] Test checkout flow (Stripe test mode first)
- [ ] Test license generation
- [ ] Test WhatsApp notifications
- [ ] Test admin dashboard
- [ ] Test customer dashboard
- [ ] Test order cancellation

### 2. API Testing
```bash
# Test license activation
curl -X POST https://yourdomain.com/api/v1/licenses/activate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"XXXX-XXXX-XXXX-XXXX","domain":"test.com"}'

# Test license validation
curl -X POST https://yourdomain.com/api/v1/licenses/validate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"XXXX-XXXX-XXXX-XXXX","domain":"test.com"}'
```

### 3. Performance Testing
- [ ] Test page load times
- [ ] Test API response times
- [ ] Test under load (100+ concurrent users)

## Go Live

### 1. DNS Configuration
- [ ] Point domain to server IP
- [ ] Wait for DNS propagation (24-48 hours)
- [ ] Verify DNS with `nslookup yourdomain.com`

### 2. Final Checks
- [ ] All environment variables set correctly
- [ ] Database migrations completed
- [ ] Stripe webhook working
- [ ] WhatsApp notifications working
- [ ] SSL certificate valid
- [ ] All tests passing

### 3. Launch
- [ ] Switch Stripe to live mode
- [ ] Enable WhatsApp notifications
- [ ] Announce launch
- [ ] Monitor logs for errors

## Post-Deployment

### 1. Monitoring
- [ ] Check error logs daily
- [ ] Monitor API usage
- [ ] Track payment success rate
- [ ] Monitor WhatsApp delivery rate

### 2. Maintenance
- [ ] Schedule regular backups
- [ ] Plan for updates
- [ ] Monitor security advisories
- [ ] Keep dependencies updated

### 3. Documentation
- [ ] Document any custom configurations
- [ ] Create runbook for common issues
- [ ] Train support team

## Rollback Plan

If issues occur:

1. **Immediate Actions**
```bash
# Revert to previous version
git checkout previous-stable-tag
composer install
npm install && npm run build
php artisan migrate:rollback
```

2. **Database Restore**
```bash
mysql -u cf7user -p cf7whatsapp_prod < /backups/db_backup.sql
```

3. **Clear Caches**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support Contacts

- **Developer**: [Your Email]
- **Server Admin**: [Admin Email]
- **Stripe Support**: https://support.stripe.com
- **Emergency**: [Emergency Contact]

---

**Last Updated**: 2026-01-04  
**Version**: 1.0.0
