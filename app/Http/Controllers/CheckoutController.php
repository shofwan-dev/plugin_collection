<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function show(Product $product): View|RedirectResponse|\Illuminate\Http\Response
    {
        Log::info('Checkout show requested for product: ' . $product->slug);

        if (!$product->paddle_price_id) {
            Log::warning('Product missing Paddle Price ID: ' . $product->slug);
            return redirect()->back()->with('error', 'This product is not configured for payment yet (Missing Paddle Price ID).');
        }

        if (empty(config('cashier.seller_id'))) {
            Log::error('Paddle Seller ID is missing in configuration');
            return redirect()->back()->with('error', 'Paddle Payment Gateway is not fully configured (Missing Seller ID). Please contact administrator.');
        }

        try {
            $user = Auth::user();
            
            // Get Paddle settings from database
            $paddleSettings = [
                'client_token' => \App\Models\Setting::get('paddle_client_token', config('cashier.client_token')),
                'sandbox' => \App\Models\Setting::get('paddle_sandbox', config('cashier.sandbox', true)),
            ];
            
            // Prepare checkout using Cashier
            // Only send essential data - customer info is handled by Paddle automatically
            $checkout = $user->checkout([$product->paddle_price_id])
                ->returnTo(route('checkout.success'))
                ->customData([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                ]);

            session(['last_product_id' => $product->id]);

            $response = response()->view('checkout.show', compact('product', 'checkout', 'paddleSettings'));
            
            // Add CSP headers to allow framing by Paddle and allow localhost
            $response->headers->set(
                'Content-Security-Policy',
                "frame-ancestors 'self' http://localhost http://localhost:8000 https://sandbox-buy.paddle.com https://buy.paddle.com"
            );

            return $response;
        } catch (\Exception $e) {
            Log::error('Error creating Paddle checkout: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while preparing secure checkout. Please try again later.');
        }
    }

    /**
     * Process checkout - This might be handled directly by Paddle.js in the view
     * but we keep it for any pre-processing if necessary.
     */
    public function process(Request $request, Product $product): RedirectResponse
    {
        // With Cashier, we usually don't need a custom process method 
        // if we use the checkout component, but we can use it to create a pending order.
        
        $validated = $request->validate([
            'terms' => 'required|accepted',
        ]);

        return redirect()->route('checkout.show', $product->slug)
            ->with('status', 'Please complete your payment.');
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request): View
    {
        // Find order by user or wait for webhook
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('product_id', session('last_product_id'))
            ->latest()
            ->first();

        return view('checkout.success', compact('order'));
    }

    /**
     * Save checkout data to session
     */
    public function saveData(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_number' => 'nullable|string|max:20',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
        ]);

        // Save to session for later use in webhook
        session([
            'checkout_whatsapp' => $validated['whatsapp_number'] ?? '',
            'checkout_customer_name' => $validated['customer_name'] ?? '',
            'checkout_customer_email' => $validated['customer_email'] ?? '',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(): View
    {
        return view('checkout.cancel');
    }
}
