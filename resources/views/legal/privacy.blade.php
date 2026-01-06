@extends('legal.layout')

@section('legal-content')
@php
    $siteName = \App\Models\Setting::get('site_name', 'Our Store');
    $companyEmail = \App\Models\Setting::get('company_email', 'support@example.com');
    $companyAddress = \App\Models\Setting::get('company_address', 'Address not set');
@endphp

<div class="mb-4">
    <p class="lead">
        At {{ $siteName }}, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and purchase our products.
    </p>
</div>

<h2>1. Information We Collect</h2>

<h3>1.1 Personal Information</h3>
<p>When you register, make a purchase, or interact with our website, we may collect:</p>
<ul>
    <li><strong>Contact Information:</strong> Name, email address, phone number</li>
    <li><strong>Account Credentials:</strong> Username, password (encrypted)</li>
    <li><strong>Billing Information:</strong> Billing address (payment card details are processed by Paddle and not stored on our servers)</li>
    <li><strong>WhatsApp Number:</strong> If you opt-in for WhatsApp notifications</li>
</ul>

<h3>1.2 Automatic Information</h3>
<p>We automatically collect certain information when you visit our website:</p>
<ul>
    <li><strong>Device Information:</strong> IP address, browser type, device type</li>
    <li><strong>Usage Data:</strong> Pages visited, time spent, click behavior</li>
    <li><strong>Location Data:</strong> Approximate location based on IP address</li>
    <li><strong>Cookies:</strong> See our Cookie Policy below</li>
</ul>

<h3>1.3 Purchase Information</h3>
<p>When you make a purchase, we collect:</p>
<ul>
    <li>Order history and transaction details</li>
    <li>Product licenses and activation information</li>
    <li>Download activity and support requests</li>
    <li>Domain names where our products are installed</li>
</ul>

<h2>2. How We Use Your Information</h2>

<h3>2.1 Service Delivery</h3>
<p>We use your information to:</p>
<ul>
    <li>Process and fulfill your orders</li>
    <li>Deliver products and license keys</li>
    <li>Provide customer support and technical assistance</li>
    <li>Manage your account and preferences</li>
    <li>Send order confirmations and receipts</li>
</ul>

<h3>2.2 Communication</h3>
<p>We may use your contact information to:</p>
<ul>
    <li>Send transactional emails (order updates, license information)</li>
    <li>Provide product updates and feature announcements</li>
    <li>Send WhatsApp notifications (if you opted in)</li>
    <li>Respond to your inquiries and support requests</li>
    <li>Send marketing communications (with your consent)</li>
</ul>

<h3>2.3 Improvement and Analytics</h3>
<p>We use collected data to:</p>
<ul>
    <li>Analyze website usage and user behavior</li>
    <li>Improve our products and services</li>
    <li>Detect and prevent fraud or security issues</li>
    <li>Conduct market research and surveys</li>
</ul>

<h2>3. How We Share Your Information</h2>

<h3>3.1 Service Providers</h3>
<p>We may share your information with trusted third-party service providers:</p>
<ul>
    <li><strong>Paddle:</strong> Payment processing and subscription management</li>
    <li><strong>Email Services:</strong> Transactional and marketing email delivery</li>
    <li><strong>WhatsApp Business API:</strong> For WhatsApp notifications (if opted in)</li>
    <li><strong>Analytics Tools:</strong> Google Analytics, usage tracking</li>
    <li><strong>Cloud Hosting:</strong> Server and database hosting</li>
</ul>

<h3>3.2 Legal Requirements</h3>
<p>We may disclose your information if required by law or in response to:</p>
<ul>
    <li>Legal processes or government requests</li>
    <li>Protection of our rights, privacy, safety, or property</li>
    <li>Investigation of fraud or security issues</li>
    <li>Enforcement of our Terms of Service</li>
</ul>

<h3>3.3 Business  Transfers</h3>
<p>
    If {{ $siteName }} is involved in a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.
</p>

<h2>4. Data Security</h2>

<h3>4.1 Security Measures</h3>
<p>We implement industry-standard security measures to protect your data:</p>
<ul>
    <li><strong>Encryption:</strong> SSL/TLS encryption for data transmission</li>
    <li><strong>Secure Storage:</strong> Encrypted databases and secure servers</li>
    <li><strong>Access Controls:</strong> Limited access to personal information</li>
    <li><strong>Regular Audits:</strong> Security audits and vulnerability assessments</li>
    <li><strong>Password Protection:</strong> Encrypted password storage</li>
</ul>

<h3>4.2 Payment Security</h3>
<p>
    All payment transactions are processed by Paddle, a PCI-DSS compliant payment processor. We do not store or have access to your complete credit card information.
</p>

<h2>5. Cookies and Tracking Technologies</h2>

