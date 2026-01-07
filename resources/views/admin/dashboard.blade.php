@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@push('styles')
<style>
    .stat-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .table-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<!-- Stats Row -->
<div class="row g-4 mb-4">
    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #6366f1;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Total Orders</p>
                        <h2 class="fw-bold mb-0">{{ $stats['total_orders'] }}</h2>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> All time
                        </small>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <i class="bi bi-cart text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #10b981;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Total Revenue</p>
                        <h2 class="fw-bold mb-0">${{ number_format($stats['total_revenue'], 0) }}</h2>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> Revenue
                        </small>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-currency-dollar text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Licenses -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #f59e0b;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Active Licenses</p>
                        <h2 class="fw-bold mb-0">{{ $stats['active_licenses'] }}</h2>
                        <small class="text-warning">
                            <i class="bi bi-check-circle"></i> Active
                        </small>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="bi bi-check-circle text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Licenses -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #ec4899;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Total Licenses</p>
                        <h2 class="fw-bold mb-0">{{ $stats['total_licenses'] }}</h2>
                        <small class="text-danger">
                            <i class="bi bi-key"></i> All licenses
                        </small>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                        <i class="bi bi-key text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-cart text-primary me-2"></i> Recent Orders
                    </h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-4">Order #</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Amount</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders as $order)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold text-primary">{{ $order->order_number }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($order->customer_email, 30) }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold">${{ number_format($order->amount, 2) }}</span>
                                </td>
                                <td>
                                    @if($order->status === 'completed')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Completed
                                        </span>
                                    @elseif($order->status === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No orders yet</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Licenses -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-key text-warning me-2"></i> Recent Licenses
                    </h5>
                    <a href="{{ route('admin.licenses.index') }}" class="btn btn-sm btn-outline-primary">
                        All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recent_licenses as $license)
                    <a href="{{ route('admin.licenses.show', $license) }}" class="list-group-item list-group-item-action border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-medium mb-1">
                                    <code class="text-primary small">{{ Str::limit($license->license_key, 20) }}</code>
                                </div>
                                <small class="text-muted">{{ $license->product?->name ?? 'No Product' }}</small>
                            </div>
                            <div>
                                @if($license->status === 'active')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                @elseif($license->status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle"></i>
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-dash-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0 small">No licenses yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
            <div class="card-body p-4 text-white">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-lightning me-2"></i> Quick Actions
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </a>
                    <a href="{{ route('admin.licenses.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-key me-1"></i> Manage Licenses
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-gear me-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
