<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['product', 'plan', 'license', 'user']); // Added product, kept plan for backward compatibility

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['product', 'plan', 'license', 'user']); // Added product, kept plan for backward compatibility
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $order->update(['status' => $newStatus]);

            // Send WhatsApp notification
            try {
                $whatsapp = app(WhatsAppService::class);
                $whatsapp->sendOrderStatusUpdateNotification($order, $newStatus);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp notification for order status update', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return back()->with('success', 'Order status updated successfully');
        }

        return back()->with('info', 'Status unchanged');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:pending,partial,paid,failed,expired,refunded',
        ]);

        $oldPaymentStatus = $order->payment_status;
        $newPaymentStatus = $request->payment_status;

        if ($oldPaymentStatus !== $newPaymentStatus) {
            $order->update(['payment_status' => $newPaymentStatus]);

            // Send WhatsApp notification based on payment status
            try {
                $whatsapp = app(WhatsAppService::class);
                
                switch ($newPaymentStatus) {
                    case 'paid':
                        $whatsapp->sendPaymentSuccessNotification($order);
                        break;
                    case 'refunded':
                        $whatsapp->sendPaymentRefundedNotification($order);
                        break;
                    case 'expired':
                        $whatsapp->sendPaymentExpiredNotification($order);
                        break;
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp notification for payment status update', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return back()->with('success', 'Payment status updated successfully');
        }

        return back()->with('info', 'Payment status unchanged');
    }
}
