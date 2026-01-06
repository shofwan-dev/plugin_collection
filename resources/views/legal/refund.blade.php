@extends('legal.layout')

@section('legal-content')
@php
    $siteName = \App\Models\Setting::get('site_name', 'Our Store');
    $contactEmail = \App\Models\Setting::get('contact_email', 'support@example.com');
@endphp

<div class="mb-4">
    <p class="lead">
        We want you to be completely satisfied with your purchase from {{ $siteName }}. This Refund Policy outlines our policies regarding refunds and returns for digital products.
    </p>
</div>

<h2>1. Refund Eligibility</h2>

<h3>1.1 30-Day Money-Back Guarantee</h3>
<p>
    We offer a <strong>30-day money-back guarantee</strong> on all our products. If you are not satisfied with your purchase, you may request a full refund within 30 days of the purchase date.
</p>

<div class="highlight">
    <p><strong>Important:</strong> Refunds are only available if you meet the eligibility criteria outlined below.</p>
</div>

<h3>1.2 Valid Refund Reasons</h3>
<p>Refunds will be granted for the following reasons:</p>
<ul>
    <li><strong>Product Defects:</strong> The product has critical bugs that prevent normal functionality and cannot be fixed by our support team</li>
    <li><strong>Incompatibility:</strong> The product is incompatible with your WordPress version or server requirements, despite meeting system requirements</li>
    <li><strong>Misleading Description:</strong> The product significantly differs from its description on our website</li>
    <li><strong>Non-Delivery:</strong> You did not receive the product download and license key within 48 hours of purchase</li>
    <li><strong>Double Charge:</strong> You were accidentally charged twice for the same product</li>
    <li><strong>Installation Issues:</strong> Technical issues prevent installation, and our support team cannot resolve them</li>
</ul>

<h3>1.3 Non-Refundable Situations</h3>
<p>Refunds will NOT be granted in the following cases:</p>
<ul>
    <li>You simply changed your mind or no longer need the product</li>
    <li>You purchased the wrong product by mistake (we may offer an exchange instead)</li>
    <li>You failed to read the product description or system requirements</li>
    <li>The product lacks features you assumed it would have</li>
    <li>You found a similar product for a lower price elsewhere</li>
    <li>Your site was hacked or infected due to third-party plugins (not our product)</li>
    <li>You violated our Terms of Service or License Agreement</li>
    <li>More than 30 days have passed since the purchase date</li>
</ul>

<h2>2. Refund Process</h2>

<h3>2.1 How to Request a Refund</h3>
<p>To request a refund:</p>
<ol>
    <li><strong>Contact Support:</strong> Email us at <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a> with your order number</li>
    <li><strong>Provide Details:</strong> Explain the reason for your refund request</li>
    <li><strong>Provide Evidence:</strong> Include screenshots,  error messages, or other relevant documentation</li>
    <li><strong>Allow Investigation:</strong> Give our support team 24-48 hours to investigate and attempt to resolve the issue</li>
</ol>

<h3>2.2 Required Information</h3>
<p>Please include the following in your refund request:</p>
<ul>
    <li>Your order number or purchase email</li>
    <li>Detailed description of the problem</li>
    <li>Steps to reproduce the issue (if applicable)</li>
    <li>Screenshots or error messages</li>
    <li>WordPress version and PHP version</li>
    <li>Server environment details (if relevant)</li>
</ul>

<h3>2.3 Investigation Period</h3>
<p>
    Our support team will:
</p>
<ul>
    <li>Review your refund request within 24-48 hours</li>
    <li>Attempt to resolve the issue first</li>
    <li>Request additional information if needed</li>
    <li>Make a decision within 3-5 business days</li>
</ul>

<h2>3. Refund Processing</h2>

<h3>3.1 Approval</h3>
<p>
    If your refund is approved:
</p>
<ul>
    <li>We will send a confirmation email</li>
    <li>Your license will be deactivated</li>
    <li>You must uninstall the product from all websites</li>
    <li>Your access to product updates and support will be revoked</li>
</ul>

<h3>3.2 Payment Reversal</h3>
<p>
    Refunds are processed through the same payment method used for purchase:
</p>
<ul>
    <li><strong>Credit/Debit Card:</strong> 5-10 business days</li>
    <li><strong>PayPal:</strong> 3-5 business days</li>
    <li><strong>Other Methods:</strong> Time may vary depending on your payment provider</li>
</ul>

<div class="highlight">
    <p><strong>Note:</strong> Processing time depends on your bank or payment provider. {{ $siteName }} is not responsible for delays in refund processing by third parties.</p>
</div>

<h3>3.3 Partial Refunds</h3>
<p>
    In some cases, we may offer a partial refund:
</p>
<ul>
    <li>If you used the product for an extended period before requesting a refund</li>
    <li>If you already received value from the product (e.g., used it on multiple sites)</li>
    <li>If support time was extensively used</li>
</ul>

<h2>4. Exchanges and Alternatives</h2>

<h3>4.1 Product Exchange</h3>
<p>
    If you purchased the wrong product, we may offer an exchange instead of a refund:
</p>
<ul>
    <li>Exchange must be of equal or lesser value</li>
    <li>Request must be made within 30 days</li>
    <li>Original product must not have been extensively used</li>
</ul>

