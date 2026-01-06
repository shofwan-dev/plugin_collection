@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold">My Dashboard</h1>
            <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm mb-2">Total Licenses</div>
                <div class="text-3xl font-bold">{{ $licenses->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm mb-2">Active Licenses</div>
                <div class="text-3xl font-bold">{{ $licenses->where('status', 'active')->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm mb-2">Total Orders</div>
                <div class="text-3xl font-bold">{{ $orders->count() }}</div>
            </div>
        </div>

        <!-- My Licenses -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-semibold">My Licenses</h2>
                <a href="{{ route('dashboard.licenses') }}" class="text-primary-600 hover:underline">View All</a>
            </div>
            <div class="p-6">
                @if($licenses->count() > 0)
                    <div class="space-y-4">
                        @foreach($licenses->take(5) as $license)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-mono font-semibold text-lg">{{ $license->license_key }}</div>
                                    <div class="text-sm text-gray-600">{{ $license->plan->name }}</div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded {{ $license->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($license->status) }}
                                </span>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                <div>Domains: {{ count($license->activated_domains ?? []) }} / {{ $license->max_domains === -1 ? '∞' : $license->max_domains }}</div>
                                <div>Expires: {{ $license->expires_at ? $license->expires_at->format('Y-m-d') : 'Never' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">You don't have any licenses yet</p>
                    <div class="text-center">
                        <a href="{{ route('pricing') }}" class="btn btn-primary">Purchase a License</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Recent Orders</h2>
            </div>
            <div class="p-6">
                @if($orders->count() > 0)
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-600 text-sm border-b">
                                <th class="pb-3">Order #</th>
                                <th class="pb-3">Plan</th>
                                <th class="pb-3">Amount</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders->take(5) as $order)
                            <tr class="border-b">
                                <td class="py-3">{{ $order->order_number }}</td>
                                <td class="py-3">{{ $order->product ? $order->product->name : ($order->plan ? $order->plan->name : 'N/A') }}</td>
                                <td class="py-3">${{ number_format($order->amount, 2) }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs rounded {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-3">{{ $order->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-center py-8">No orders yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
