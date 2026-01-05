@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
            <div class="card-body p-4 text-white">
                <h3 class="fw-bold mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                <p class="mb-0 opacity-90">Here's what's happening with your account today.</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <i class="bi bi-key text-white fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0 small">Total Licenses</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_licenses'] ?? 0 }}</h3>
                    </div>
                </div>
                <a href="{{ route('dashboard.licenses') }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-arrow-right me-1"></i> View Licenses
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-check-circle text-white fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0 small">Active Licenses</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['active_licenses'] ?? 0 }}</h3>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success">
                    <i class="bi bi-arrow-up"></i> All Active
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="bi bi-cart text-white fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0 small">Total Orders</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_orders'] ?? 0 }}</h3>
                    </div>
                </div>
                <a href="{{ route('dashboard.orders.index') }}" class="btn btn-sm btn-outline-warning w-100">
                    <i class="bi bi-arrow-right me-1"></i> View Orders
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-3" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                        <i class="bi bi-box text-white fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-0 small">Products</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['products_count'] ?? 0 }}</h3>
                    </div>
                </div>
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-arrow-right me-1"></i> Browse Products
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Licenses -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-key text-primary me-2"></i> My Recent Licenses
                    </h5>
                    <a href="{{ route('dashboard.licenses') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">License Key</th>
                                <th class="border-0">Plan</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Domains</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_licenses ?? [] as $license)
                            <tr>
                                <td>
                                    <code class="text-primary">{{ Str::limit($license->license_key, 20) }}</code>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $license->plan->name }}</span>
                                </td>
                                <td>
                                    @if($license->status === 'active')
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ ucfirst($license->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $license->activations_count }}/{{ $license->plan->max_domains === -1 ? '∞' : $license->plan->max_domains }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No licenses yet. <a href="{{ route('home') }}">Browse products</a> to get started!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-lightning text-warning me-2"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-cart-plus me-2"></i> Browse Products
                    </a>
                    <a href="{{ route('dashboard.licenses') }}" class="btn btn-outline-primary">
                        <i class="bi bi-key me-2"></i> Manage Licenses
                    </a>
                    <a href="{{ route('dashboard.orders.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-receipt me-2"></i> View Orders
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-gear me-2"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Support Card -->
        <div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
            <div class="card-body text-center p-4">
                <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                <h5 class="fw-bold mb-2">Need Help?</h5>
                <p class="text-muted small mb-3">Our support team is here to help you 24/7</p>
                <a href="{{ route('documentation') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-book me-1"></i> View Documentation
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
