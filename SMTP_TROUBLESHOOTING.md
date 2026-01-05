# SMTP Email Troubleshooting Guide

## Problem: Email Not Received

Dari log terlihat email sudah dibuat dengan benar, tapi tidak sampai ke penerima.

### Common Issues

#### 1. SMTP Credentials Invalid
**Check:**
- Email username benar?
- Email password benar? (Untuk Gmail harus App Password)
- Host dan Port sesuai?

**Gmail Example:**
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: xxxx xxxx xxxx xxxx (App Password dari Google)
```

#### 2. Email Masuk ke Spam
**Action:**
- Check folder Spam/Junk di email penerima
- Gmail/Outlook sering block email dari unknown sender

#### 3. SMTP Port Blocked
**Test:**
```bash
# Test connection
telnet smtp.gmail.com 587
```

#### 4. Firewall/Antivirus Blocking
**Check:**
- Windows Firewall
- Antivirus settings
- Port 587/465 allowed?

### Debugging Steps

#### Step 1: Enable SMTP Debug
Edit `config/mail.php` atau update di controller:

```php
config([
    'mail.mailers.smtp.host' => $host,
    'mail.mailers.smtp.port' => $port,
    'mail.mailers.smtp.encryption' => $encryption,
    'mail.mailers.smtp.username' => $username,
    'mail.mailers.smtp.password' => $password,
    'mail.from.address' => $fromAddress,
    'mail.from.name' => $fromName,
]);

// Enable debug
\Config::set('app.debug', true);
```

#### Step 2: Check Mail Failures
```php
\Mail::raw('Test', function($message) {
    $message->to('test@example.com')->subject('Test');
});

if (count(\Mail::failures()) > 0) {
    dd('Failed to send to:', \Mail::failures());
}
```

#### Step 3: Test via Artisan Tinker
```bash
php artisan tinker

>>> Mail::raw('Test email', function($m) { $m->to('your@email.com')->subject('Test'); });
```

### Gmail Specific

#### Enable App Password:
1. Go to Google Account: https://myaccount.google.com/
2. Security → 2-Step Verification (enable if not)
3. App passwords → Generate
4. Use generated password (16 chars) in Email Password field

#### Settings:
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: youremail@gmail.com
Password: xxxx xxxx xxxx xxxx (App Password)
From Address: youremail@gmail.com
From Name: Your Name
```

### Mailtrap (For Testing)

#### Settings:
```
Host: smtp.mailtrap.io
Port: 587
Encryption: TLS
Username: (from Mailtrap inbox)
Password: (from Mailtrap inbox)
```

**Benefit:** Catches ALL emails for testing without sending to real addresses.

### Check Current Settings

Run in tinker:
```bash
php artisan tinker

>>> config('mail.mailers.smtp.host')
>>> config('mail.mailers.smtp.port')
>>> config('mail.from.address')
```

### Laravel Log Location

Check for errors:
```
storage/logs/laravel.log
```

Search for:
- `TEST EMAIL EXCEPTION`
- `SMTP`
- `Swift_Transport`

### Common Error Messages

**"Connection refused"**
- Port blocked or wrong host

**"Authentication failed"**
- Wrong username/password

**"Connection timeout"**
- Firewall blocking or wrong port

**"Could not connect to SMTP host"**
- Host unreachable or wrong address

### Quick Test Script

Create `test-email.php`:
```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$host = 'smtp.gmail.com';
$port = 587;
$username = 'your@gmail.com';
$password = 'your-app-password';

config([
    'mail.mailers.smtp.host' => $host,
    'mail.mailers.smtp.port' => $port,
    'mail.mailers.smtp.encryption' => 'tls',
    'mail.mailers.smtp.username' => $username,
    'mail.mailers.smtp.password' => $password,
]);

try {
    Mail::raw('Test email content', function($message) {
        $message->to('test@example.com')
                ->subject('Test Email');
    });
    
    echo "Email sent successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

Run: `php test-email.php`

### Current Status

From your log:
```
From: Mutekar Store <admin@mutekar.com>  ✅ Correct
To: admin@mutekar.com                     ✅ Correct
Subject: Test Email - SMTP Configuration  ✅ Correct
```

**Email created successfully, but not delivered.**

### Likely Issues:

1. **Gmail App Password not used** - Most common
2. **Email in Spam folder** - Check spam
3. **SMTP credentials wrong** - Double check

### Next Steps:

1. Check Spam folder
2. If using Gmail, generate App Password
3. Update Email Password field with App Password
4. Test again
5. Check Laravel log for new errors
