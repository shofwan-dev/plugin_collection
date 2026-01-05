<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10B981; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; }
        .license-key { background: #fff; border: 2px dashed #10B981; padding: 15px; text-align: center; font-size: 20px; font-family: monospace; margin: 20px 0; }
        .button { background: #10B981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 License Activated!</h1>
        </div>
        <div class="content">
            <p>Congratulations! Your license has been activated successfully.</p>
            
            <div class="license-key">
                {{ $license->license_key }}
            </div>

            <h3>License Details:</h3>
            <ul>
                <li><strong>Product:</strong> {{ $license->product->name }}</li>
                <li><strong>Max Domains:</strong> {{ $license->max_domains === -1 ? 'Unlimited' : $license->max_domains }}</li>
                <li><strong>Expires:</strong> {{ $license->expires_at ? $license->expires_at->format('Y-m-d') : 'Never' }}</li>
            </ul>

            <h3>Next Steps:</h3>
            <ol>
                <li>Download the plugin from your dashboard</li>
                <li>Install it on your WordPress site</li>
                <li>Activate using your license key above</li>
            </ol>

            <p style="margin-top: 30px;">
                <a href="{{ route('dashboard.licenses') }}" class="button">View License</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CF7 WhatsApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
