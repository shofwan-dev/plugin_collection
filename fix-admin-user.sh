#!/bin/bash

# Fix Admin User Script
# Run this on production server to fix admin access

echo "🔧 Fixing Admin User Access"
echo "============================"
echo ""

cd /var/www/cf7whatsapp-website

# Check if is_admin column exists
echo "🔍 Checking is_admin column..."
COLUMN_EXISTS=$(php artisan tinker --execute="
try {
    \$user = App\Models\User::first();
    echo isset(\$user->is_admin) ? 'yes' : 'no';
} catch (\Exception \$e) {
    echo 'no';
}
" 2>/dev/null)

if [ "$COLUMN_EXISTS" != "yes" ]; then
    echo "❌ Column is_admin not found!"
    echo "📝 Running migration to add is_admin column..."
    
    # Run the specific migration
    php artisan migrate --path=database/migrations/2026_01_03_155944_add_is_admin_to_users_table.php --force
    
    if [ $? -eq 0 ]; then
        echo "✅ Migration completed successfully"
    else
        echo "❌ Migration failed. Please check the error above."
        exit 1
    fi
else
    echo "✅ Column is_admin already exists"
fi

# Update admin user
echo ""
echo "👤 Updating admin user..."

read -p "Enter admin email [admin@cf7whatsapp.com]: " ADMIN_EMAIL
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@cf7whatsapp.com}

# Check if user exists
USER_EXISTS=$(php artisan tinker --execute="echo App\Models\User::where('email', '$ADMIN_EMAIL')->exists() ? 'yes' : 'no';" 2>/dev/null)

if [ "$USER_EXISTS" = "yes" ]; then
    echo "📝 User exists, updating to admin..."
    php artisan tinker --execute="
        \$user = App\Models\User::where('email', '$ADMIN_EMAIL')->first();
        \$user->is_admin = true;
        \$user->email_verified_at = now();
        \$user->save();
        echo 'User updated to admin successfully';
    "
else
    echo "📝 User not found, creating new admin user..."
    read -p "Enter admin name [Admin]: " ADMIN_NAME
    ADMIN_NAME=${ADMIN_NAME:-Admin}
    
    read -sp "Enter admin password: " ADMIN_PASS
    echo ""
    
    php artisan tinker --execute="
        App\Models\User::create([
            'name' => '$ADMIN_NAME',
            'email' => '$ADMIN_EMAIL',
            'password' => bcrypt('$ADMIN_PASS'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
        echo 'Admin user created successfully';
    "
fi

# Clear caches
echo ""
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan config:cache

echo ""
echo "✅ Admin user fixed successfully!"
echo ""
echo "Login credentials:"
echo "  Email: $ADMIN_EMAIL"
echo "  Admin Panel: https://yourdomain.com/admin"
echo ""
echo "⚠️  Make sure to change the password after first login!"
