@extends('layouts.public')

@section('title', 'Documentation - CF7 to WhatsApp')
@section('description', 'Complete guide to installing and using CF7 to WhatsApp plugin')

@push('styles')
<style>
    .doc-card {
        transition: all 0.3s;
        border-left: 4px solid var(--primary);
    }

    .doc-card:hover {
        transform: translateX(5px);
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
    }

    .code-block {
        background: #1e293b;
        color: #e2e8f0;
        padding: 1rem;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
    }

    .step-number {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="gradient-bg text-white py-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold mb-4">Documentation</h1>
                <p class="lead fs-4 opacity-90">
                    Complete guide to installing and using CF7 to WhatsApp plugin
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Documentation Content -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <!-- Installation -->
                <div class="card doc-card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 fw-bold mb-4">
                            <i class="bi bi-download text-primary"></i> Installation
                        </h2>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3">
                                <div class="step-number">1</div>
                                <div>
                                    <strong>Download</strong> the plugin from your account dashboard
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="step-number">2</div>
                                <div>
                                    <strong>Upload</strong> to WordPress via <code>Plugins → Add New → Upload Plugin</code>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="step-number">3</div>
                                <div>
                                    <strong>Activate</strong> the plugin from the Plugins page
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="step-number">4</div>
                                <div>
                                    <strong>Enter your license key</strong> in <code>CF7 to WhatsApp → License</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuration -->
                <div class="card doc-card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 fw-bold mb-4">
                            <i class="bi bi-gear text-primary"></i> Configuration
                        </h2>
                        
                        <h3 class="h5 fw-bold mb-3">1. API Settings</h3>
                        <ul class="mb-4">
                            <li class="mb-2">Go to <code>CF7 to WhatsApp → Settings</code></li>
                            <li class="mb-2">Enter your MPWA API URL and Token</li>
                            <li class="mb-2">Add your WhatsApp admin numbers</li>
                        </ul>

                        <h3 class="h5 fw-bold mb-3">2. Message Template</h3>
                        <p class="mb-3">Customize your WhatsApp message template using placeholders:</p>
                        <div class="code-block mb-3">
                            <div class="mb-2"><code>[your-name]</code> - Customer name</div>
                            <div class="mb-2"><code>[your-email]</code> - Customer email</div>
                            <div class="mb-2"><code>[your-message]</code> - Customer message</div>
                            <div><code>[any-cf7-field]</code> - Any Contact Form 7 field name</div>
                        </div>

                        <div class="alert alert-info border-0">
                            <i class="bi bi-info-circle"></i>
                            <strong>Tip:</strong> You can use any Contact Form 7 field name as a placeholder in your message template.
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="card doc-card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 fw-bold mb-4">
                            <i class="bi bi-star text-primary"></i> Features
                        </h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Automatic WhatsApp notifications</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Multiple admin numbers support</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Custom message templates</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Comprehensive logging</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Resend failed messages</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span>Per-domain licensing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="card doc-card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 fw-bold mb-4">
                            <i class="bi bi-tools text-primary"></i> Troubleshooting
                        </h2>
                        
                        <div class="mb-4">
                            <h3 class="h5 fw-bold mb-3">
                                <i class="bi bi-question-circle text-warning"></i> Messages not sending?
                            </h3>
                            <ul>
                                <li class="mb-2">Check your MPWA API credentials</li>
                                <li class="mb-2">Verify WhatsApp numbers are in correct format (e.g., 628123456789)</li>
                                <li class="mb-2">Check the logs for error messages</li>
                                <li class="mb-2">Ensure your server can make outbound HTTP requests</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h3 class="h5 fw-bold mb-3">
                                <i class="bi bi-question-circle text-warning"></i> License activation failed?
                            </h3>
                            <ul>
                                <li class="mb-2">Ensure you're using the correct license key</li>
                                <li class="mb-2">Check if domain limit is reached</li>
                                <li class="mb-2">Verify license hasn't expired or been suspended</li>
                                <li class="mb-2">Contact support if the issue persists</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="h5 fw-bold mb-3">
                                <i class="bi bi-question-circle text-warning"></i> Plugin not appearing in WordPress?
                            </h3>
                            <ul>
                                <li class="mb-2">Check if the plugin was uploaded correctly</li>
                                <li class="mb-2">Verify WordPress version compatibility</li>
                                <li class="mb-2">Check for PHP errors in WordPress debug log</li>
                                <li class="mb-2">Ensure Contact Form 7 is installed and active</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Need Help -->
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white;">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-headset display-4 mb-3"></i>
                        <h2 class="h3 fw-bold mb-3">Need Help?</h2>
                        <p class="mb-4 opacity-90">
                            Our support team is here to help you 24/7. Don't hesitate to reach out!
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="mailto:support@cf7whatsapp.com" class="btn btn-light btn-lg px-5 fw-bold">
                                <i class="bi bi-envelope"></i> Email Support
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg px-5 fw-bold">
                                <i class="bi bi-arrow-left"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Quick Links -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-book display-4 text-primary mb-3"></i>
                    <h3 class="h5 fw-bold mb-2">Getting Started</h3>
                    <p class="text-muted mb-3">New to the plugin? Start here for a quick setup guide.</p>
                    <a href="#" class="btn btn-outline-primary">Learn More</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-code-square display-4 text-success mb-3"></i>
                    <h3 class="h5 fw-bold mb-2">API Reference</h3>
                    <p class="text-muted mb-3">Detailed API documentation for developers.</p>
                    <a href="#" class="btn btn-outline-success">View API Docs</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-question-circle display-4 text-warning mb-3"></i>
                    <h3 class="h5 fw-bold mb-2">FAQ</h3>
                    <p class="text-muted mb-3">Find answers to commonly asked questions.</p>
                    <a href="#" class="btn btn-outline-warning">View FAQ</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
