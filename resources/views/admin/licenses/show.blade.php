@extends('layouts.admin')

@section('title', 'License Details')
@section('page-title', 'License Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.licenses.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to Licenses
    </a>
</div>

<div class="row g-4">
    <!-- Left Column - Main Info -->
    <div class="col-lg-8">
        <!-- License Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-key text-primary me-2"></i>
                    License Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">License Key</label>
                        <div class="d-flex align-items-center gap-2">
                            <code class="bg-light px-3 py-2 rounded flex-grow-1 fw-bold" id="licenseKey">{{ $license->license_key }}</code>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyLicenseKey()" title="Copy to clipboard">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Status</label>
                        <div>
                            @if($license->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success fs-6 px-3 py-2">
                                    <i class="bi bi-check-circle-fill"></i> Active
                                </span>
                            @elseif($license->status === 'expired')
                                <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-2">
                                    <i class="bi bi-x-circle-fill"></i> Expired
                                </span>
                            @elseif($license->status === 'suspended')
                                <span class="badge bg-warning-subtle text-warning border border-warning fs-6 px-3 py-2">
                                    <i class="bi bi-pause-circle-fill"></i> Suspended
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary fs-6 px-3 py-2">
                                    {{ ucfirst($license->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Plan</label>
                        <div class="fw-semibold fs-5">{{ $license->plan->name }}</div>
                        <small class="text-muted">{{ $license->plan->description }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Domain Limit</label>
                        <div class="fw-semibold fs-5">
                            @if($license->max_domains === -1)
                                <i class="bi bi-infinity text-success"></i> Unlimited
                            @else
                                {{ $license->max_domains }} {{ Str::plural('domain', $license->max_domains) }}
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Created Date</label>
                        <div class="fw-semibold">{{ $license->created_at->format('d M Y, H:i') }}</div>
                        <small class="text-muted">{{ $license->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Expiration Date</label>
                        <div class="fw-semibold">
                            @if($license->expires_at)
                                {{ $license->expires_at->format('d M Y') }}
                                @if($license->expires_at->isPast())
                                    <span class="badge bg-danger-subtle text-danger ms-2">Expired</span>
                                @elseif($license->expires_at->diffInDays() <= 30)
                                    <span class="badge bg-warning-subtle text-warning ms-2">Expires soon</span>
                                @endif
                            @else
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-infinity"></i> Lifetime
                                </span>
                            @endif
                        </div>
                        @if($license->expires_at && !$license->expires_at->isPast())
                            <small class="text-muted">{{ $license->expires_at->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Activated Domains Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-globe text-primary me-2"></i>
                        Activated Domains
                    </h5>
                    @php
                        $domains = is_string($license->activated_domains) 
                            ? json_decode($license->activated_domains, true) 
                            : $license->activated_domains;
                        $domainCount = $domains && is_array($domains) ? count($domains) : 0;
                    @endphp
                    <span class="badge bg-primary">
                        {{ $domainCount }} / {{ $license->max_domains === -1 ? '∞' : $license->max_domains }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($domains && is_array($domains) && count($domains) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3 fw-semibold">Domain</th>
                                    <th class="px-4 py-3 fw-semibold">Activated At</th>
                                    <th class="px-4 py-3 fw-semibold">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($domains as $domain)
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-globe2 text-primary me-2"></i>
                                        <span class="fw-semibold">{{ $domain['domain'] ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <small>{{ $domain['activated_at'] ?? 'N/A' }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <code class="bg-light px-2 py-1 rounded small">{{ $domain['ip'] ?? 'N/A' }}</code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-globe display-4 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">No domains activated yet</p>
                        <small class="text-muted">This license hasn't been activated on any domain</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-lightning text-warning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    @if($license->status === 'active')
                        <form method="POST" action="{{ route('admin.licenses.suspend', $license) }}" onsubmit="return confirm('Are you sure you want to suspend this license?')">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-pause-circle"></i> Suspend License
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.licenses.activate', $license) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-play-circle"></i> Activate License
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Info Card -->
        @if($license->user)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person text-info me-2"></i>
                    Customer Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Name</label>
                    <div class="fw-semibold">{{ $license->user->name }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Email</label>
                    <div>
                        <a href="mailto:{{ $license->user->email }}" class="text-decoration-none">
                            {{ $license->user->email }}
                        </a>
                    </div>
                </div>
                <div>
                    <label class="text-muted small mb-1">Member Since</label>
                    <div class="small">{{ $license->user->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Order Info Card -->
        @if($license->order)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-receipt text-success me-2"></i>
                    Order Information
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Order Number</label>
                    <div>
                        <a href="{{ route('admin.orders.show', $license->order) }}" class="text-primary text-decoration-none fw-semibold">
                            {{ $license->order->order_number }}
                            <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Customer Email</label>
                    <div class="small">{{ $license->order->customer_email }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Amount</label>
                    <div class="fw-bold fs-5 text-success">${{ number_format($license->order->amount, 2) }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small mb-1">Payment Status</label>
                    <div>
                        @if($license->order->payment_status === 'paid')
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Paid
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                {{ ucfirst($license->order->payment_status) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-muted small mb-1">Order Date</label>
                    <div class="small">{{ $license->order->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyLicenseKey() {
    const licenseKey = document.getElementById('licenseKey').textContent;
    navigator.clipboard.writeText(licenseKey).then(() => {
        // Show success feedback
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i>';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-primary');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    });
}
</script>
@endpush
@endsection