<h3>5.1 Types of Cookies</h3>
<p>We use the following types of cookies:</p>
<ul>
    <li><strong>Essential Cookies:</strong> Required for website functionality (login, cart)</li>
    <li><strong>Performance Cookies:</strong> Analytics and site performance tracking</li>
    <li><strong>Functional Cookies:</strong> Remember your preferences</li>
    <li><strong>Marketing Cookies:</strong> Track ad campaign effectiveness</li>
</ul>

<h3>5.2 Cookie Management</h3>
<p>
    You can control cookies through your browser settings. Note that disabling cookies may affect website functionality. Visit <a href="https://www.aboutcookies.org" target="_blank">aboutcookies.org</a> for more information.
</p>

<h2>6. Your Rights and Choices</h2>

<h3>6.1 Access and Update</h3>
<p>You have the right to:</p>
<ul>
    <li>Access your personal information</li>
    <li>Update or correct your data</li>
    <li>Download your data (data portability)</li>
    <li>Manage your communication preferences</li>
</ul>

<h3>6.2 Deletion and Restriction</h3>
<p>You can request to:</p>
<ul>
    <li>Delete your account and personal data</li>
    <li>Restrict processing of your data</li>
    <li>Object to data processing for marketing purposes</li>
</ul>

<h3>6.3 Marketing Communications</h3>
<p>
    You can opt-out of marketing emails by clicking the "unsubscribe" link in any marketing email or by updating your preferences in your account dashboard.
</p>

<h3>6.4 WhatsApp Notifications</h3>
<p>
    WhatsApp notifications are opt-in only. You can disable them at any time through your account settings.
</p>

<h2>7. Data Retention</h2>
<p>
    We retain your personal information for as long as necessary to:
</p>
<ul>
    <li>Provide our services and support</li>
    <li>Comply with legal obligations</li>
    <li>Resolve disputes and enforce agreements</li>
    <li>Maintain valid licenses and product ownership records</li>
</ul>

<p>
    Account data is typically retained for 7 years after account closure for legal and accounting purposes.
</p>

<h2>8. Children's Privacy</h2>
<p>
    Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.
</p>

<h2>9. International Data Transfers</h2>
<p>
    Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your data in accordance with this Privacy Policy.
</p>

<h2>10. Third-Party Links</h2>
<p>
    Our website may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. Please review their privacy policies before providing any personal information.
</p>

<h2>11. Product Usage Data</h2>

<h3>11.1 License Validation</h3>
<p>
    Our products may communicate with our servers to:
</p>
<ul>
    <li>Validate license keys and check activation status</li>
    <li>Check for product updates</li>
    <li>Verify domain authorization</li>
    <li>Collect anonymous usage statistics</li>
</ul>

<h3>11.2 Error Reporting</h3>
<p>
    With your permission, our products may send error reports containing:
</p>
<ul>
    <li>Error messages and stack traces</li>
    <li>WordPress and PHP version information</li>
    <li>Plugin configuration (no personal data)</li>
</ul>

<h2>12. Changes to This Privacy Policy</h2>
<p>
    We may update this Privacy Policy from time to time. We will notify you of significant changes by:
</p>
<ul>
    <li>Posting the new Privacy Policy on this page</li>
    <li>Updating the "Last Updated" date</li>
    <li>Sending an email notification for material changes</li>
</ul>

<h2>13. GDPR Compliance (For EU Users)</h2>
<p>
    If you are located in the European Economic Area (EEA), you have additional rights under GDPR:
</p>
<ul>
    <li>Right to be informed about data collection</li>
    <li>Right of access to your personal data</li>
    <li>Right to rectification of inaccurate data</li>
    <li>Right to erasure ("right to be forgotten")</li>
    <li>Right to restrict processing</li>
    <li>Right to data portability</li>
    <li>Right to object to processing</li>
    <li>Rights related to automated decision-making</li>
</ul>

<h2>14. California Privacy Rights (CCPA)</h2>
<p>
    California residents have the right to:
</p>
<ul>
    <li>Know what personal information is collected</li>
    <li>Know if personal information is sold or disclosed</li>
    <li>Say no to the sale of personal information</li>
    <li>Access their personal information</li>
    <li>Equal service and price</li>
</ul>

<div class="highlight">
    <p><strong>Note:</strong> We do NOT sell your personal information to third parties.</p>
</div>

<h2>15. Contact Us</h2>
<p>
    If you have questions about this Privacy Policy or wish to exercise your rights, please contact us:
</p>
<ul>
    <li><strong>Email:</strong> <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></li>
    <li><strong>Data Protection Officer:</strong> <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a></li>
    <li><strong>Website:</strong> <a href="{{ route('home') }}">{{ config('app.url') }}</a></li>
    @if($companyAddress && $companyAddress != 'Address not set')
    <li><strong>Address:</strong> {{ $companyAddress }}</li>
    @endif
</ul>

<div class="highlight mt-5">
    <p class="mb-0">
        <strong>Last Updated:</strong> {{ now()->format('F d, Y') }}<br>
        By using {{ $siteName }}, you consent to the collection and use of information as described in this Privacy Policy.
    </p>
</div>
@endsection
