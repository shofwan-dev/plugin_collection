# Fix Permission Denied Error (13) - Laravel on aaPanel

## Problem
```
stat() "/www/wwwroot/store.mutekar.com/public/index.php" failed (13: Permission denied)
```

Nginx cannot access Laravel files due to incorrect file permissions.

## Quick Fix (Via SSH)

### Option 1: Using the Script (Recommended)

1. **Upload `fix-permissions.sh` to your server**
2. **SSH into your server:**
   ```bash
   ssh root@your-server-ip
   ```

3. **Navigate to your Laravel directory:**
   ```bash
   cd /www/wwwroot/store.mutekar.com
   ```

4. **Run the fix script:**
   ```bash
   chmod +x fix-permissions.sh
   ./fix-permissions.sh
   ```

### Option 2: Manual Commands

Run these commands one by one:

```bash
# Navigate to Laravel root
cd /www/wwwroot/store.mutekar.com

# Fix ownership (www is the default user for aaPanel)
sudo chown -R www:www .

# Fix directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Fix file permissions
sudo find . -type f -exec chmod 644 {} \;

# Fix storage and cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Make artisan executable
sudo chmod +x artisan

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Restart PHP-FPM
sudo systemctl restart php-fpm
```

## Fix via aaPanel (No SSH Required)

1. **Login to aaPanel**
2. **Go to: Files → /www/wwwroot/store.mutekar.com**
3. **Select the entire folder (store.mutekar.com)**
4. **Click "Permission" button at the top**
5. **Set:**
   - Owner: `www`
   - Group: `www`
   - Permission: `755` for directories, `644` for files
   - ✅ Check "Apply to subdirectories"
6. **Click OK**

7. **Then manually fix storage:**
   - Navigate to `storage` folder
   - Right-click → Permission
   - Set to `775`
   - ✅ Apply to subdirectories

8. **Do the same for:**
   - `bootstrap/cache` → `775`

9. **Restart PHP-FPM:**
   - Website → Your Site → Service → Restart PHP

## Verify Permissions

Check if permissions are correct:

```bash
ls -la /www/wwwroot/store.mutekar.com/public/index.php
```

Should show:
```
-rw-r--r-- 1 www www 1234 Jan 5 16:00 index.php
```

## Common Permission Issues

### If still getting errors:

1. **Check SELinux (if enabled):**
   ```bash
   sudo setenforce 0
   ```

2. **Check Nginx user:**
   ```bash
   ps aux | grep nginx
   ```
   Should show `www` as the user.

3. **Verify PHP-FPM user:**
   ```bash
   ps aux | grep php-fpm
   ```
   Should also show `www`.

## Prevention

After every `git pull`, run:
```bash
sudo chown -R www:www /www/wwwroot/store.mutekar.com
sudo chmod -R 775 storage bootstrap/cache
```

Or create a post-deployment script.

## Understanding Permissions

- **755** (rwxr-xr-x): Directories - Owner can read/write/execute, others can read/execute
- **644** (rw-r--r--): Files - Owner can read/write, others can only read
- **775** (rwxrwxr-x): Storage/Cache - Owner and group can read/write/execute

## Nginx User Configuration

If using a different user, update in:
```bash
sudo nano /www/server/nginx/conf/nginx.conf
```

First line should be:
```nginx
user www www;
```

## Testing

After fixing permissions:

1. Visit: `https://store.mutekar.com/lp/cf7-to-whatsapp`
2. Should load without 403/404 errors
3. Check error log:
   ```bash
   tail -f /www/wwwlogs/store.mutekar.com.error.log
   ```
   Should be clean (no permission errors)
