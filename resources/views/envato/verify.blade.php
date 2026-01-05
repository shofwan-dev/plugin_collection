@extends('layouts.app')

@section('title', 'Verify Envato Purchase')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <span class="gradient-text">Verify Your Envato Purchase</span>
                </h1>
                <p class="lead text-muted">
                    Already purchased from CodeCanyon? Verify your purchase code to get your license key
                </p>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-info-circle-fill text-primary fs-2"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2">How to find your purchase code?</h5>
                            <ol class="mb-0 ps-3">
                                <li class="mb-2">Login to your <a href="https://codecanyon.net/downloads" target="_blank" class="text-primary">Envato account</a></li>
                                <li class="mb-2">Go to <strong>Downloads</strong> page</li>
                                <li class="mb-2">Find the item and click <strong>Download → License certificate & purchase code</strong></li>
                                <li class="mb-0">Copy the <strong>Item Purchase Code</strong></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-check text-success me-2"></i>
                        Verify Purchase Code
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('envato.verify') }}">
                        @csrf

                        <!-- Purchase Code -->
                        <div class="mb-4">
                            <label for="purchase_code" class="form-label fw-semibold">
                                Purchase Code <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg @error('purchase_code') is-invalid @enderror" 
                                id="purchase_code" 
                                name="purchase_code" 
                                placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                value="{{ old('purchase_code') }}"
                                required
                                maxlength="36"
                                pattern="[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}"
                            >
                            @error('purchase_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (36 characters)
                            </small>
                        </div>

                        <!-- Plan Selection -->
                        <div class="mb-4">
                            <label for="plan_id" class="form-label fw-semibold">
                                Select Your Plan <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select form-select-lg @error('plan_id') is-invalid @enderror" 
                                id="plan_id" 
                                name="plan_id" 
                                required
                            >
                                <option value="">Choose the plan you purchased...</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get() as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} - ${{ number_format($plan->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Select the plan that matches your Envato purchase
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold">
                                <i class="bi bi-shield-check me-2"></i>
                                Verify & Generate License
                            </button>
                        </div>

                        <!-- Help Text -->
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="bi bi-lock-fill me-1"></i>
                                Your purchase code will be verified with Envato API
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-question-circle text-info me-2"></i>
                        Frequently Asked Questions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I use the same purchase code multiple times?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    No, each purchase code can only be used once to generate a license. If you need multiple licenses, you'll need to purchase additional copies.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What happens after verification?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    After successful verification, a license key will be automatically generated and added to your account. You can then use this license to activate the plugin on your website.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    I'm getting an "Invalid purchase code" error
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Make sure you're copying the complete purchase code from your Envato downloads page. The code should be 36 characters long in the format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx. If the problem persists, contact our support.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alternative Purchase -->
            <div class="text-center mt-5">
                <p class="text-muted mb-3">Don't have an Envato purchase yet?</p>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="bi bi-cart"></i> Purchase Directly from Our Website
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
