@extends('legal.layout')

@section('legal-content')
@php
    $siteName = \App\Models\Setting::get('site_name', 'Our Store');
    $contactEmail = \App\Models\Setting::get('contact_email', 'support@example.com');
    $contactPhone = \App\Models\Setting::get('contact_phone');
@endphp

<div class="mb-4">
    <p class="lead">
        Welcome to {{ $siteName }}. These Terms of Service govern your use of our website and the purchase of our WordPress plugins and web applications.
    </p>
</div>

<h2>1. Acceptance of Terms</h2>
<p>
    By accessing and using {{ $siteName }}, you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to these Terms of Service, please do not use our services.
</p>

<h2>2. Description of Service</h2>
<p>
    {{ $siteName }} provides digital products including WordPress plugins, web applications, and related software tools. Our products are delivered electronically via download links and license keys.
</p>

<h3>2.1 Product Types</h3>
<ul>
    <li><strong>WordPress Plugins:</strong> Installable software that extends WordPress functionality</li>
    <li><strong>Web Applications:</strong> Standalone web-based software solutions</li>
    <li><strong>Add-ons and Extensions:</strong> Additional features for our main products</li>
    <li><strong>Templates and Themes:</strong> Design files for WordPress and web applications</li>
</ul>

<h2>3. License Agreement</h2>

<h3>3.1 License Grant</h3>
<p>
    Upon purchase, you are granted a non-exclusive, non-transferable license to use our products according to the license type purchased:
</p>

<ul>
    <li><strong>Single Site License:</strong> Use on one (1) domain or subdomain</li>
    <li><strong>Multi-Site License:</strong> Use on up to the specified number of domains</li>
    <li><strong>Developer License:</strong> Use on unlimited client projects (not for resale)</li>
    <li><strong>Lifetime License:</strong> Permanent license with lifetime updates</li>
</ul>

<h3>3.2 License Restrictions</h3>
<p>You may NOT:</p>
<ul>
    <li>Sell, rent, lease, or sublicense the software to third parties</li>
    <li>Redistribute or share your license key with others</li>
    <li>Reverse engineer, decompile, or disassemble the software</li>
    <li>Remove or alter any copyright notices or branding</li>
    <li>Use the software for illegal or unauthorized purposes</li>
</ul>

<h2>4. Purchase and Payment</h2>

<h3>4.1 Payment Processing</h3>
<p>
    All payments are processed securely through our payment partner, Paddle. We accept major credit cards and other payment methods as displayed during checkout.
</p>

<h3>4.2 Pricing</h3>
<p>
    All prices are listed in USD unless otherwise stated. Prices are subject to change without notice, but changes will not affect orders already placed.
</p>

<h3>4.3 Taxes</h3>
<p>
    Prices do not include applicable taxes or VAT. Customers are responsible for any taxes associated with their purchase.
</p>

<h2>5. Delivery</h2>

<h3>5.1 Digital Delivery</h3>
<p>
    Upon successful payment, you will receive:
</p>
<ul>
    <li>Download link to the product files</li>
    <li>License key for product activation</li>
    <li>Access to your customer dashboard</li>
    <li>Email confirmation with order details</li>
</ul>

<h3>5.2 Delivery Timeframe</h3>
<p>
    Products are typically delivered immediately after payment confirmation. If you don't receive your download within 24 hours, please contact our support team at <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>.
</p>

<h2>6. Support and Updates</h2>

<h3>6.1 Support Period</h3>
<p>
    Most licenses include support for a specified period (typically 6-12 months). Lifetime licenses include lifetime support. Support includes:
</p>
<ul>
    <li>Bug fixes and technical assistance</li>
    <li>Product updates and new features</li>
    <li>Documentation and tutorials</li>
    <li>Email support (response within 24-48 hours)</li>
</ul>

<h3>6.2 Support Limitations</h3>
<p>
    Support does NOT include:
</p>
<ul>
    <li>Custom development or modifications</li>
    <li>Installation on your server</li>
    <li>Fixing issues caused by third-party plugins</li>
    <li>General WordPress support</li>
</ul>

<h2>7. Refund Policy</h2>
<p>
    Please refer to our <a href="{{ route('legal.refund') }}">Refund Policy</a> for detailed information about refunds and returns.
</p>

<h2>8. Intellectual Property</h2>

<h3>8.1 Ownership</h3>
<p>
    All products, including code, design, documentation, and branding, are the intellectual property of {{ $siteName }}. Your license grants you the right to use, but not own, the software.
</p>

<h3>8.2 Copyright</h3>
<p>
    All content on this website is Copyright © {{ now()->year }} {{ $siteName }}. All rights reserved.
</p>

<h2>9. Warranties and Disclaimers</h2>

<h3>9.1 Product Warranty</h3>
<p>
    We warrant that our products will perform substantially as described in the product documentation. This warranty is valid for 30 days from the date of purchase.
</p>

<h3>9.2 Disclaimer</h3>
<div class="highlight">
    <p><strong>IMPORTANT:</strong></p>
    <p>
        Our products are provided "AS IS" without warranty of any kind. We do not guarantee that the product will be error-free or uninterrupted. Use at your own risk.
    </p>
</div>

<h2>10. Limitation of Liability</h2>
<p>
    {{ $siteName }} shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use or inability to use our products, even if we have been advised of the possibility of such damages.
</p>

<h2>11. Privacy</h2>
<p>
    Your privacy is important to us. Please review our <a href="{{ route('legal.privacy') }}">Privacy Policy</a> to understand how we collect and use your information.
</p>

<h2>12. Modifications to Terms</h2>
<p>
    We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting to this page. Your continued use of our services after changes constitutes acceptance of the modified terms.
</p>

<h2>13. Account Termination</h2>
<p>
    We reserve the right to suspend or terminate your account and access to our products if you violate these Terms of Service, engage in fraudulent activity, or misuse our software.
</p>

<h2>14. Governing Law</h2>
<p>
    These Terms of Service shall be governed by and construed in accordance with the laws of the jurisdiction in which {{ $siteName }} operates, without regard to its conflict of law provisions.
</p>

<h2>15. Contact Information</h2>
<p>
    If you have any questions about these Terms of Service, please contact us:
</p>
<ul>
    <li><strong>Email:</strong> <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></li>
    <li><strong>Website:</strong> <a href="{{ route('home') }}">{{ config('app.url') }}</a></li>
    @if($companyAddress && $companyAddress != 'Address not set')
    <li><strong>Address:</strong> {{ $companyAddress }}</li>
    @endif
</ul>

<div class="highlight mt-5">
    <p class="mb-0">
        <strong>Last Updated:</strong> {{ now()->format('F d, Y') }}<br>
        By purchasing from {{ $siteName }}, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.
    </p>
</div>
@endsection
