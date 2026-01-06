<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        .message {
            margin-bottom: 25px;
            color: #666;
        }
        .order-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .order-details h3 {
            margin-top: 0;
            color: #667eea;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .license-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        .license-box h3 {
            margin-top: 0;
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .license-key {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            word-break: break-all;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .features {
            margin: 25px 0;
        }
        .feature-item {
            padding: 10px 0;
            display: flex;
            align-items: start;
        }
        .feature-icon {
            color: #28a745;
            margin-right: 10px;
            font-size: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🎉</div>
            <h1>Payment Successful!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Thank you for your purchase</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $order->customer_name }}!
            </div>

            <div class="message">
                <p>Great news! Your payment has been successfully processed. Thank you for purchasing <strong>{{ $product->name }}</strong>.</p>
                <p>Your order is now complete and ready to use. Below you'll find your license key and download information.</p>
            </div>

            <!-- License Key -->
            @if($license)
            <div class="license-box">
                <h3>🔑 YOUR LICENSE KEY</h3>
                <div class="license-key">
                    {{ $license->license_key }}
                </div>
                <p style="margin: 15px 0 0 0; font-size: 13px; opacity: 0.9;">
                    Keep this key safe - you'll need it to activate the plugin
                </p>
            </div>
            @endif

            <!-- Order Details -->
            <div class="order-details">
                <h3>📋 Order Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#{{ $order->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Product:</span>
                    <span class="detail-value">{{ $product->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Version:</span>
                    <span class="detail-value">v{{ $product->version }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Price:</span>
                    <span class="detail-value">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value" style="color: #28a745; font-weight: 600;">✓ Paid</span>
                </div>
                @if($license)
                <div class="detail-row">
                    <span class="detail-label">Max Domains:</span>
                    <span class="detail-value">
                        @if($product->max_domains === -1)
                            ∞ Unlimited
                        @else
                            {{ $product->max_domains }} {{ Str::plural('domain', $product->max_domains) }}
                        @endif
                    </span>
                </div>
                @endif
            </div>

            <!-- What's Included -->
            <div class="features">
                <h3 style="color: #333; margin-bottom: 15px;">✨ What's Included:</h3>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>Plugin file attached to this email</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>Lifetime updates</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>Priority support</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>30-day money-back guarantee</span>
                </div>
                @if($license)
                <div class="feature-item">
                    <span class="feature-icon">✓</span>
                    <span>License valid for {{ $product->max_domains === -1 ? 'unlimited' : $product->max_domains }} {{ Str::plural('domain', $product->max_domains) }}</span>
                </div>
                @endif
            </div>

            <div class="divider"></div>

            <!-- Download Button -->
            @if($downloadUrl)
            <div style="text-align: center;">
                <a href="{{ $downloadUrl }}" class="button">
                    📥 View Order & Download
                </a>
                <p style="color: #666; font-size: 14px; margin-top: 10px;">
                    You can also download the plugin file from the attachment above
                </p>
            </div>
            @endif

            <!-- Installation Instructions -->
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 25px 0; border-radius: 5px;">
                <h4 style="margin-top: 0; color: #856404;">📖 Quick Start Guide:</h4>
                <ol style="margin: 10px 0 0 20px; color: #856404;">
                    <li>Download the plugin file (attached to this email)</li>
                    <li>Upload to your WordPress site via Plugins → Add New → Upload</li>
                    <li>Activate the plugin</li>
                    <li>Enter your license key: <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">{{ $license->license_key ?? 'See above' }}</code></li>
                    <li>Start using the plugin!</li>
                </ol>
            </div>

            <!-- Support -->
            <div style="text-align: center; margin: 30px 0;">
                <p style="color: #666;">Need help? We're here for you!</p>
                <p style="margin: 10px 0;">
                    <a href="{{ route('dashboard') }}" style="color: #667eea; text-decoration: none; font-weight: 600;">
                        Visit Your Dashboard
                    </a>
                    <span style="color: #ccc; margin: 0 10px;">|</span>
                    <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@example.com') }}" style="color: #667eea; text-decoration: none; font-weight: 600;">
                        Contact Support
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>{{ \App\Models\Setting::get('site_name', config('app.name')) }}</strong>
            </p>
            <p style="margin: 0; font-size: 13px;">
                This email was sent because you completed a purchase on our website.
            </p>
            <p style="margin: 10px 0 0 0; font-size: 13px;">
                <a href="{{ route('legal.terms') }}">Terms of Service</a> | 
                <a href="{{ route('legal.privacy') }}">Privacy Policy</a> | 
                <a href="{{ route('legal.refund') }}">Refund Policy</a>
            </p>
        </div>
    </div>
</body>
</html>
