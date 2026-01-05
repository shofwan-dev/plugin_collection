<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        $licenses = License::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->get();

        $orders = Order::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->get();

        // Stats
        $stats = [
            'total_licenses' => $licenses->count(),
            'active_licenses' => $licenses->where('status', 'active')->count(),
            'total_orders' => $orders->count(),
            'products_count' => Product::where('is_active', true)->count(),
        ];

        // Recent licenses (top 5)
        $recent_licenses = $licenses->take(5);

        return view('dashboard', compact('licenses', 'orders', 'stats', 'recent_licenses'));
    }

    public function licenses(): View
    {
        $user = auth()->user();
        
        $licenses = License::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->paginate(10);

        return view('customer.licenses', compact('licenses'));
    }
}
