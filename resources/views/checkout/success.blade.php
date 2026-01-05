@extends('layouts.public')

@section('title', 'Payment Successful!')

@section('content')
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Icon -->
                <div class="text-center mb-5 animate-fadeInUp">
                    <div class="success-icon mx-auto mb-4">
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <h1 class="display-4 fw-bold mb-3">Payment Successful!</h1>
                    <p class="lead text-muted">Thank you for your purchase. Your order has been processed successfully.</p>
                </div>

                @if($order && $order->license)
                <!-- License Key Card -->
                <div class="card border-0 shadow-lg mb-4 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="card-body p-5">
                        <h3 class="fw-bold mb-4">
                            <i class="bi bi-key-fill text-primary me-2"></i>
                            Your License Key
                        </h3>
                        
                        <div class="alert alert-primary d-flex align-items-center mb-4">
                            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                            <div>
                                <strong>Important:</strong> Save this license key. You'll need it to activate the plugin.
                            </div>
                        </div>

                        <div class="license-key-box p-4 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <code class="fs-4 fw-bold text-primary mb-0" id="license-key">{{ $order->license->license_key }}</code>
                                <button onclick="copyLicense()" class="btn btn-outline-primary">
                                    <i class="bi bi-clipboard me-2"></i>Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Next Steps -->
                <div class="card border-0 shadow-lg mb-4 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="card-body p-5">
                        <h3 class="fw-bold mb-4">
                            <i class="bi bi-list-check text-success me-2"></i>
                            Next Steps
                        </h3>
                        
                        <div class="steps">
                            <div class="step-item d-flex mb-4">
                                <div class="step-number">
                                    <span class="badge bg-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">1</span>
                                </div>
                                <div class="step-content ms-3 flex-grow-1">
                                    <h5 class="fw-bold mb-2">Check Your Email</h5>
                                    <p class="text-muted mb-2">We've sent your license key and download link to <strong>{{ $order->customer_email ?? auth()->user()->email }}</strong></p>
                                </div>
                            </div>

                            <div class="step-item d-flex mb-4">
                                <div class="step-number">
                                    <span class="badge bg-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">2</span>
                                </div>
                                <div class="step-content ms-3 flex-grow-1">
                                    <h5 class="fw-bold mb-2">Download the Plugin</h5>
                                    <p class="text-muted mb-2">Download the latest version from your dashboard</p>
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                        <i class="bi bi-download me-2"></i>Go to Dashboard
                                    </a>
                                </div>
                            </div>

                            <div class="step-item d-flex mb-4">
                                <div class="step-number">
                                    <span class="badge bg-primary rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">3</span>
                                </div>
                                <div class="step-content ms-3 flex-grow-1">
                                    <h5 class="fw-bold mb-2">Install & Activate</h5>
                                    <p class="text-muted mb-0">Upload the plugin to your WordPress site and enter your license key</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Card -->
                <div class="card border-0 shadow-lg bg-gradient text-white animate-fadeInUp" style="animation-delay: 0.6s; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-3">Need Help?</h5>
                        <p class="mb-3">Our support team is here to help you get started</p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ route('documentation') }}" class="btn btn-light">
                                <i class="bi bi-book me-2"></i>Documentation
                            </a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-light">
                                <i class="bi bi-headset me-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.success-icon i {
    font-size: 8rem;
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.license-key-box {
    border: 2px dashed #667eea;
}

.step-item {
    position: relative;
    padding-bottom: 1rem;
}

.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 19px;
    top: 45px;
    bottom: -15px;
    width: 2px;
    background: #e9ecef;
}
</style>

@push('scripts')
<script>
function copyLicense() {
    const licenseKey = document.getElementById('license-key').textContent;
    navigator.clipboard.writeText(licenseKey).then(() => {
        // Show success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 me-2"></i>Copied!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(err => {
        alert('Failed to copy. Please copy manually.');
    });
}
</script>
@endpush
@endsection
