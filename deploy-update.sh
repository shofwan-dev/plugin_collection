#!/bin/bash

# Quick Update Deployment Script
# Run this after pushing new code to update production server

echo "🚀 Updating Application"
echo "======================"
echo ""

cd /var/www/cf7whatsapp-website

# Pull latest code
echo "📥 Pulling latest code..."
git pull origin master

if [ $? -ne 0 ]; then
    echo "❌ Git pull failed!"
    exit 1
fi

# Install/Update dependencies (if composer.json changed)
if git diff HEAD@{1} --name-only | grep -q "composer.json"; then
    echo "📦 Updating composer dependencies..."
    composer install --optimize-autoloader --no-dev
fi

# Run migrations (if any new migrations)
if git diff HEAD@{1} --name-only | grep -q "database/migrations"; then
    echo "🗄️  Running migrations..."
    php artisan migrate --force
fi

# Clear all caches
echo "🧹 Clearing caches..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild caches
echo "💾 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
echo "🔐 Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
systemctl restart php8.2-fpm

echo ""
echo "✅ Application updated successfully!"
echo ""
echo "📊 Current status:"
php artisan --version
echo ""
echo "🔗 Visit your site to verify the update"
