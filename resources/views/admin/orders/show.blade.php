@extends('layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="row g-4">
    <!-- Left Column - Main Info -->
    <div class="col-lg-8">
        <!-- Order Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-receipt text-primary me-2"></i>
                    Order Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Order Number</label>
                        <div class="d-flex align-items-center gap-2">
                            <code class="bg-light px-3 py-2 rounded flex-grow-1 fw-bold fs-5" id="orderNumber">{{ $order->order_number }}</code>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyOrderNumber()" title="Copy to clipboard">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Order Status</label>
                        <div>
                            @if($order->status === 'completed')
                                <span class="badge bg-success-subtle text-success border border-success fs-6 px-3 py-2">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </span>
                            @elseif($order->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning fs-6 px-3 py-2">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @elseif($order->status === 'failed')
                                <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-2">
                                    <i class="bi bi-x-circle-fill"></i> Failed
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary fs-6 px-3 py-2">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Payment Status</label>
                        <div>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="bi bi-check-circle"></i> Paid
                                </span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="bi bi-clock"></i> Pending
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Currency</label>
                        <div class="fw-semibold fs-5">{{ strtoupper($order->currency) }}</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Customer Name</label>
                        <div class="fw-semibold fs-5">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            {{ $order->customer_name }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Customer Email</label>
                        <div>
                            <a href="mailto:{{ $order->customer_email }}" class="text-decoration-none">
                                <i class="bi bi-envelope text-primary me-2"></i>
                                {{ $order->customer_email }}
                            </a>
                        </div>
                    </div>
                    @if($order->user)
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">User Account</label>
                        <div class="small">
                            <span class="badge bg-info-subtle text-info">
                                <i class="bi bi-person-check"></i> Registered User
                            </span>
                        </div>
                    </div>
                    @endif
                </div>

                <hr class="my-4">

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Plan</label>
                        <div class="fw-semibold fs-5">{{ $order->plan->name }}</div>
                        <small class="text-muted">{{ $order->plan->description }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Amount</label>
                        <div class="fw-bold fs-3 text-success">${{ number_format($order->amount, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Order Date</label>
                        <div class="fw-semibold">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Paid At</label>
                        <div class="fw-semibold">
                            @if($order->paid_at)
                                {{ $order->paid_at->format('d M Y, H:i') }}
                                <div class="small text-muted">{{ $order->paid_at->diffForHumans() }}</div>
                            @else
                                <span class="text-muted">Not paid yet</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-credit-card text-success me-2"></i>
                    Payment Information
                </h5>
            </div>
            <div class="card-body p-4">
                @if($order->paddle_transaction_id || $order->paddle_subscription_id)
                    <div class="row g-4">
                        @if($order->paddle_transaction_id)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Paddle Transaction ID</label>
                            <div>
                                <code class="bg-light px-3 py-2 rounded d-inline-block">{{ $order->paddle_transaction_id }}</code>
                            </div>
                        </div>
                        @endif
                        @if($order->paddle_subscription_id)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Paddle Subscription ID</label>
                            <div>
                                <code class="bg-light px-3 py-2 rounded d-inline-block">{{ $order->paddle_subscription_id }}</code>
                            </div>
                        </div>
                        @endif
                        <div class="col-12">
                            <div class="alert alert-info border-0 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <small>Payment processed via Paddle Billing</small>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-credit-card display-4 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">No payment information available</p>
                        <small class="text-muted">Payment details will appear here once processed</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="col-lg-4">
        <!-- License Card -->
        @if($order->license)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-key text-warning me-2"></i>
                    License Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="text-muted small mb-1">License Key</label>
                    <div class="d-flex align-items-center gap-2">
                        <code class="bg-light px-2 py-1 rounded flex-grow-1 small" id="licenseKey">{{ $order->license->license_key }}</code>
                        <button class="btn btn-sm btn-outline-primary" onclick="copyLicenseKey()" title="Copy license key">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Status</label>
                    <div>
                        @if($order->license->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check-circle-fill"></i> Active
                            </span>
                        @elseif($order->license->status === 'expired')
                            <span class="badge bg-danger-subtle text-danger border border-danger">
                                <i class="bi bi-x-circle-fill"></i> Expired
                            </span>
                        @elseif($order->license->status === 'suspended')
                            <span class="badge bg-warning-subtle text-warning border border-warning">
                                <i class="bi bi-pause-circle-fill"></i> Suspended
                            </span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Domains</label>
                    <div class="fw-semibold">
                        {{ count($order->license->activated_domains ?? []) }} / 
                        {{ $order->license->max_domains === -1 ? '∞' : $order->license->max_domains }}
                    </div>
                </div>
                <div>
                    <label class="text-muted small mb-1">Expires</label>
                    <div class="small">
                        @if($order->license->expires_at)
                            {{ $order->license->expires_at->format('d M Y') }}
                        @else
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-infinity"></i> Lifetime
                            </span>
                        @endif
                    </div>
                </div>
                <hr class="my-3">
                <a href="{{ route('admin.licenses.show', $order->license) }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-eye"></i> View License Details
                </a>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-key text-warning me-2"></i>
                    License Information
                </h5>
            </div>
            <div class="card-body p-4 text-center">
                <i class="bi bi-key display-4 text-muted d-block mb-3"></i>
                <p class="text-muted mb-0">No license generated yet</p>
                <small class="text-muted">License will be created after payment confirmation</small>
            </div>
        </div>
        @endif

        <!-- Order Timeline Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history text-info me-2"></i>
                    Order Timeline
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="timeline-icon bg-primary text-white rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-cart-plus small"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">Order Created</div>
                                <div class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($order->paid_at)
                    <div class="timeline-item mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="timeline-icon bg-success text-white rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle small"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">Payment Received</div>
                                <div class="text-muted small">{{ $order->paid_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($order->license)
                    <div class="timeline-item">
                        <div class="d-flex align-items-start gap-3">
                            <div class="timeline-icon bg-warning text-white rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-key small"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">License Generated</div>
                                <div class="text-muted small">{{ $order->license->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyOrderNumber() {
    const orderNumber = document.getElementById('orderNumber').textContent;
    navigator.clipboard.writeText(orderNumber).then(() => {
        showCopyFeedback(event.target.closest('button'));
    });
}

function copyLicenseKey() {
    const licenseKey = document.getElementById('licenseKey').textContent;
    navigator.clipboard.writeText(licenseKey).then(() => {
        showCopyFeedback(event.target.closest('button'));
    });
}

function showCopyFeedback(btn) {
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check"></i>';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-outline-primary');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-primary');
    }, 2000);
}
</script>
@endpush
@endsection
