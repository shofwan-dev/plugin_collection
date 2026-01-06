@extends('layouts.app')

@section('page-title', 'My Orders')

@section('content')
<div class="container-fluid">
    @if($orders->count() > 0)
        <!-- Orders Table Card -->
        <div class="card border-0 shadow-sm" style="animation: fadeIn 0.5s ease;">
            <div class="card-body p-0">
                <!-- Desktop Table View -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Order #</th>
                                <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Product</th>
                                <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Amount</th>
                                <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Status</th>
                                <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Payment</th>
                                <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Date</th>
                                <th class="px-4 py-3 text-uppercase fw-semibold text-end" style="font-size: 0.75rem; letter-spacing: 0.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $index => $order)
                            <tr class="order-row" style="animation: slideInUp 0.3s ease {{$index * 0.05}}s both;">
                                <td class="px-4 py-3">
                                    <a href="{{ route('dashboard.orders.show', $order) }}" class="text-primary text-decoration-none fw-semibold font-monospace">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">
                                        {{ $order->product ? $order->product->name : ($order->plan ? $order->plan->name : 'N/A') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold">${{ number_format($order->amount, 2) }}</div>
                                    <small class="text-muted">{{ strtoupper($order->currency ?? 'USD') }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    @if($order->status === 'completed')
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i> Completed
                                        </span>
                                    @elseif($order->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2">
                                            <i class="bi bi-clock me-1"></i> Pending
                                        </span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> Cancelled
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i> Paid
                                        </span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2">
                                            <i class="bi bi-clock me-1"></i> Pending
                                        </span>
                                    @elseif($order->payment_status === 'failed')
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> Failed
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-medium">{{ $order->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('dashboard.orders.show', $order) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm" style="animation: fadeIn 0.5s ease;">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-cart-x" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                </div>
                <h4 class="fw-bold mb-3">No Orders Yet</h4>
                <p class="text-muted mb-4">You haven't placed any orders yet. Browse our products to get started!</p>
                <a href="{{ route('pricing') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-cart-plus me-2"></i> Browse Products
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .order-row {
        transition: all 0.3s ease;
    }

    .order-row:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .badge {
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
    }
</style>
@endsection
