<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        \Log::info('Admin Dashboard Controller Called');
        
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('amount'),
            'active_licenses' => License::where('status', 'active')->count(),
            'total_licenses' => License::count(),
        ];

        \Log::info('Dashboard Stats', $stats);

        $recent_orders = Order::with(['plan', 'license'])
            ->latest()
            ->take(10)
            ->get();

        \Log::info('Recent Orders Count: ' . $recent_orders->count());

        $recent_licenses = License::with(['plan', 'order'])
            ->latest()
            ->take(10)
            ->get();

        \Log::info('Recent Licenses Count: ' . $recent_licenses->count());

        return view('admin.dashboard', compact('stats', 'recent_orders', 'recent_licenses'));
    }
}
