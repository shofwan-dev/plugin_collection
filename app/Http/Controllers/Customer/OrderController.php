<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Show customer's orders
     */
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['plan', 'license'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function show(Order $order): View
    {
        // Check ownership
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        $order->load(['plan', 'license']);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        // Check ownership
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order');
        }

        // Check if order can be cancelled
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled. It may have already been paid or cancelled.');
        }

        // Validate cancel reason
        $validated = $request->validate([
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        // Cancel the order
        if ($order->cancel($validated['cancel_reason'] ?? null)) {
            // Log the cancellation
            \Log::info('Order cancelled by user', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $validated['cancel_reason'] ?? 'No reason provided',
            ]);

            return redirect()->route('customer.orders.index')
                ->with('success', 'Order cancelled successfully');
        }

        return back()->with('error', 'Failed to cancel order');
    }
}
