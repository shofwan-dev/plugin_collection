<?php
/**
 * Test script untuk mensimulasikan Paddle webhook
 * Jalankan dengan: php test-paddle-webhook.php
 */

// Simulate Paddle transaction.completed webhook payload
$webhookPayload = [
    'event_type' => 'transaction.completed',
    'data' => [
        'id' => 'txn_test_' . uniqid(),
        'status' => 'completed',
        'customer' => [
            'id' => 'ctm_test_123',
            'email' => 'test@example.com',
            'name' => 'Test Customer'
        ],
        'custom_data' => [
            'product_id' => '1', // Sesuaikan dengan product ID yang ada
            'user_id' => '2',
            'customer_name' => 'Test Customer',
            'whatsapp_number' => '628123456789'
        ],
        'details' => [
            'totals' => [
                'total' => 4900, // $49.00 in cents
                'subtotal' => 4900,
                'tax' => 0
            ]
        ],
        'currency_code' => 'USD',
        'created_at' => date('c'),
        'updated_at' => date('c')
    ]
];

// Send POST request to webhook endpoint
$url = 'http://localhost:8000/webhook/paddle'; // Laravel dev server
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: Paddle-Webhook-Test'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo "Sending test webhook to: $url\n";
echo "Payload:\n";
echo json_encode($webhookPayload, JSON_PRETTY_PRINT) . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Response Code: $httpCode\n";
echo "Response Body: $response\n";

if (curl_errno($ch)) {
    echo "Error: " . curl_error($ch) . "\n";
}

curl_close($ch);

echo "\n\nSekarang cek file log Laravel di: storage/logs/laravel.log\n";
