<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #EF4444; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Refunded</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            <p>Your order has been refunded.</p>
            
            <h3>Refund Details:</h3>
            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_number }}</li>
                <li><strong>Amount Refunded:</strong> ${{ number_format($order->amount, 2) }}</li>
                @if($reason)
                <li><strong>Reason:</strong> {{ $reason }}</li>
                @endif
            </ul>

            <p>The refund will be processed within 5-10 business days.</p>

            <p>If you have any questions, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CF7 WhatsApp. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