<h3>4.2 Upgrade Options</h3>
<p>
    If you need more features, consider upgrading to a higher license tier instead of requesting a refund. Contact us for upgrade pricing.
</p>

<h2>5. Special Circumstances</h2>

<h3>5.1 Subscription Products</h3>
<p>
    For subscription-based products:
</p>
<ul>
    <li>You can request a refund for the current billing period within 30 days</li>
    <li>Future billing will be cancelled</li>
    <li>No refunds for previous billing periods</li>
    <li>Prorated refunds are not available</li>
</ul>

<h3>5.2 Bundle Purchases</h3>
<p>
    For bundled products:
</p>
<ul>
    <li>Refunds must be requested for the entire bundle</li>
    <li>Partial refunds for individual products in a bundle are not available</li>
    <li>Bundle discounts apply only to the full package</li>
</ul>

<h3>5.3 Promotional Purchases</h3>
<p>
    Products purchased during sales or with discount codes:
</p>
<ul>
    <li>Are still eligible for refunds under the same policy</li>
    <li>Refund will be for the amount actually paid</li>
    <li>Promotional codes cannot be reused after refund</li>
</ul>

<h2>6. License Deactivation</h2>

<h3>6.1 Automatic Deactivation</h3>
<p>
    Upon refund approval:
</p>
<ul>
    <li>Your license key will be automatically deactivated</li>
    <li>All domain activations will be removed</li>
    <li>You will lose access to updates and support</li>
    <li>Product files must be deleted from your server</li>
</ul>

<h3>6.2 Compliance Requirement</h3>
<p>
    You must:
</p>
<ul>
    <li>Uninstall the product from all websites</li>
    <li>Delete all product files and backups</li>
    <li>Not use the product after receiving a refund</li>
</ul>

<div class="highlight">
    <p><strong>Warning:</strong> Continued use of the product after receiving a refund is a violation of our Terms of Service and may result in legal action.</p>
</div>

<h2>7. Chargebacks and Disputes</h2>

<h3>7.1 Contact Us First</h3>
<p>
    <strong>Before filing a chargeback or payment dispute, please contact us directly.</strong> Most issues can be resolved quickly through our support team.
</p>

<h3>7.2 Chargeback Consequences</h3>
<p>
    Filing a chargeback without contacting us may result in:
</p>
<ul>
    <li>Immediate account suspension</li>
    <li>License deactivation</li>
    <li>Ban from future purchases</li>
    <li>Legal action for fraudulent chargebacks</li>
</ul>

<h3>7.3 Fraudulent Requests</h3>
<p>
    We reserve the right to deny refunds if:
</p>
<ul>
    <li>We suspect fraud or abuse</li>
    <li>You have a history of excessive refund requests</li>
    <li>You violated our Terms of Service</li>
    <li>You provided false information in your request</li>
</ul>

<h2>8. Support-First Approach</h2>

<h3>8.1 Resolution Attempts</h3>
<p>
    Before approving a refund, we will make reasonable efforts to:
</p>
<ul>
    <li>Identify and fix the issue</li>
    <li>Provide workarounds or alternative solutions</li>
    <li>Offer additional assistance and documentation</li>
    <li>Schedule a support call if necessary</li>
</ul>

<h3>8.2 Cooperation Required</h3>
<p>
    To receive a refund, you must:
</p>
<ul>
    <li>Provide requested information promptly</li>
    <li>Follow troubleshooting steps provided by support</li>
    <li>Allow reasonable time for issue resolution</li>
    <li>Maintain professional communication</li>
</ul>

<h2>9. Exceptions and Edge Cases</h2>

<h3>9.1 Server Compatibility</h3>
<p>
    Refunds for server compatibility issues will only be granted if your server meets our published system requirements and the product still doesn't work.
</p>

<h3>9.2 Third-Party Conflicts</h3>
<p>
    We are not responsible for conflicts with third-party plugins or themes. However, we will make reasonable efforts to help resolve such conflicts.
</p>

<h3>9.3 Customization Requests</h3>
<p>
    Products sold "as-is" are not eligible for refunds based on lack of specific customization options unless explicitly promised in the product description.
</p>

<h2>10. Policy Updates</h2>
<p>
    We reserve the right to update this Refund Policy at any time. Changes will not affect refunds already in progress. The policy in effect at the time of purchase applies to your order.
</p>

<h2>11. Questions and Concerns</h2>
<p>
    If you have questions about our Refund Policy or need assistance with a refund request:
</p>
<ul>
    <li><strong>Email:</strong> <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></li>
    <li><strong>Subject:</strong> "Refund Request - Order #[Your Order Number]"</li>
    <li><strong>Response Time:</strong> Within 24-48 hours</li>
</ul>

<div class="highlight mt-5">
    <p class="mb-3">
        <strong>Our Commitment:</strong>
    </p>
    <p class="mb-0">
        At {{ $siteName }}, customer satisfaction is our priority. While we have a clear refund policy, we are always willing to work with customers to find a fair solution. If you're experiencing issues, please reach out to our support team before requesting a refund.
    </p>
</div>

<div class="highlight mt-4">
    <p class="mb-0">
        <strong>Last Updated:</strong> {{ now()->format('F d, Y') }}<br>
        This Refund Policy is part of our Terms of Service. By making a purchase from {{ $siteName }}, you acknowledge that you have read and agree to this policy.
    </p>
</div>
@endsection
