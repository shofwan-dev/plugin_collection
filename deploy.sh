#!/bin/bash

# CF7 WhatsApp - Quick Deployment Script
# This script automates the deployment process
# Run with: bash deploy.sh

set -e

echo "🚀 CF7 WhatsApp Deployment Script"
echo "=================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/cf7whatsapp-website"
PHP_VERSION="8.2"

# Functions
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    print_error "Please run as root (use sudo)"
    exit 1
fi

# Step 1: Update system
echo "Step 1: Updating system..."
apt update && apt upgrade -y
print_success "System updated"

# Step 2: Install required packages
echo ""
echo "Step 2: Installing required packages..."

# Install PHP and extensions
if ! command -v php &> /dev/null; then
    add-apt-repository -y ppa:ondrej/php
    apt update
    apt install -y php${PHP_VERSION} php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-curl php${PHP_VERSION}-zip php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl php${PHP_VERSION}-soap php${PHP_VERSION}-cli
    print_success "PHP ${PHP_VERSION} installed"
else
    print_success "PHP already installed"
fi

# Install MySQL
if ! command -v mysql &> /dev/null; then
    apt install -y mysql-server
    print_success "MySQL installed"
else
    print_success "MySQL already installed"
fi

# Install Nginx
if ! command -v nginx &> /dev/null; then
    apt install -y nginx
    print_success "Nginx installed"
else
    print_success "Nginx already installed"
fi

# Install Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    print_success "Composer installed"
else
    print_success "Composer already installed"
fi

# Install Certbot for SSL
if ! command -v certbot &> /dev/null; then
    apt install -y certbot python3-certbot-nginx
    print_success "Certbot installed"
else
    print_success "Certbot already installed"
fi

# Step 3: Create database
echo ""
echo "Step 3: Database setup..."
read -p "Enter database name [cf7whatsapp_prod]: " DB_NAME
DB_NAME=${DB_NAME:-cf7whatsapp_prod}

read -p "Enter database username [cf7whatsapp_user]: " DB_USER
DB_USER=${DB_USER:-cf7whatsapp_user}

read -sp "Enter database password: " DB_PASS
echo ""

mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
print_success "Database created"

# Step 4: Set up application directory
echo ""
echo "Step 4: Setting up application..."

if [ ! -d "$APP_DIR" ]; then
    print_error "Application directory not found: $APP_DIR"
    print_warning "Please upload your application files to $APP_DIR first"
    exit 1
fi

cd $APP_DIR

# Set permissions
chown -R www-data:www-data $APP_DIR
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
print_success "Permissions set"

# Install dependencies
if [ -f "composer.json" ]; then
    sudo -u www-data composer install --optimize-autoloader --no-dev
    print_success "Composer dependencies installed"
fi

# Step 5: Environment configuration
echo ""
echo "Step 5: Configuring environment..."

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        print_success ".env file created"
    else
        print_error ".env.example not found"
        exit 1
    fi
fi

# Generate app key
php artisan key:generate --force
print_success "Application key generated"

# Update .env with database credentials
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env

print_warning "Please update the following in .env file:"
echo "  - APP_URL"
echo "  - MAIL_* settings"
echo "  - PADDLE_* settings"
echo "  - ENVATO_API_TOKEN (if using Envato)"
echo ""
read -p "Press Enter after updating .env file..."

# Step 6: Run migrations
echo ""
echo "Step 6: Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"

# Step 7: Optimize application
echo ""
echo "Step 7: Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
print_success "Application optimized"

# Step 8: Configure Nginx
echo ""
echo "Step 8: Configuring Nginx..."
read -p "Enter your domain name (e.g., example.com): " DOMAIN

cat > /etc/nginx/sites-available/cf7whatsapp << EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    access_log /var/log/nginx/cf7whatsapp-access.log;
    error_log /var/log/nginx/cf7whatsapp-error.log;

    client_max_body_size 20M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/cf7whatsapp /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx
print_success "Nginx configured"

# Step 9: SSL Certificate
echo ""
echo "Step 9: Setting up SSL certificate..."
read -p "Do you want to install SSL certificate with Let's Encrypt? (y/n): " INSTALL_SSL

if [ "$INSTALL_SSL" = "y" ]; then
    certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos --email admin@${DOMAIN}
    print_success "SSL certificate installed"
else
    print_warning "SSL certificate skipped. You can install it later with: certbot --nginx -d ${DOMAIN}"
fi

# Step 10: Firewall
echo ""
echo "Step 10: Configuring firewall..."
if command -v ufw &> /dev/null; then
    ufw allow 22/tcp
    ufw allow 80/tcp
    ufw allow 443/tcp
    ufw --force enable
    print_success "Firewall configured"
else
    apt install -y ufw
    ufw allow 22/tcp
    ufw allow 80/tcp
    ufw allow 443/tcp
    ufw --force enable
    print_success "Firewall installed and configured"
fi

# Step 11: Create admin user
echo ""
echo "Step 11: Creating admin user..."
read -p "Enter admin name: " ADMIN_NAME
read -p "Enter admin email: " ADMIN_EMAIL
read -sp "Enter admin password: " ADMIN_PASS
echo ""

php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = '${ADMIN_NAME}';
\$user->email = '${ADMIN_EMAIL}';
\$user->password = bcrypt('${ADMIN_PASS}');
\$user->role = 'admin';
\$user->email_verified_at = now();
\$user->save();
echo 'Admin user created successfully';
"
print_success "Admin user created"

# Step 12: Setup cron for scheduler
echo ""
echo "Step 12: Setting up Laravel scheduler..."
(crontab -u www-data -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1") | crontab -u www-data -
print_success "Scheduler configured"

# Final steps
echo ""
echo "=================================="
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo "=================================="
echo ""
echo "Next steps:"
echo "1. Visit https://${DOMAIN} to verify installation"
echo "2. Login to admin panel: https://${DOMAIN}/admin"
echo "3. Configure Paddle webhook in Paddle Dashboard:"
echo "   URL: https://${DOMAIN}/paddle/webhook"
echo "4. Update remaining .env variables if needed"
echo "5. Test the application thoroughly"
echo ""
echo "Important files:"
echo "  - Application: ${APP_DIR}"
echo "  - Nginx config: /etc/nginx/sites-available/cf7whatsapp"
echo "  - Logs: ${APP_DIR}/storage/logs/laravel.log"
echo "  - Nginx logs: /var/log/nginx/cf7whatsapp-*.log"
echo ""
print_warning "Don't forget to:"
echo "  - Setup regular backups"
echo "  - Monitor application logs"
echo "  - Keep system updated"
echo ""
