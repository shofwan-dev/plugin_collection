@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- User Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person text-primary me-2"></i>
                    User Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Full Name</label>
                        <div class="fw-semibold fs-5">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Email Address</label>
                        <div>
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                {{ $user->email }}
                            </a>
                            @if($user->email_verified_at)
                                <i class="bi bi-patch-check-fill text-success" title="Verified"></i>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Role</label>
                        <div>
                            @if($user->is_admin)
                                <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-2">
                                    <i class="bi bi-shield-fill"></i> Admin
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary fs-6 px-3 py-2">
                                    <i class="bi bi-person"></i> Customer
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Member Since</label>
                        <div class="fw-semibold">{{ $user->created_at->format('d M Y') }}</div>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-cart text-success me-2"></i>
                        Orders
                    </h5>
                    <span class="badge bg-primary">{{ $user->orders->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($user->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Order #</th>
                                    <th class="px-4 py-3">Plan</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders->take(5) as $order)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary text-decoration-none">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ $order->plan->name }}</td>
                                    <td class="px-4 py-3">${{ number_format($order->amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        @if($order->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $order->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->orders->count() > 5)
                        <div class="card-footer bg-white border-0 text-center">
                            <small class="text-muted">Showing 5 of {{ $user->orders->count() }} orders</small>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart display-4 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">No orders yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Licenses -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-key text-warning me-2"></i>
                        Licenses
                    </h5>
                    <span class="badge bg-primary">{{ $user->licenses->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($user->licenses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">License Key</th>
                                    <th class="px-4 py-3">Plan</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Domains</th>
                                    <th class="px-4 py-3">Expires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->licenses->take(5) as $license)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.licenses.show', $license) }}" class="text-primary text-decoration-none">
                                            <code class="bg-light px-2 py-1 rounded">{{ $license->license_key }}</code>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ $license->plan->name }}</td>
                                    <td class="px-4 py-3">
                                        @if($license->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($license->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ count($license->activated_domains ?? []) }} / {{ $license->max_domains === -1 ? '∞' : $license->max_domains }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $license->expires_at ? $license->expires_at->format('d M Y') : 'Never' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->licenses->count() > 5)
                        <div class="card-footer bg-white border-0 text-center">
                            <small class="text-muted">Showing 5 of {{ $user->licenses->count() }} licenses</small>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-key display-4 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">No licenses yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Actions -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-lightning text-warning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-arrow-repeat"></i> Toggle Admin Role
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-graph-up text-info me-2"></i>
                    Statistics
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Total Orders</label>
                    <div class="fw-bold fs-4">{{ $user->orders->count() }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Total Spent</label>
                    <div class="fw-bold fs-4 text-success">
                        ${{ number_format($user->orders->where('status', 'completed')->sum('amount'), 2) }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Active Licenses</label>
                    <div class="fw-bold fs-4">{{ $user->licenses->where('status', 'active')->count() }}</div>
                </div>
                <div>
                    <label class="text-muted small mb-1">Last Activity</label>
                    <div class="small">{{ $user->updated_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
