<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; }
        .button { background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            <p>Thank you for your order! Your order has been received and is being processed.</p>
            
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                <li><strong>Product:</strong> {{ $order->product->name }}</li>
                <li><strong>Amount:</strong> ${{ number_format($order->amount, 2) }}</li>
                <li><strong>Status:</strong> {{ ucfirst($order->status) }}</li>
            </ul>

            @if($order->payment_status === 'paid')
            <p>Your license key will be sent to you shortly.</p>
            @else
            <p>Please complete your payment to activate your license.</p>
            @endif

            <p style="margin-top: 30px;">
                <a href="{{ route('dashboard.orders.show', $order) }}" class="button">View Order</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CF7 WhatsApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
