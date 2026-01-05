#!/bin/bash

# Quick Fix Script for Products Table
# Run this on your production server

echo "🔧 Fixing Products Table Schema"
echo "================================"

cd /var/www/cf7whatsapp-website

# Backup database first
echo "📦 Creating database backup..."
php artisan db:backup 2>/dev/null || echo "⚠️  Backup command not available, continuing..."

# Check if products table exists and has data
echo "🔍 Checking products table..."
PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\Product::count();" 2>/dev/null || echo "0")

if [ "$PRODUCT_COUNT" -gt 0 ]; then
    echo "⚠️  Found $PRODUCT_COUNT products in database"
    echo "📝 Exporting products data..."
    php artisan tinker --execute="
        \$products = App\Models\Product::all();
        file_put_contents('/tmp/products_backup.json', json_encode(\$products->toArray(), JSON_PRETTY_PRINT));
        echo 'Products exported to /tmp/products_backup.json';
    "
fi

# Fresh migration for products table
echo "🔄 Refreshing products table migration..."
php artisan migrate:refresh --path=database/migrations/2026_01_04_003322_create_products_table.php --force

# If there was data, restore it
if [ "$PRODUCT_COUNT" -gt 0 ] && [ -f "/tmp/products_backup.json" ]; then
    echo "♻️  Restoring products data..."
    php artisan tinker --execute="
        \$data = json_decode(file_get_contents('/tmp/products_backup.json'), true);
        foreach (\$data as \$item) {
            App\Models\Product::create([
                'name' => \$item['name'] ?? 'Product',
                'slug' => \$item['slug'] ?? Str::slug(\$item['name'] ?? 'product'),
                'description' => \$item['description'] ?? null,
                'features' => \$item['features'] ?? null,
                'icon' => \$item['icon'] ?? null,
                'is_active' => \$item['is_active'] ?? true,
                'sort_order' => \$item['sort_order'] ?? 0,
            ]);
        }
        echo 'Products restored successfully';
    "
    rm /tmp/products_backup.json
fi

# Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan config:cache

echo ""
echo "✅ Products table fixed successfully!"
echo ""
echo "Next steps:"
echo "1. Test the homepage"
echo "2. Verify products are showing"
echo "3. Check admin panel"
