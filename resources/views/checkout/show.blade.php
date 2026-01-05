@extends('layouts.public')

@section('title', 'Secure Checkout - ' . $plan->name)

@push('styles')
<style>
    .checkout-progress {
        position: relative;
        padding: 20px 0;
    }
    
    .checkout-progress::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }
    
    .progress-step {
        position: relative;
        z-index: 1;
        background: white;
    }
    
    .progress-step.active .step-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .progress-step.completed .step-circle {
        background: #28a745;
        color: white;
    }
    
    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    
    .order-summary-card {
        position: sticky;
        top: 100px;
    }
    
    .price-breakdown {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
    }
    
    .trust-seal {
        transition: transform 0.3s ease;
    }
    
    .trust-seal:hover {
        transform: translateY(-5px);
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>
@endpush

@push('scripts')
<!-- Paddle.js from Cashier -->
@paddleJS
@endpush

@section('content')
<!-- Progress Bar -->
<section class="bg-light py-4">
    <div class="container">
        <div class="checkout-progress">
            <div class="row text-center">
                <div class="col-4 progress-step completed">
                    <div class="step-circle">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="mt-2 small fw-semibold">Select Plan</div>
                </div>
                <div class="col-4 progress-step active">
                    <div class="step-circle">
                        2
                    </div>
                    <div class="mt-2 small fw-semibold">Your Details</div>
                </div>
                <div class="col-4 progress-step">
                    <div class="step-circle">
                        3
                    </div>
                    <div class="mt-2 small fw-semibold">Payment</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Checkout -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Column - Form -->
            <div class="col-lg-7">
                <!-- Header -->
                <div class="mb-4">
                    <h1 class="fw-bold mb-2">Complete Your Order</h1>
                    <p class="text-muted">You're just one step away from transforming your business!</p>
                </div>

                <!-- Checkout Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form id="checkoutForm">
                            @csrf
                            
                            <!-- Personal Information -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    Personal Information
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" id="first_name" name="first_name" required value="{{ old('first_name', auth()->user()->name ?? '') }}" placeholder="John" readonly>
                                        @error('first_name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                        <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" placeholder="Doe" readonly>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="john@example.com" readonly>
                                    <small class="text-muted">Your license key will be sent to this email</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Terms & Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required checked>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <x-paddle-button :checkout="$checkout" class="btn btn-lg w-100 py-3 fw-bold shadow text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="bi bi-lock-fill me-2"></i>
                                Proceed to Secure Payment
                            </x-paddle-button>

                            <p class="text-center text-muted small mt-3 mb-0">
                                <i class="bi bi-shield-check me-1"></i>
                                Protected by Paddle Secure Checkout
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Trust Seals -->
                <div class="row g-3 mt-4 text-center">
                    <div class="col-4">
                        <div class="trust-seal p-3">
                            <i class="bi bi-shield-check text-success fs-2"></i>
                            <div class="small fw-semibold mt-2">Secure Checkout</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="trust-seal p-3">
                            <i class="bi bi-arrow-repeat text-primary fs-2"></i>
                            <div class="small fw-semibold mt-2">30-Day Guarantee</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="trust-seal p-3">
                            <i class="bi bi-headset text-info fs-2"></i>
                            <div class="small fw-semibold mt-2">24/7 Support</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="col-lg-5">
                <div class="order-summary-card">
                    <!-- Price Breakdown -->
                    <div class="price-breakdown shadow-lg mb-4">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ $plan->name }}</span>
                                <span class="fw-semibold">${{ number_format($plan->price, 2) }}</span>
                            </div>
                            <small class="opacity-75">{{ $plan->description }}</small>
                        </div>

                        <hr class="border-white opacity-25 my-4">

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>${{ number_format($plan->price, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span>Calculated by Paddle</span>
                        </div>

                        <hr class="border-white opacity-25 my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold fs-5">Total</div>
                                <small class="opacity-75">Billed {{ $plan->billing_period ?? 'one-time' }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-3">${{ number_format($plan->price, 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- What's Included -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">What's Included</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach($plan->features ?? [] as $feature)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    {{ $feature }}
                                </li>
                                @endforeach
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Lifetime updates
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Priority support
                                </li>
                                <li class="mb-0">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    30-day money-back guarantee
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
