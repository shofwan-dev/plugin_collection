@extends('layouts.admin')

@section('page-title', 'Settings')

@section('content')

<!-- General Settings Form -->
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-globe text-primary me-2"></i> General Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Site Name</label>
                            <input type="text" name="site_name" value="{{ $settings['general']['site_name'] ?? '' }}" 
                                   class="form-control" placeholder="CF7 to WhatsApp">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Site Description</label>
                            <textarea name="site_description" rows="3" class="form-control" 
                                      placeholder="Enter site description">{{ $settings['general']['site_description'] ?? '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Email</label>
                            <input type="email" name="contact_email" value="{{ $settings['general']['contact_email'] ?? '' }}" 
                                   class="form-control" placeholder="contact@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ $settings['general']['contact_phone'] ?? '' }}" 
                                   class="form-control" placeholder="+1234567890">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Save General Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Branding -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-image text-warning me-2"></i> Branding
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Site Logo</label>
                        
                        @if($settings['general']['site_logo'] ?? false)
                        <div class="mb-3 p-3 bg-light rounded border">
                            <p class="text-muted small mb-2">Current Logo:</p>
                            <img src="{{ asset('storage/' . $settings['general']['site_logo']) }}" 
                                 alt="Current Logo" 
                                 class="img-fluid"
                                 style="max-height: 80px; object-fit: contain;"
                                 id="current-logo">
                        </div>
                        @endif

                        <input type="file" 
                               name="site_logo" 
                               id="site_logo"
                               accept="image/jpeg,image/jpg,image/png,image/svg+xml"
                               class="form-control"
                               onchange="previewImage(this, 'logo-preview')">
                        <small class="text-muted d-block mt-1">
                            PNG, JPG, SVG (max 2MB)<br>
                            Logo will be used for navbar, footer, and favicon.
                        </small>
                        <div id="logo-preview" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- WhatsApp Settings Form (Separate) -->
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')
    
    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-whatsapp text-success me-2"></i> WhatsApp Settings
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">API URL</label>
                            <input type="url" name="whatsapp_api_url" value="{{ $settings['whatsapp']['whatsapp_api_url'] ?? '' }}" 
                                   class="form-control" placeholder="https://mpwa.mutekar.com/send-message">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">API Key</label>
                            <input type="text" name="whatsapp_api_key" value="{{ $settings['whatsapp']['whatsapp_api_key'] ?? '' }}" 
                                   class="form-control" placeholder="Your API key">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sender Number</label>
                            <input type="text" name="whatsapp_sender" value="{{ $settings['whatsapp']['whatsapp_sender'] ?? '' }}" 
                                   class="form-control" placeholder="6281234567890">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admin Number</label>
                            <input type="text" name="whatsapp_admin_number" value="{{ $settings['whatsapp']['whatsapp_admin_number'] ?? '' }}" 
                                   class="form-control" placeholder="6281234567890">
                            <small class="text-muted">Admin will receive test messages and order notifications</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="whatsapp_enabled" value="1" 
                                       {{ ($settings['whatsapp']['whatsapp_enabled'] ?? false) ? 'checked' : '' }} id="whatsappEnabled">
                                <label class="form-check-label fw-semibold" for="whatsappEnabled">
                                    Enable WhatsApp Notifications
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Save WhatsApp Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Test WhatsApp (Separate Form) -->
<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-success bg-opacity-10 border-success border-2">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-whatsapp text-success fs-2"></i>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold text-success">Test WhatsApp Gateway</p>
                        <small class="text-muted">Send test message to Admin Number</small>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.test-whatsapp') }}">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send me-2"></i> Test WhatsApp
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Settings Form (Separate) -->
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')
    
    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-envelope text-info me-2"></i> Email Settings (SMTP)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">SMTP Host</label>
                            <input type="text" name="email_host" value="{{ $settings['email']['email_host'] ?? '' }}" 
                                   class="form-control" placeholder="smtp.gmail.com">
                            <small class="text-muted">Example: smtp.gmail.com, smtp.mailtrap.io</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Port</label>
                            <input type="number" name="email_port" value="{{ $settings['email']['email_port'] ?? '587' }}" 
                                   class="form-control" placeholder="587">
                            <small class="text-muted">587 or 465</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Encryption</label>
                            <select name="email_encryption" class="form-select">
                                <option value="tls" {{ ($settings['email']['email_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings['email']['email_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="" {{ ($settings['email']['email_encryption'] ?? '') === '' ? 'selected' : '' }}>None</option>
                            </select>
                            <small class="text-muted">TLS (port 587) or SSL (port 465)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="email_username" value="{{ $settings['email']['email_username'] ?? '' }}" 
                                   class="form-control" placeholder="your-email@gmail.com">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="email_password" value="{{ $settings['email']['email_password'] ?? '' }}" 
                                   class="form-control" placeholder="Your SMTP password or app password">
                            <small class="text-muted">For Gmail, use App Password (not your account password)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Address</label>
                            <input type="email" name="email_from_address" value="{{ $settings['email']['email_from_address'] ?? '' }}" 
                                   class="form-control" placeholder="noreply@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Name</label>
                            <input type="text" name="email_from_name" value="{{ $settings['email']['email_from_name'] ?? '' }}" 
                                   class="form-control" placeholder="CF7 to WhatsApp">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Save Email Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Test Email (Separate Form) -->
<div class="row g-4 mt-1 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-info bg-opacity-10 border-info border-2">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-envelope text-info fs-2"></i>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold text-info">Test Email Configuration</p>
                        <small class="text-muted">Send test email to Contact Email</small>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.test-email') }}">
                        @csrf
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-envelope-check me-2"></i> Test Email
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Paddle Settings Form (Separate) -->
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')
    
    <div class="row g-4 mt-1">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-credit-card text-primary me-2"></i> Paddle Payment Gateway
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill me-3"></i>
                            <div>
                                <p class="mb-1 fw-semibold">Paddle Integration</p>
                                <small>Get your credentials from <a href="https://vendors.paddle.com/" target="_blank" class="text-decoration-underline">Paddle Dashboard</a> → Developer Tools → Authentication</small><br>
                                <small>See <code>PADDLE_SETUP.md</code> for detailed setup instructions</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Environment</label>
                            <select name="paddle_environment" class="form-select">
                                <option value="sandbox" {{ ($settings['paddle']['paddle_environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>
                                    Sandbox (Testing)
                                </option>
                                <option value="live" {{ ($settings['paddle']['paddle_environment'] ?? '') === 'live' ? 'selected' : '' }}>
                                    Live (Production)
                                </option>
                            </select>
                            <small class="text-muted">Use Sandbox for testing, Live for production</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Paddle Seller ID (Vendor ID)</label>
                            <input type="text" name="paddle_seller_id" value="{{ $settings['paddle']['paddle_seller_id'] ?? '' }}" 
                                   class="form-control font-monospace" placeholder="12345">
                            <small class="text-muted">Click the '...' icon in top-left sidebar of Paddle Dashboard to see your Seller ID</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">API Key</label>
                            <input type="text" name="paddle_api_key" value="{{ $settings['paddle']['paddle_api_key'] ?? '' }}" 
                                   class="form-control font-monospace" placeholder="pdl_sdbx_apikey_... or pdl_live_apikey_...">
                            <small class="text-muted">From Developer Tools → Authentication → API Keys (create new if needed)</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Client Token</label>
                            <input type="text" name="paddle_client_token" value="{{ $settings['paddle']['paddle_client_token'] ?? '' }}" 
                                   class="form-control font-monospace" placeholder="test_... or live_...">
                            <small class="text-muted">From Developer Tools → Authentication → Client-side tokens (create new if needed)</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Webhook Secret (Optional)</label>
                            <input type="text" name="paddle_webhook_secret" value="{{ $settings['paddle']['paddle_webhook_secret'] ?? '' }}" 
                                   class="form-control font-monospace" placeholder="pdl_ntfset_...">
                            <small class="text-muted">From Notifications → Notification Settings → Webhook Secret (after setting webhook URL below)</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning border-0 mb-0">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle-fill me-3"></i>
                                    <div>
                                        <p class="mb-1 fw-semibold">Webhook URL</p>
                                        <small>Configure this URL in Paddle Dashboard:</small><br>
                                        <code class="bg-white px-2 py-1 rounded">{{ url(config('cashier.path', 'paddle') . '/webhook') }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Save Paddle Settings
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-question-circle text-primary me-2"></i>
                        Quick Guide
                    </h6>
                    <ol class="small mb-0 ps-3">
                        <li class="mb-2">Create Paddle account at <a href="https://vendors.paddle.com/" target="_blank">vendors.paddle.com</a></li>
                        <li class="mb-2">Click '...' icon (top-left sidebar) to find your <strong>Seller ID</strong></li>
                        <li class="mb-2">Go to <strong>Developer Tools → Authentication</strong></li>
                        <li class="mb-2">Create <strong>API Key</strong> with all permissions</li>
                        <li class="mb-2">Create <strong>Client-side Token</strong></li>
                        <li class="mb-2">Go to <strong>Catalog</strong> to create products and prices</li>
                        <li class="mb-2">Copy the <strong>Price ID</strong> from each price</li>
                        <li class="mb-0">Add Price ID to your products in Admin → Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Test Paddle Connection -->
<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 border-primary border-2">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-credit-card text-primary fs-2"></i>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold text-primary">Test Paddle Connection</p>
                        <small class="text-muted">Verify API credentials and connection</small>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.test-paddle') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plug me-2"></i> Test Connection
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Envato API Settings -->
<form method="POST" action="{{ route('admin.settings.update') }}" class="row g-4 mt-4">
    @csrf
    <input type="hidden" name="group" value="envato">
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-code-square text-success me-2"></i>
                    Envato API Settings
                </h5>
                <small class="text-muted">Configure Envato API for purchase code verification</small>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Envato API Token</label>
                        <input type="text" name="envato_api_token" value="{{ $settings['envato']['envato_api_token'] ?? '' }}" 
                               class="form-control font-monospace" placeholder="Your Envato Personal Token">
                        <small class="text-muted">Get your token from <a href="https://build.envato.com/create-token/" target="_blank">Envato API</a></small>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info border-0 mb-0">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill me-3"></i>
                                <div>
                                    <p class="mb-1 fw-semibold">How to get Envato API Token:</p>
                                    <ol class="small mb-0 ps-3">
                                        <li>Go to <a href="https://build.envato.com/create-token/" target="_blank">Envato Create Token</a></li>
                                        <li>Login with your Envato account</li>
                                        <li>Give your token a name (e.g., "License Verification")</li>
                                        <li>Check permissions: <strong>View and search Envato sites</strong> and <strong>View your account username</strong></li>
                                        <li>Click "Create Token" and copy it here</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-0">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i> Save Envato Settings
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-shield-check text-success me-2"></i>
                    Verification Page
                </h6>
                <p class="small mb-3">Customers who purchased from Envato can verify their purchase code here:</p>
                <a href="{{ route('envato.show') }}" target="_blank" class="btn btn-outline-success btn-sm w-100 mb-2">
                    <i class="bi bi-box-arrow-up-right me-2"></i>
                    Open Verification Page
                </a>
                <small class="text-muted d-block text-center">Share this link with your Envato customers</small>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'p-3 bg-primary bg-opacity-10 rounded border border-primary';
            
            const label = document.createElement('p');
            label.className = 'text-primary small mb-2 fw-semibold';
            label.textContent = 'Preview:';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-fluid';
            img.style.maxHeight = '80px';
            img.style.objectFit = 'contain';
            img.alt = 'Preview';
            
            div.appendChild(label);
            div.appendChild(img);
            preview.appendChild(div);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
