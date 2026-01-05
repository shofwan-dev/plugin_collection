# 🚀 Deployment Guide - CF7 to WhatsApp License System

Panduan lengkap untuk deploy aplikasi ke production server dengan aman.

## 📋 Table of Contents
1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Deployment Steps](#deployment-steps)
4. [Security Configuration](#security-configuration)
5. [Environment Variables](#environment-variables)
6. [Database Migration](#database-migration)
7. [Post-Deployment](#post-deployment)
8. [Troubleshooting](#troubleshooting)

---

## ✅ Pre-Deployment Checklist

### 1. **Code Preparation**
- [ ] All features tested locally
- [ ] No debug code or `dd()` statements
- [ ] All dependencies in `composer.json`
- [ ] `.env.example` updated with all required variables
- [ ] Database migrations tested
- [ ] Seeders ready (if needed)

### 2. **Security Check**
- [ ] Remove all test/dummy data
- [ ] Change all default passwords
- [ ] Review `.gitignore` file
- [ ] No sensitive data in code
- [ ] API keys moved to environment variables

### 3. **Performance**
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`

---

## 🖥️ Server Requirements

### Minimum Requirements:
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Web Server**: Nginx or Apache
- **SSL Certificate**: Required for production
- **Composer**: Latest version
- **Node.js**: 18+ (for asset compilation)

### PHP Extensions Required:
```bash
php -m | grep -E 'bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml|curl|gd|zip'
```

Required extensions:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD
- Zip

---

## 🚀 Deployment Steps

### Step 1: Server Setup

#### A. Update Server
```bash
sudo apt update && sudo apt upgrade -y
```

#### B. Install Required Packages
```bash
# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
php8.2-intl php8.2-soap php8.2-cli

# Install MySQL
sudo apt install -y mysql-server

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (optional, for frontend assets)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2: Create Database

```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE cf7whatsapp_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cf7whatsapp_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON cf7whatsapp_prod.* TO 'cf7whatsapp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Upload Application

#### Option A: Using Git (Recommended)
```bash
# Navigate to web directory
cd /var/www

# Clone repository
sudo git clone https://github.com/yourusername/cf7whatsapp-website.git
cd cf7whatsapp-website

# Set proper ownership
sudo chown -R www-data:www-data /var/www/cf7whatsapp-website
sudo chmod -R 755 /var/www/cf7whatsapp-website
```

#### Option B: Using FTP/SFTP
1. Upload all files to `/var/www/cf7whatsapp-website`
2. Set permissions (see Step 4)

### Step 4: Set Permissions

```bash
cd /var/www/cf7whatsapp-website

# Set ownership
sudo chown -R www-data:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Step 5: Install Dependencies

```bash
cd /var/www/cf7whatsapp-website

# Install PHP dependencies (production)
composer install --optimize-autoloader --no-dev

# Install NPM dependencies (if needed)
npm install --production
npm run build
```

### Step 6: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit environment file
nano .env
```

**Important `.env` configurations:**

```env
# Application
APP_NAME="CF7 to WhatsApp"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cf7whatsapp_prod
DB_USERNAME=cf7whatsapp_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Mail (Configure your mail service)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Paddle (Production)
PADDLE_SELLER_ID=your_seller_id
PADDLE_API_KEY=your_api_key
PADDLE_CLIENT_TOKEN=your_client_token
PADDLE_WEBHOOK_SECRET=your_webhook_secret
PADDLE_SANDBOX=false

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### Step 7: Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --force

# Create admin user
php artisan tinker
```

In tinker:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@yourdomain.com';
$user->password = bcrypt('STRONG_ADMIN_PASSWORD');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();
exit;
```

### Step 8: Optimize Application

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

### Step 9: Configure Web Server

#### Nginx Configuration

Create file: `/etc/nginx/sites-available/cf7whatsapp`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/cf7whatsapp-website/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';" always;

    # Logging
    access_log /var/log/nginx/cf7whatsapp-access.log;
    error_log /var/log/nginx/cf7whatsapp-error.log;

    # Max upload size
    client_max_body_size 20M;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/cf7whatsapp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 10: SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (already configured, but verify)
sudo certbot renew --dry-run
```

---

## 🔒 Security Configuration

### 1. **Firewall Setup**

```bash
# Install UFW
sudo apt install -y ufw

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable
sudo ufw status
```

### 2. **Secure MySQL**

```bash
sudo mysql_secure_installation
```

Answer:
- Set root password: Yes
- Remove anonymous users: Yes
- Disallow root login remotely: Yes
- Remove test database: Yes
- Reload privilege tables: Yes

### 3. **Disable Directory Listing**

Already configured in Nginx config above.

### 4. **Hide PHP Version**

Edit `/etc/php/8.2/fpm/php.ini`:
```ini
expose_php = Off
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### 5. **Setup Fail2Ban** (Optional but recommended)

```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 6. **Regular Backups**

Create backup script: `/root/backup-cf7whatsapp.sh`

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/root/backups"
APP_DIR="/var/www/cf7whatsapp-website"
DB_NAME="cf7whatsapp_prod"
DB_USER="cf7whatsapp_user"
DB_PASS="YOUR_DB_PASSWORD"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage public/storage

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

Make executable and add to cron:
```bash
chmod +x /root/backup-cf7whatsapp.sh

# Add to crontab (daily at 2 AM)
crontab -e
# Add line:
0 2 * * * /root/backup-cf7whatsapp.sh >> /var/log/cf7whatsapp-backup.log 2>&1
```

---

## 📧 Post-Deployment

### 1. **Test Application**

- [ ] Visit homepage: `https://yourdomain.com`
- [ ] Test registration
- [ ] Test login
- [ ] Test checkout flow (sandbox mode first)
- [ ] Test admin panel
- [ ] Test license activation API
- [ ] Test Envato verification (if configured)

### 2. **Configure Paddle Webhook**

1. Login to Paddle Dashboard
2. Go to Developer Tools → Notifications
3. Add webhook URL: `https://yourdomain.com/paddle/webhook`
4. Copy webhook secret to `.env`

### 3. **Configure Monitoring** (Optional)

Install Laravel Telescope for debugging (dev only):
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 4. **Setup Queue Worker** (If using queues)

Create systemd service: `/etc/systemd/system/cf7whatsapp-worker.service`

```ini
[Unit]
Description=CF7 WhatsApp Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/cf7whatsapp-website
ExecStart=/usr/bin/php /var/www/cf7whatsapp-website/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable cf7whatsapp-worker
sudo systemctl start cf7whatsapp-worker
```

### 5. **Setup Scheduler**

Add to crontab:
```bash
sudo crontab -e -u www-data
# Add line:
* * * * * cd /var/www/cf7whatsapp-website && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check logs
tail -f /var/log/nginx/cf7whatsapp-error.log
tail -f /var/www/cf7whatsapp-website/storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data /var/www/cf7whatsapp-website
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: Database Connection Error

**Solution:**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Check .env configuration
cat .env | grep DB_
```

### Issue: Assets Not Loading

**Solution:**
```bash
# Create storage link
php artisan storage:link

# Check public directory permissions
ls -la /var/www/cf7whatsapp-website/public
```

### Issue: Paddle Webhook Not Working

**Solution:**
1. Check webhook URL in Paddle Dashboard
2. Verify webhook secret in `.env`
3. Check logs: `tail -f storage/logs/laravel.log`
4. Test webhook: Use Paddle's webhook testing tool

---

## 📝 Maintenance Commands

### Update Application
```bash
cd /var/www/cf7whatsapp-website

# Pull latest code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### View Logs
```bash
# Application logs
tail -f /var/www/cf7whatsapp-website/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/cf7whatsapp-error.log
tail -f /var/log/nginx/cf7whatsapp-access.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

---

## 🎯 Security Best Practices

1. ✅ Always use HTTPS
2. ✅ Keep PHP and dependencies updated
3. ✅ Use strong passwords
4. ✅ Enable firewall
5. ✅ Regular backups
6. ✅ Monitor logs
7. ✅ Disable debug mode in production
8. ✅ Use environment variables for secrets
9. ✅ Implement rate limiting
10. ✅ Regular security audits

---

## 📞 Support

Jika ada masalah saat deployment, check:
1. Server error logs
2. Laravel logs
3. Nginx/Apache logs
4. PHP-FPM logs

---

**Last Updated:** January 2026
**Version:** 1.0
