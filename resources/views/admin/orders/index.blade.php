@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-cart text-primary me-2"></i>
                    All Orders
                </h5>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex gap-2 justify-content-md-end">
                    <select name="status" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    <input type="text" name="search" placeholder="Search..." class="form-control form-control-sm" style="width: 200px;" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">Order #</th>
                        <th class="px-4 py-3 fw-semibold">Customer</th>
                        <th class="px-4 py-3 fw-semibold">Plan</th>
                        <th class="px-4 py-3 fw-semibold">Amount</th>
                        <th class="px-4 py-3 fw-semibold">Payment</th>
                        <th class="px-4 py-3 fw-semibold">Status</th>
                        <th class="px-4 py-3 fw-semibold">Date</th>
                        <th class="px-4 py-3 fw-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary text-decoration-none fw-semibold">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="fw-semibold">{{ $order->customer_name }}</div>
                            <div class="small text-muted">{{ $order->customer_email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark border">{{ $order->plan->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="fw-semibold">${{ number_format($order->amount, 2) }}</span>
                            <div class="small text-muted">{{ $order->currency }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Paid
                                </span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock"></i> Pending
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($order->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </span>
                            @elseif($order->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @elseif($order->status === 'failed')
                                <span class="badge bg-danger-subtle text-danger border border-danger">
                                    <i class="bi bi-x-circle-fill"></i> Failed
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="small">{{ $order->created_at->format('d M Y') }}</div>
                            <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <p class="mb-0">No orders found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
