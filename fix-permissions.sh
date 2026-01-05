#!/bin/bash

# Fix Laravel Permissions on aaPanel/Linux Server
# Run this script on your server to fix permission issues

echo "=== Fixing Laravel File Permissions ==="

# Navigate to your Laravel root directory
cd /www/wwwroot/store.mutekar.com

# Set correct ownership (www user for aaPanel)
echo "Setting ownership to www:www..."
sudo chown -R www:www .

# Set correct permissions for directories
echo "Setting directory permissions to 755..."
sudo find . -type d -exec chmod 755 {} \;

# Set correct permissions for files
echo "Setting file permissions to 644..."
sudo find . -type f -exec chmod 644 {} \;

# Set special permissions for storage and cache
echo "Setting storage permissions..."
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Set executable permission for artisan
echo "Making artisan executable..."
sudo chmod +x artisan

# Clear Laravel cache
echo "Clearing Laravel cache..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Restart PHP-FPM
echo "Restarting PHP-FPM..."
sudo systemctl restart php-fpm

echo "=== Done! Permissions fixed. ==="
echo "Please test your website now."
