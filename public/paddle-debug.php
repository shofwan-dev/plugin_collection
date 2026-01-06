<?php
/**
 * Paddle API Debug Tool
 * Access via: http://localhost/paddle-debug.php
 * 
 * This will show detailed API responses from Paddle
 */

// ========================================
// CONFIGURATION - Edit these values
// ========================================
$API_KEY = 'YOUR_API_KEY_HERE';  // Paste your API Key here
$ENVIRONMENT = 'live';  // 'sandbox' or 'live'

// ========================================
// DO NOT EDIT BELOW THIS LINE
// ========================================

$apiUrl = $ENVIRONMENT === 'live' 
    ? 'https://api.paddle.com' 
    : 'https://sandbox-api.paddle.com';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paddle API Debug Tool</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        
        .config-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            font-weight: 600;
            color: #495057;
        }
        
        .config-value {
            font-family: monospace;
            color: #667eea;
        }
        
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status.warning {
            background: #fff3cd;
            color: #856404;
        }
        
        pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .endpoint-test {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .endpoint-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .method {
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>🔍 Paddle API Debug Tool</h1>
            <p class="subtitle">Detailed diagnostics for Paddle API integration</p>
            
            <?php if ($API_KEY === 'YOUR_API_KEY_HERE'): ?>
                <div class="alert error">
                    <strong>⚠️ Configuration Required!</strong><br>
                    Please edit this file (<code>paddle-debug.php</code>) and replace:<br>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li><code>$API_KEY = 'YOUR_API_KEY_HERE';</code> with your actual API Key</li>
                        <li><code>$ENVIRONMENT = 'live';</code> with 'sandbox' or 'live'</li>
                    </ul>
                </div>
            <?php else: ?>
                
                <div class="config-section">
                    <h3 style="margin-bottom: 15px;">📋 Current Configuration</h3>
                    <div class="config-item">
                        <span class="config-label">Environment:</span>
                        <span class="config-value"><?= strtoupper($ENVIRONMENT) ?></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">API URL:</span>
                        <span class="config-value"><?= $apiUrl ?></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">API Key:</span>
                        <span class="config-value"><?= substr($API_KEY, 0, 20) ?>...<?= substr($API_KEY, -10) ?></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">API Key Length:</span>
                        <span class="config-value"><?= strlen($API_KEY) ?> characters</span>
                    </div>
                </div>

                <?php
                // Function to make API call
                function makeApiCall($url, $apiKey) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $apiKey,
                        'Paddle-Version: 1',
                    ]);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $error = curl_error($ch);
                    curl_close($ch);
                    
                    return [
                        'http_code' => $httpCode,
                        'response' => $response,
                        'error' => $error,
                        'success' => $httpCode >= 200 && $httpCode < 300
                    ];
                }

                // Test 1: Event Types (Basic connectivity)
                echo '<div class="endpoint-test">';
                echo '<div class="endpoint-title"><span class="method">GET</span> Event Types (Basic Connectivity Test)</div>';
                
                $eventTypesResult = makeApiCall($apiUrl . '/event-types', $API_KEY);
                
                if ($eventTypesResult['success']) {
                    echo '<span class="status success">✓ SUCCESS (HTTP ' . $eventTypesResult['http_code'] . ')</span>';
                    $data = json_decode($eventTypesResult['response'], true);
                    echo '<p style="margin-top: 10px; color: #28a745;">✅ API connection is working! Found ' . count($data['data'] ?? []) . ' event types.</p>';
                } else {
                    echo '<span class="status error">✗ FAILED (HTTP ' . $eventTypesResult['http_code'] . ')</span>';
                    echo '<p style="margin-top: 10px; color: #dc3545;">❌ Cannot connect to Paddle API</p>';
                    if ($eventTypesResult['error']) {
                        echo '<p style="color: #dc3545;">Error: ' . htmlspecialchars($eventTypesResult['error']) . '</p>';
                    }
                }
                
                echo '<pre>' . htmlspecialchars(json_encode(json_decode($eventTypesResult['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                echo '</div>';

                // Test 2: Products
                echo '<div class="endpoint-test">';
                echo '<div class="endpoint-title"><span class="method">GET</span> Products</div>';
                
                $productsResult = makeApiCall($apiUrl . '/products?per_page=20', $API_KEY);
                
                if ($productsResult['success']) {
                    $productsData = json_decode($productsResult['response'], true);
                    $productCount = $productsData['meta']['pagination']['total'] ?? 0;
                    
                    echo '<span class="status success">✓ SUCCESS (HTTP ' . $productsResult['http_code'] . ')</span>';
                    echo '<p style="margin-top: 10px;"><strong>Total Products: ' . $productCount . '</strong></p>';
                    
                    if ($productCount === 0) {
                        echo '<div class="alert warning" style="margin-top: 10px;">';
                        echo '<strong>⚠️ No Products Found!</strong><br>';
                        echo 'Products count is 0. This could mean:<br>';
                        echo '<ul style="margin-left: 20px; margin-top: 5px;">';
                        echo '<li>Products were created in a different environment (check sandbox vs live)</li>';
                        echo '<li>Products have not been created yet in Paddle Dashboard</li>';
                        echo '<li>API Key doesn\'t have permission to read products</li>';
                        echo '</ul>';
                        echo '</div>';
                    } else {
                        echo '<h4 style="margin-top: 15px;">Products List:</h4>';
                        foreach ($productsData['data'] as $product) {
                            echo '<div style="padding: 10px; background: white; border-radius: 5px; margin: 5px 0;">';
                            echo '<strong>' . htmlspecialchars($product['name']) . '</strong><br>';
                            echo '<small>ID: <code>' . htmlspecialchars($product['id']) . '</code></small><br>';
                            echo '<small>Status: ' . htmlspecialchars($product['status']) . '</small>';
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<span class="status error">✗ FAILED (HTTP ' . $productsResult['http_code'] . ')</span>';
                    $errorData = json_decode($productsResult['response'], true);
                    
                    echo '<div class="alert error" style="margin-top: 10px;">';
                    echo '<strong>❌ Cannot Fetch Products</strong><br>';
                    echo 'Error: ' . htmlspecialchars($errorData['error']['detail'] ?? 'Unknown error') . '<br>';
                    echo '<br><strong>Possible causes:</strong><br>';
                    echo '<ul style="margin-left: 20px;">';
                    echo '<li>API Key doesn\'t have "Read" permission for products</li>';
                    echo '<li>Wrong environment (using sandbox key for live, or vice versa)</li>';
                    echo '<li>API Key is invalid or expired</li>';
                    echo '</ul>';
                    echo '</div>';
                }
                
                echo '<pre>' . htmlspecialchars(json_encode(json_decode($productsResult['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                echo '</div>';

                // Test 3: Prices
                echo '<div class="endpoint-test">';
                echo '<div class="endpoint-title"><span class="method">GET</span> Prices</div>';
                
                $pricesResult = makeApiCall($apiUrl . '/prices?per_page=20', $API_KEY);
                
                if ($pricesResult['success']) {
                    $pricesData = json_decode($pricesResult['response'], true);
                    $priceCount = $pricesData['meta']['pagination']['total'] ?? 0;
                    
                    echo '<span class="status success">✓ SUCCESS (HTTP ' . $pricesResult['http_code'] . ')</span>';
                    echo '<p style="margin-top: 10px;"><strong>Total Prices: ' . $priceCount . '</strong></p>';
                    
                    if ($priceCount === 0) {
                        echo '<div class="alert warning" style="margin-top: 10px;">';
                        echo '<strong>⚠️ No Prices Found!</strong><br>';
                        echo 'Prices count is 0. Please create prices for your products in Paddle Dashboard.';
                        echo '</div>';
                    } else {
                        echo '<h4 style="margin-top: 15px;">Prices List:</h4>';
                        foreach ($pricesData['data'] as $price) {
                            echo '<div style="padding: 10px; background: white; border-radius: 5px; margin: 5px 0;">';
                            echo '<strong>' . htmlspecialchars($price['description'] ?? 'Price') . '</strong><br>';
                            echo '<small>Price ID: <code>' . htmlspecialchars($price['id']) . '</code></small><br>';
                            echo '<small>Amount: ' . htmlspecialchars($price['unit_price']['amount']) . ' ' . htmlspecialchars($price['unit_price']['currency_code']) . '</small><br>';
                            echo '<small>Product ID: <code>' . htmlspecialchars($price['product_id']) . '</code></small><br>';
                            echo '<small>Status: ' . htmlspecialchars($price['status']) . '</small>';
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<span class="status error">✗ FAILED (HTTP ' . $pricesResult['http_code'] . ')</span>';
                }
                
                echo '<pre>' . htmlspecialchars(json_encode(json_decode($pricesResult['response']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . '</pre>';
                echo '</div>';

                // Recommendations
                echo '<div class="card" style="background: #f8f9fa; margin-top: 20px;">';
                echo '<h3 style="margin-bottom: 15px;">💡 Recommendations</h3>';
                
                if (!$eventTypesResult['success']) {
                    echo '<p>❌ <strong>Fix API Connection First:</strong> Check your API Key and environment settings.</p>';
                } elseif (!$productsResult['success']) {
                    echo '<p>⚠️ <strong>API Key Permissions:</strong> Your API Key needs "Read" permission. Regenerate it in Paddle Dashboard with correct permissions.</p>';
                } elseif ($productCount === 0) {
                    echo '<p>⚠️ <strong>Wrong Environment:</strong> You might be using LIVE API Key but products are in SANDBOX (or vice versa).</p>';
                    echo '<p>📝 <strong>Action:</strong> Double-check that products exist in the <strong>' . strtoupper($ENVIRONMENT) . '</strong> environment in Paddle Dashboard.</p>';
                } else {
                    echo '<p>✅ <strong>All Good!</strong> Your Paddle integration is working correctly.</p>';
                    echo '<p>📋 Copy the Price IDs above and paste them into your Laravel products.</p>';
                }
                
                echo '</div>';
                
                ?>
            <?php endif; ?>
            
            <div style="margin-top: 30px; text-align: center;">
                <a href="/" class="btn">← Back to Home</a>
                <a href="?refresh=<?= time() ?>" class="btn" style="background: #28a745;">🔄 Refresh Test</a>
            </div>
        </div>
    </div>
</body>
</html>
