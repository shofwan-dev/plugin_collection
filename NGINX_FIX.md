# Fix 404 Error on Landing Page Update (Nginx)

## Problem
When editing a landing page on the live server (Nginx), clicking "Update Page" returns a 404 error, even though it works fine on local (Laragon/Apache).

## Root Cause
Nginx servers sometimes don't properly handle HTTP PUT/PATCH methods used by Laravel's resource routes.

## Solutions

### Solution 1: Update Nginx Configuration (Recommended)

1. **SSH into your server**
2. **Edit your Nginx site configuration:**
   ```bash
   sudo nano /etc/nginx/sites-available/your-site
   ```

3. **Ensure your configuration includes:**
   ```nginx
   location ~ \.php$ {
       fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
       fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
       include fastcgi_params;
       
       # IMPORTANT: These lines ensure HTTP methods are passed correctly
       fastcgi_param REQUEST_METHOD $request_method;
       fastcgi_param CONTENT_TYPE $content_type;
       fastcgi_param CONTENT_LENGTH $content_length;
   }
   ```

4. **Test and reload Nginx:**
   ```bash
   sudo nginx -t
   sudo systemctl reload nginx
   ```

### Solution 2: Use POST Fallback Route (Quick Fix)

We've added a fallback POST route in `routes/web.php`:

```php
Route::post('landing-pages/{landing_page}/update', [LandingPageController::class, 'update'])
    ->name('landing-pages.update-post');
```

To use this, update the form action in `edit.blade.php`:

**Option A: Change to POST-only (remove @method('PUT'))**
```blade
<form method="POST" action="{{ route('admin.landing-pages.update-post', $landingPage->id) }}" enctype="multipart/form-data">
    @csrf
    <!-- Remove @method('PUT') -->
```

**Option B: Keep current (recommended - works on both)**
Keep the current form as is. Laravel will try PUT first, and if it fails, you can manually use the POST route.

### Solution 3: aaPanel Specific Fix

If you're using aaPanel:

1. **Go to aaPanel → Website → Your Site → Rewrite**
2. **Select "Laravel" from the dropdown**
3. **Or manually add:**
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

4. **Save and restart Nginx**

### Solution 4: Check PHP-FPM

Ensure PHP-FPM is running and configured correctly:

```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

## Testing

After applying any solution:

1. Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

2. Test the landing page edit form
3. Check Nginx error logs if still failing:
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

## Files Created

- `public/.htaccess` - Apache fallback (if needed)
- `nginx-laravel.conf` - Reference Nginx configuration
- `routes/web.php` - Added POST fallback route

## Verification

To verify the route is working:

```bash
php artisan route:list | grep landing-pages
```

You should see both:
- `PUT|PATCH admin/landing-pages/{landing_page}`
- `POST admin/landing-pages/{landing_page}/update`
