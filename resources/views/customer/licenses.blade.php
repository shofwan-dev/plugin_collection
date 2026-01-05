@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header with Animation -->
    <div class="mb-4 animate__animated animate__fadeInDown">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">
                    <i class="bi bi-key-fill text-primary me-2"></i> My Licenses
                </h2>
                <p class="text-muted mb-0">Manage your plugin licenses and activated domains</p>
            </div>
            @if($licenses->count() > 0)
            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                <i class="bi bi-shield-check me-1"></i> {{ $licenses->count() }} {{ $licenses->count() === 1 ? 'License' : 'Licenses' }}
            </div>
            @endif
        </div>
    </div>

    @if($licenses->count() > 0)
    <div class="row g-4">
        @foreach($licenses as $index => $license)
        <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
            <div class="card border-0 shadow-sm hover-lift">
                <!-- Card Header with Gradient -->
                <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-box-seam me-2"></i> {{ $license->plan->name }}
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <code class="bg-white bg-opacity-25 px-2 py-1 rounded text-white small">
                                    {{ $license->license_key }}
                                </code>
                                <button type="button" 
                                        class="btn btn-sm btn-light btn-sm-icon"
                                        onclick="copyLicenseKey('{{ $license->license_key }}', this)"
                                        title="Copy License Key">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            @if($license->status === 'active')
                            <span class="badge bg-success px-3 py-2 pulse-animation">
                                <i class="bi bi-check-circle me-1"></i> Active
                            </span>
                            @elseif($license->status === 'suspended')
                            <span class="badge bg-warning px-3 py-2">
                                <i class="bi bi-pause-circle me-1"></i> Suspended
                            </span>
                            @else
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="bi bi-info-circle me-1"></i> {{ ucfirst($license->status) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- License Stats with Icons -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card text-center p-3 rounded hover-scale">
                                <div class="stat-icon mb-2">
                                    <i class="bi bi-server fs-2 text-primary"></i>
                                </div>
                                <div class="text-muted small mb-1">Max Domains</div>
                                <div class="fw-bold fs-4 text-primary">
                                    {{ $license->max_domains === -1 ? '∞' : $license->max_domains }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card text-center p-3 rounded hover-scale">
                                <div class="stat-icon mb-2">
                                    <i class="bi bi-globe fs-2 text-success"></i>
                                </div>
                                <div class="text-muted small mb-1">Activated</div>
                                <div class="fw-bold fs-4 text-success">
                                    @php
                                        $activatedDomains = is_string($license->activated_domains) 
                                            ? json_decode($license->activated_domains, true) 
                                            : $license->activated_domains;
                                        $domainCount = is_array($activatedDomains) ? count($activatedDomains) : 0;
                                    @endphp
                                    {{ $domainCount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card text-center p-3 rounded hover-scale">
                                <div class="stat-icon mb-2">
                                    <i class="bi bi-calendar-check fs-2 text-info"></i>
                                </div>
                                <div class="text-muted small mb-1">Expires</div>
                                <div class="fw-bold fs-4 text-info">
                                    {{ $license->expires_at ? $license->expires_at->format('d M Y') : 'Never' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activated Domains -->
                    @if($domainCount > 0)
                    <div class="border-top pt-4">
                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="bi bi-globe text-primary me-2"></i> 
                            Activated Domains
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $domainCount }}</span>
                        </h6>
                        <div class="domain-list">
                            @foreach($activatedDomains as $domainIndex => $domain)
                            <div class="domain-item p-3 rounded mb-2 animate__animated animate__fadeInLeft" 
                                 style="animation-delay: {{ $domainIndex * 0.05 }}s;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="fw-semibold">{{ is_array($domain) ? ($domain['domain'] ?? $domain) : $domain }}</span>
                                    </div>
                                    @if(is_array($domain) && isset($domain['activated_at']))
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $domain['activated_at'] }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="border-top pt-4">
                        <div class="text-center py-4 empty-state">
                            <i class="bi bi-globe fs-1 text-muted mb-2"></i>
                            <p class="text-muted mb-0">No domains activated yet</p>
                            <small class="text-muted">Activate this license on your WordPress site</small>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Card Footer with Actions -->
                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($license->plan->product && $license->plan->product->file_path)
                        <a href="{{ route('admin.products.download', $license->plan->product) }}" 
                           class="btn btn-primary btn-hover">
                            <i class="bi bi-download me-2"></i> Download Plugin
                        </a>
                        @endif
                        <button type="button" 
                                class="btn btn-outline-secondary btn-hover"
                                onclick="copyLicenseKey('{{ $license->license_key }}', this)">
                            <i class="bi bi-clipboard me-2"></i> Copy License Key
                        </button>
                        <a href="{{ route('dashboard.orders.show', $license->order) }}" 
                           class="btn btn-outline-primary btn-hover ms-auto">
                            <i class="bi bi-receipt me-2"></i> View Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($licenses->hasPages())
    <div class="mt-4 animate__animated animate__fadeIn">
        {{ $licenses->links() }}
    </div>
    @endif
    @else
    <!-- Empty State with Animation -->
    <div class="animate__animated animate__fadeIn">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="empty-state-icon mb-4">
                    <i class="bi bi-key fs-1 text-muted"></i>
                </div>
                <h5 class="fw-bold mb-2">No Licenses Yet</h5>
                <p class="text-muted mb-4">You don't have any licenses. Purchase a plan to get started with our plugin.</p>
                <a href="{{ route('pricing') }}" class="btn btn-primary btn-lg btn-hover">
                    <i class="bi bi-cart me-2"></i> View Pricing Plans
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
/* Card Hover Effects */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

/* Stat Cards */
.stat-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
}

/* Domain List */
.domain-list {
    max-height: 300px;
    overflow-y: auto;
}

.domain-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.domain-item:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    transform: translateX(5px);
}

/* Button Hover Effects */
.btn-hover {
    transition: all 0.3s ease;
}

.btn-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-sm-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Pulse Animation for Active Badge */
.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
    }
}

/* Empty State Animation */
.empty-state-icon i {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Scrollbar Styling */
.domain-list::-webkit-scrollbar {
    width: 6px;
}

.domain-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.domain-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.domain-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function copyLicenseKey(key, btn) {
    // Create temporary input
    const temp = document.createElement('input');
    temp.value = key;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);
    
    // Visual feedback
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check me-2"></i> Copied!';
    btn.classList.remove('btn-outline-secondary', 'btn-light');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        if(btn.classList.contains('btn-sm-icon')) {
            btn.classList.add('btn-light');
        } else {
            btn.classList.add('btn-outline-secondary');
        }
    }, 2000);
}

// Add smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
</script>
@endpush
@endsection
