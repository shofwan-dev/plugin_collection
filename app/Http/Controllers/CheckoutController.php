<?php

namespace App\Http\Controllers;

use App\Models\Plan;
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
    public function show(Plan $plan): View|RedirectResponse
    {
        if (!$plan->paddle_price_id) {
            return redirect()->back()->with('error', 'This plan is not configured for payment yet.');
        }

        if (empty(config('cashier.seller_id'))) {
            return redirect()->back()->with('error', 'Paddle Payment Gateway is not fully configured (Missing Seller ID). Please contact administrator.');
        }

        $user = Auth::user();
        
        // Prepare checkout using Cashier
        $checkout = $user->checkout([$plan->paddle_price_id])
            ->returnTo(route('checkout.success'))
            ->customData([
                'plan_id' => $plan->id,
                'user_id' => $user->id,
            ]);

        session(['last_plan_id' => $plan->id]);

        return view('checkout.show', compact('plan', 'checkout'));
    }

    /**
     * Process checkout - This might be handled directly by Paddle.js in the view
     * but we keep it for any pre-processing if necessary.
     */
    public function process(Request $request, Plan $plan): RedirectResponse
    {
        // With Cashier, we usually don't need a custom process method 
        // if we use the checkout component, but we can use it to create a pending order.
        
        $validated = $request->validate([
            'terms' => 'required|accepted',
        ]);

        return redirect()->route('checkout.show', $plan->slug)
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
            ->where('plan_id', session('last_plan_id'))
            ->latest()
            ->first();

        return view('checkout.success', compact('order'));
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(): View
    {
        return view('checkout.cancel');
    }
}
