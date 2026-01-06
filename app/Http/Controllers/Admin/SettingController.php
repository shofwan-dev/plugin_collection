<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Show settings page
     */
    public function index(): View
    {
        $settings = [
            'general' => Setting::getGroup('general'),
            'whatsapp' => Setting::getGroup('whatsapp'),
            'email' => Setting::getGroup('email'),
            'paddle' => Setting::getGroup('paddle'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'site_logo' => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048',
            'whatsapp_api_url' => 'nullable|url',
            'whatsapp_api_key' => 'nullable|string',
            'whatsapp_sender' => 'nullable|string',
            'whatsapp_admin_number' => 'nullable|string',
            'whatsapp_enabled' => 'nullable|boolean',
            'email_host' => 'nullable|string',
            'email_port' => 'nullable|integer',
            'email_encryption' => 'nullable|string|in:tls,ssl,',
            'email_username' => 'nullable|string',
            'email_password' => 'nullable|string',
            'email_from_address' => 'nullable|email',
            'email_from_name' => 'nullable|string',
            'paddle_environment' => 'nullable|string|in:sandbox,live',
            'paddle_seller_id' => 'nullable|string',
            'paddle_api_key' => 'nullable|string',
            'paddle_client_token' => 'nullable|string',
            'paddle_webhook_secret' => 'nullable|string',
            'envato_api_token' => 'nullable|string',
        ]);

        // Handle logo upload using native PHP method (same as ProductController)
        if ($request->hasFile('site_logo') && $request->file('site_logo')->isValid()) {
            try {
                // Ensure logos directory exists
                $logosPath = storage_path('app/public/logos');
                if (!file_exists($logosPath)) {
                    mkdir($logosPath, 0755, true);
                }

                // Delete old logo if exists and path is not empty
                $oldLogo = Setting::get('site_logo');
                if ($oldLogo && !empty($oldLogo)) {
                    $oldFilePath = storage_path('app/public/' . $oldLogo);
                    if (file_exists($oldFilePath)) {
                        try {
                            unlink($oldFilePath);
                            \Log::info('Deleted old logo', ['path' => $oldFilePath]);
                        } catch (\Exception $e) {
                            \Log::warning('Could not delete old logo: ' . $e->getMessage());
                        }
                    }
                }

                $file = $request->file('site_logo');
                
                // Get file info BEFORE moving
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                
                $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                $destinationPath = $logosPath . DIRECTORY_SEPARATOR . $filename;
                
                \Log::info('Attempting to upload logo', [
                    'filename' => $filename,
                    'original' => $originalName,
                    'destination' => $destinationPath,
                ]);

                // Use native PHP move_uploaded_file
                if (move_uploaded_file($file->getPathname(), $destinationPath)) {
                    \Log::info('Logo uploaded successfully', ['path' => $destinationPath]);
                    Setting::set('site_logo', 'logos/' . $filename, 'string', 'general');
                } else {
                    \Log::error('Logo upload failed - move_uploaded_file returned false');
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['site_logo' => 'Failed to upload logo. Please check folder permissions.']);
                }
            } catch (\Exception $e) {
                \Log::error('Logo upload error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['site_logo' => 'Error uploading logo: ' . $e->getMessage()]);
            }
        }

        // Update other settings
        foreach ($validated as $key => $value) {
            if ($value !== null && $key !== 'site_logo') {
                // Determine type and group
                $type = 'string';
                $group = 'general';

                if (str_starts_with($key, 'whatsapp_')) {
                    $group = 'whatsapp';
                    if ($key === 'whatsapp_enabled') {
                        $type = 'boolean';
                    }
                } elseif (str_starts_with($key, 'email_')) {
                    $group = 'email';
                } elseif (str_starts_with($key, 'paddle_')) {
                    $group = 'paddle';
                }

                Setting::set($key, $value, $type, $group);
            }
        }

        return back()->with('success', 'Settings updated successfully');
    }

    /**
     * Test WhatsApp connection
     */
    public function testWhatsApp(): RedirectResponse
    {
        Log::channel('daily')->info('=== TEST WHATSAPP STARTED ===');
        
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);
            $adminNumber = Setting::get('whatsapp_admin_number');

            Log::channel('daily')->info('Test WhatsApp Configuration', [
                'admin_number' => $adminNumber,
                'api_url' => Setting::get('whatsapp_api_url'),
                'api_key' => Setting::get('whatsapp_api_key') ? 'set' : 'not set',
                'sender' => Setting::get('whatsapp_sender'),
                'enabled' => Setting::get('whatsapp_enabled'),
            ]);

            if (!$adminNumber) {
                Log::channel('daily')->error('Test WhatsApp Failed: Admin number not configured');
                return back()->with('error', 'Please configure admin WhatsApp number first');
            }

            $message = "*Test Message*\n\nWhatsApp service is configured correctly! ✅";
            
            Log::channel('daily')->info('Attempting to send test WhatsApp message', [
                'to' => $adminNumber,
                'message' => $message,
            ]);
            
            if ($whatsapp->sendMessage($adminNumber, $message)) {
                Log::channel('daily')->info('=== TEST WHATSAPP SUCCESS ===');
                return back()->with('success', '✅ Test WhatsApp message sent successfully!');
            }

            Log::channel('daily')->error('=== TEST WHATSAPP FAILED ===');
            return back()->with('error', '❌ Failed to send test message. Check logs for details.');

        } catch (\Exception $e) {
            Log::channel('daily')->error('=== TEST WHATSAPP EXCEPTION ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Test Email connection
     */
    public function testEmail(): RedirectResponse
    {
        Log::channel('daily')->info('=== TEST EMAIL STARTED ===');
        
        try {
            $adminEmail = Setting::get('contact_email') ?? auth()->user()->email;

            Log::channel('daily')->info('Test Email Configuration', [
                'to' => $adminEmail,
                'host' => Setting::get('email_host'),
                'port' => Setting::get('email_port'),
                'encryption' => Setting::get('email_encryption'),
                'username' => Setting::get('email_username'),
            ]);

            if (!$adminEmail) {
                Log::channel('daily')->error('Test Email Failed: Email address not configured');
                return back()->with('error', 'Please configure contact email first');
            }

            // Configure mail settings from database
            $fromAddress = Setting::get('email_from_address');
            $fromName = Setting::get('email_from_name', 'CF7 to WhatsApp');
            $host = Setting::get('email_host');
            $port = Setting::get('email_port', 587);
            $encryption = Setting::get('email_encryption', 'tls');
            $username = Setting::get('email_username');
            $password = Setting::get('email_password');

            // Update mail config at runtime
            config([
                'mail.default' => 'smtp',  // Force SMTP mailer
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => $port,
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.from.address' => $fromAddress ?? 'noreply@example.com',
                'mail.from.name' => $fromName,
            ]);

            Log::channel('daily')->info('Attempting to send test email', [
                'to' => $adminEmail,
                'from' => $fromAddress,
                'from_name' => $fromName,
            ]);

            // Send test email
            \Mail::raw('This is a test email from CF7 to WhatsApp Gateway. If you receive this, your SMTP configuration is working correctly! ✅', function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('TestThis is a test email from Email - SMTP Configuration');
            });

            Log::channel('daily')->info('=== TEST EMAIL SUCCESS ===');
            return back()->with('success', '✅ Test email sent successfully to ' . $adminEmail);

        } catch (\Exception $e) {
            Log::channel('daily')->error('=== TEST EMAIL EXCEPTION ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', '❌ Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Test Paddle connection
     */
    public function testPaddle(): RedirectResponse
    {
        Log::channel('daily')->info('=== TEST PADDLE STARTED ===');
        
        try {
            $apiKey = Setting::get('paddle_api_key');
            $clientToken = Setting::get('paddle_client_token');
            $sellerId = Setting::get('paddle_seller_id');
            $environment = Setting::get('paddle_environment', 'sandbox');

            Log::channel('daily')->info('Test Paddle Configuration', [
                'environment' => $environment,
                'api_key' => $apiKey ? 'set (length: ' . strlen($apiKey) . ')' : 'not set',
                'client_token' => $clientToken ? 'set (length: ' . strlen($clientToken) . ')' : 'not set',
                'seller_id' => $sellerId ?? 'not set',
            ]);

            // Validation
            if (!$apiKey) {
                Log::channel('daily')->error('Test Paddle Failed: API key not configured');
                return back()->with('error', '❌ Please configure Paddle API key first');
            }

            if (!$clientToken) {
                Log::channel('daily')->error('Test Paddle Failed: Client token not configured');
                return back()->with('warning', '⚠️ Paddle API Key is valid, but Client Token is missing. Please add Client Token for checkout to work.');
            }

            // Determine API URL based on environment
            $apiUrl = $environment === 'live' 
                ? 'https://api.paddle.com' 
                : 'https://sandbox-api.paddle.com';

            Log::channel('daily')->info('Testing Paddle API', ['api_url' => $apiUrl]);

            // Test 1: Fetch event types (basic connectivity)
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Paddle-Version' => '1',
            ])->timeout(10)->get($apiUrl . '/event-types');

            if (!$response->successful()) {
                Log::channel('daily')->error('=== TEST PADDLE FAILED (Event Types) ===', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                
                $errorMessage = $response->json()['error']['detail'] ?? 'Unable to connect to Paddle API';
                return back()->with('error', '❌ Paddle connection failed: ' . $errorMessage);
            }

            // Test 2: Fetch products (validate permissions and data)
            $productsResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Paddle-Version' => '1',
            ])->timeout(10)->get($apiUrl . '/products', [
                'per_page' => 10
            ]);

            $productCount = 0;
            $priceCount = 0;
            $productsFetchError = null;
            $pricesFetchError = null;
            
            if ($productsResponse->successful()) {
                $productsData = $productsResponse->json();
                $productCount = $productsData['meta']['pagination']['total'] ?? 0;
                
                Log::channel('daily')->info('Paddle Products Fetched', [
                    'count' => $productCount,
                    'data' => $productsData
                ]);

                // Test 3: Fetch prices
                $pricesResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Paddle-Version' => '1',
                ])->timeout(10)->get($apiUrl . '/prices', [
                    'per_page' => 10
                ]);

                if ($pricesResponse->successful()) {
                    $pricesData = $pricesResponse->json();
                    $priceCount = $pricesData['meta']['pagination']['total'] ?? 0;
                    
                    Log::channel('daily')->info('Paddle Prices Fetched', [
                        'count' => $priceCount,
                        'data' => $pricesData
                    ]);
                } else {
                    // Prices fetch failed
                    $pricesFetchError = $pricesResponse->json();
                    Log::channel('daily')->error('Paddle Prices Fetch Failed', [
                        'status' => $pricesResponse->status(),
                        'error' => $pricesFetchError
                    ]);
                }
            } else {
                // Products fetch failed
                $productsFetchError = $productsResponse->json();
                Log::channel('daily')->error('Paddle Products Fetch Failed', [
                    'status' => $productsResponse->status(),
                    'error' => $productsFetchError
                ]);
            }

            // Success with detailed info
            Log::channel('daily')->info('=== TEST PADDLE SUCCESS ===', [
                'status' => $response->status(),
                'environment' => $environment,
                'products' => $productCount,
                'prices' => $priceCount,
            ]);

            $successMessage = '✅ <strong>Paddle Connected Successfully!</strong><br>';
            $successMessage .= '🌍 Environment: <strong>' . strtoupper($environment) . '</strong><br>';
            
            if ($sellerId) {
                $successMessage .= '🏢 Seller ID: <strong>' . $sellerId . '</strong><br>';
            }
            
            // Products count with error detail if 0
            $successMessage .= '📦 Products: <strong>' . $productCount . '</strong>';
            if ($productCount === 0 && $productsFetchError) {
                $errorDetail = $productsFetchError['error']['detail'] ?? 'Unknown error';
                $errorCode = $productsFetchError['error']['code'] ?? 'unknown';
                $successMessage .= ' <small class="text-danger">(Error: ' . htmlspecialchars($errorDetail) . ')</small>';
            }
            $successMessage .= '<br>';
            
            // Prices count with error detail if 0
            $successMessage .= '💰 Prices: <strong>' . $priceCount . '</strong>';
            if ($priceCount === 0 && $pricesFetchError) {
                $errorDetail = $pricesFetchError['error']['detail'] ?? 'Unknown error';
                $successMessage .= ' <small class="text-danger">(Error: ' . htmlspecialchars($errorDetail) . ')</small>';
            }
            $successMessage .= '<br>';
            
            if ($clientToken) {
                $successMessage .= '🔑 Client Token: <strong>✓ Configured</strong><br>';
            } else {
                $successMessage .= '⚠️ Client Token: <strong>Not configured (required for checkout)</strong><br>';
            }
            
            // Add detailed error explanation if count is 0
            if ($productCount === 0 || $priceCount === 0) {
                $successMessage .= '<br><div class="alert alert-warning mt-2" style="font-size: 13px;">';
                $successMessage .= '<strong>⚠️ Products/Prices Count = 0</strong><br>';
                $successMessage .= '<strong>Possible causes:</strong><br>';
                $successMessage .= '<ul style="margin-bottom: 0; padding-left: 20px;">';
                
                if ($productsFetchError || $pricesFetchError) {
                    $successMessage .= '<li><strong>API Permission Issue:</strong> Your API Key does not have READ permission for products/prices. ';
                    $successMessage .= 'Go to Paddle Dashboard → Developer Tools → Authentication → Regenerate API Key with <strong>product:read</strong> and <strong>price:read</strong> permissions.</li>';
                } else {
                    $successMessage .= '<li><strong>Wrong Environment:</strong> Products exist in SANDBOX but you\'re testing LIVE (or vice versa)</li>';
                    $successMessage .= '<li><strong>No Products Created:</strong> No products have been created in Paddle Dashboard for this environment</li>';
                }
                
                $successMessage .= '</ul>';
                $successMessage .= '<br><strong>Note:</strong> Even if count shows 0, checkout will still work if you manually paste the correct Price ID to your Laravel product.';
                $successMessage .= '</div>';
            }
            
            $successMessage .= '<br><small class="text-muted">API Version: 1 | Connection: Active</small>';

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::channel('daily')->error('=== TEST PADDLE EXCEPTION ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }
}
