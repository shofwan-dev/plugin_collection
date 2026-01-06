@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('dashboard.orders.index') }}" class="btn btn-link text-decoration-none p-0 mb-3">
            <i class="bi bi-arrow-left me-2"></i> Back to Orders
        </a>
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="mb-1 fw-bold">Order Details</h2>
                <p class="text-muted mb-0">Order #{{ $order->order_number }}</p>
            </div>
            <div>
                @if($order->payment_status === 'paid')
                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> Paid
                </span>
                @elseif($order->payment_status === 'pending')
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2">
                    <i class="bi bi-clock me-1"></i> Pending Payment
                </span>
                @elseif($order->payment_status === 'failed')
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">
                    <i class="bi bi-x-circle me-1"></i> Failed
                </span>
                @else
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2">
                    {{ ucfirst($order->payment_status) }}
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-receipt text-primary me-2"></i> Order Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <span class="text-muted">Order Number</span>
                                <code class="fw-bold">{{ $order->order_number }}</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <span class="text-muted">Order Date</span>
                                <span class="fw-semibold">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <span class="text-muted">Product</span>
                                <span class="fw-bold text-primary">
                                    {{ $order->product ? $order->product->name : ($order->plan ? $order->plan->name : 'N/A') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <span class="text-muted">Amount</span>
                                <span class="fw-bold fs-5 text-success">${{ number_format($order->amount, 2) }}</span>
                            </div>
                        </div>
                        @if($order->paid_at)
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-success bg-opacity-10 rounded border border-success">
                                <span class="text-success fw-semibold">
                                    <i class="bi bi-check-circle me-2"></i> Paid At
                                </span>
                                <span class="fw-semibold text-success">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- License Information -->
            @if($order->license)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-key text-warning me-2"></i> License Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- License Key -->
                    <div class="mb-4">
                        <label class="form-label text-muted small mb-2">Your License Key:</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" 
                                   value="{{ $order->license->license_key }}" 
                                   id="licenseKey" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyLicenseKey()">
                                <i class="bi bi-clipboard me-1"></i> Copy
                            </button>
                        </div>
                    </div>

                    <!-- License Details -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Status</div>
                                @if($order->license->status === 'active')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> Active
                                </span>
                                @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> {{ ucfirst($order->license->status) }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Domains</div>
                                <div class="fw-bold">
                                    {{ count($order->license->activated_domains ?? []) }} / 
                                    {{ $order->license->max_domains === -1 ? '∞' : $order->license->max_domains }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Expires</div>
                                <div class="fw-bold">
                                    {{ $order->license->expires_at ? $order->license->expires_at->format('d M Y') : 'Never' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('dashboard.licenses') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right me-1"></i> View License Details
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cancellation Reason -->
            @if($order->cancel_reason)
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 border-danger py-3">
                    <h5 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-x-circle me-2"></i> Cancellation Reason
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-2">{{ $order->cancel_reason }}</p>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i> Cancelled at: {{ $order->cancelled_at->format('d M Y, H:i') }}
                    </small>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle text-info me-2"></i> Status
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Order Status -->
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-2">Order Status:</label>
                        <div>
                            @if($order->status === 'completed')
                            <span class="badge bg-success w-100 py-2">
                                <i class="bi bi-check-circle me-1"></i> Completed
                            </span>
                            @elseif($order->status === 'pending')
                            <span class="badge bg-warning w-100 py-2">
                                <i class="bi bi-clock me-1"></i> Pending
                            </span>
                            @elseif($order->status === 'cancelled')
                            <span class="badge bg-danger w-100 py-2">
                                <i class="bi bi-x-circle me-1"></i> Cancelled
                            </span>
                            @else
                            <span class="badge bg-secondary w-100 py-2">
                                {{ ucfirst($order->status) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <label class="form-label text-muted small mb-2">Payment Status:</label>
                        <div>
                            @if($order->payment_status === 'paid')
                            <span class="badge bg-success w-100 py-2">
                                <i class="bi bi-check-circle me-1"></i> Paid
                            </span>
                            @elseif($order->payment_status === 'pending')
                            <span class="badge bg-warning w-100 py-2">
                                <i class="bi bi-clock me-1"></i> Pending
                            </span>
                            @elseif($order->payment_status === 'failed')
                            <span class="badge bg-danger w-100 py-2">
                                <i class="bi bi-x-circle me-1"></i> Failed
                            </span>
                            @elseif($order->payment_status === 'expired')
                            <span class="badge bg-secondary w-100 py-2">
                                <i class="bi bi-hourglass me-1"></i> Expired
                            </span>
                            @else
                            <span class="badge bg-secondary w-100 py-2">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($order->canBeCancelled())
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="card-body p-4 text-white text-center">
                    <i class="bi bi-x-circle fs-1 mb-3"></i>
                    <h6 class="fw-bold mb-3">Cancel Order</h6>
                    <button type="button" 
                            class="btn btn-light w-100 fw-semibold" 
                            data-bs-toggle="modal" 
                            data-bs-target="#cancelModal">
                        <i class="bi bi-x-circle me-2"></i> Cancel This Order
                    </button>
                    <small class="d-block mt-2 opacity-75">Only unpaid orders can be cancelled</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i> Cancel Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('dashboard.orders.cancel', $order) }}">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-2"></i>
                        Are you sure you want to cancel this order? This action cannot be undone.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for cancellation (optional):</label>
                        <textarea name="cancel_reason" 
                                  rows="3" 
                                  class="form-control" 
                                  placeholder="e.g., Changed my mind, Found a better option, etc."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Keep Order
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-check me-1"></i> Yes, Cancel Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyLicenseKey() {
    const input = document.getElementById('licenseKey');
    input.select();
    document.execCommand('copy');
    
    // Show feedback
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check me-1"></i> Copied!';
    btn.classList.remove('btn-outline-primary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-primary');
    }, 2000);
}
</script>
@endpush
@endsection
