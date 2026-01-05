# CF7 to WhatsApp - Testing Guide

## Plugin sudah diupdate dengan License System & Auto-Update

### ✅ Yang Sudah Diimplementasikan:

1. **License Manager** (`class-cf7-to-wa-license.php`)
   - ✅ Activate license
   - ✅ Deactivate license
   - ✅ Verify license
   - ✅ Daily license check
   - ✅ Updated API endpoints

2. **Auto-Updater** (`class-cf7-to-wa-updater.php`) - NEW!
   - ✅ Check for updates automatically
   - ✅ Download updates from license server
   - ✅ Integrate with WordPress update system
   - ✅ Show update notifications

3. **Main Plugin File** (`cf7-to-whatsapp.php`)
   - ✅ Include updater class
   - ✅ Initialize updater on admin

---

## 🧪 Testing Steps

### 1. Setup Plugin

```bash
# Copy plugin ke WordPress
cp -r example/cf7-to-whatsapp /path/to/wordpress/wp-content/plugins/

# Atau zip dan upload via WordPress admin
cd example
zip -r cf7-to-whatsapp.zip cf7-to-whatsapp/
```

### 2. Update API URL

Edit file `includes/class-cf7-to-wa-license.php` dan `includes/class-cf7-to-wa-updater.php`:

```php
// Ganti ini:
private $api_url = 'https://your-domain.com/api/v1/license';

// Dengan domain Anda:
private $api_url = 'http://localhost/cf7whatsapp-website/public/api/v1/license';
// atau
private $api_url = 'https://yourdomain.com/api/v1/license';
```

### 3. Activate Plugin

1. Login ke WordPress admin
2. Go to **Plugins** → **Installed Plugins**
3. Activate **CF7 to WhatsApp Gateway**

### 4. Test License Activation

1. Go to **CF7 to WhatsApp** → **License**
2. Enter license key (dari dashboard customer)
3. Click **Activate License**

**Expected Result:**
```
✅ License activated successfully!
Status: Active
Domain: your-domain.com
Plan: Single Site (or your plan)
```

**Check Server Log:**
```bash
tail -f storage/logs/laravel.log
```

You should see:
```
[INFO] License activation attempt
[INFO] License activated for domain: your-domain.com
```

### 5. Test License Verification

Plugin akan auto-verify setiap 24 jam. Untuk test manual:

```php
// Di WordPress, run this in wp-admin/admin-ajax.php or custom page
$license_manager = CF7_To_WA_License::get_instance();
$result = $license_manager->validate_license();
var_dump($result);
```

**Expected Result:**
```php
array(
    'valid' => true,
    'message' => 'License is valid.',
    'data' => array(...)
)
```

### 6. Test Auto-Update

#### A. Prepare Update

1. Go to admin dashboard → **Products**
2. Edit your product
3. Change version to `1.1.0` (higher than current `1.0.0`)
4. Upload new plugin ZIP file
5. Add changelog
6. Save

#### B. Check for Updates in WordPress

1. Go to **Dashboard** → **Updates**
2. Or **Plugins** → **Installed Plugins**
3. You should see update notification:

```
CF7 to WhatsApp Gateway
There is a new version of CF7 to WhatsApp Gateway available.
View version 1.1.0 details or update now.
```

#### C. Update Plugin

1. Click **update now**
2. WordPress will download from your license server
3. Plugin will be updated automatically

**Check Server Log:**
```
[INFO] Plugin downloaded
[INFO] License: XXXX-XXXX-XXXX-XXXX
[INFO] Product: CF7 to WhatsApp Gateway
[INFO] Version: 1.1.0
```

### 7. Test License Deactivation

1. Go to **CF7 to WhatsApp** → **License**
2. Click **Deactivate License**

**Expected Result:**
```
✅ License deactivated successfully!
```

**Check Database:**
- `activated_domains` in licenses table should be updated
- Domain should be removed from array

---

## 🔍 Debugging

### Enable WordPress Debug

Edit `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check logs:
```bash
tail -f wp-content/debug.log
```

### Check API Responses

Add this to `class-cf7-to-wa-license.php` after API calls:

```php
error_log('API Response: ' . print_r($data, true));
```

### Test API Manually

```bash
# Test activation
curl -X POST http://localhost/cf7whatsapp-website/public/api/v1/license/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-LICENSE-KEY",
    "domain": "test.local"
  }'

# Test verification
curl -X POST http://localhost/cf7whatsapp-website/public/api/v1/license/verify \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-LICENSE-KEY",
    "domain": "test.local"
  }'

# Test check update
curl -X POST http://localhost/cf7whatsapp-website/public/api/v1/license/check-update \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-LICENSE-KEY",
    "current_version": "1.0.0",
    "product_slug": "cf7-to-whatsapp"
  }'
```

---

## ✅ Success Criteria

- [ ] Plugin activates without errors
- [ ] License page shows correctly
- [ ] Can activate license with valid key
- [ ] License status shows as "Active"
- [ ] Daily verification works (check after 24h)
- [ ] Update notification appears when new version available
- [ ] Can update plugin via WordPress admin
- [ ] Can deactivate license
- [ ] Server logs all API calls

---

## 🐛 Common Issues

### Issue: "Connection error"
**Solution:** Check API URL is correct and server is reachable

### Issue: "License activation failed"
**Solution:** 
- Check license key is valid
- Check domain matches
- Check license not expired
- Check max activations not reached

### Issue: "No update available"
**Solution:**
- Check product version is higher than current
- Check product is active
- Check license is valid

### Issue: "Download failed"
**Solution:**
- Check file exists in `storage/app/public/products/`
- Check file permissions
- Check storage link exists

---

## 📝 Next Steps

1. Test on local WordPress installation
2. Test on staging server
3. Test with different license types (Single, 5 Sites, Unlimited)
4. Test license expiration
5. Test max activations limit
6. Deploy to production
