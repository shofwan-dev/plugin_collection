@extends('layouts.public')

@section('title', $product->name . ' - CF7 to WhatsApp')
@section('description', $product->description)

@push('styles')
<style>
    .price-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .price-box::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .benefit-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    
    .benefit-item:hover {
        border-left-color: var(--primary);
        background: #f8f9fa;
        transform: translateX(5px);
    }
    
    .urgency-badge {
        animation: blink 2s ease-in-out infinite;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .trust-badge {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.9);
    }
    
    /* Product Image Styles */
    .product-image-wrapper {
        position: relative;
        display: inline-block;
        max-width: 30%;
    }
    
    .product-image {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .product-image::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #4facfe);
        background-size: 300% 300%;
        border-radius: 20px;
        z-index: -1;
        animation: gradientShift 4s ease infinite;
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .product-image:hover::before {
        opacity: 1;
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .product-image img {
        display: block;
        width: 100%;
        height: auto;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 18px;
    }
    
    .product-image:hover img {
        transform: scale(1.05);
    }
    
    .product-image:hover {
        box-shadow: 0 30px 80px rgba(102, 126, 234, 0.4);
        transform: translateY(-10px);
    }
    
    /* Floating animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .product-image-wrapper {
        animation: float 6s ease-in-out infinite;
    }
    
    .product-image-wrapper:hover {
        animation-play-state: paused;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .product-image-wrapper {
            max-width: 60%;
        }
    }
</style>
@endpush

@section('content')
<!-- Product Hero with Price -->
<section class="gradient-bg text-white py-5 position-relative overflow-hidden">
    <div class="hero-blob" style="width: 400px; height: 400px; background: rgba(255,255,255,0.1); top: -10%; right: -10%;"></div>
    <div class="hero-blob" style="width: 300px; height: 300px; background: rgba(255,255,255,0.1); bottom: -10%; left: -10%; animation-delay: 2s;"></div>

    <div class="container py-5 position-relative" style="z-index: 1;">
        <div class="row align-items-center g-4">
            <!-- Left Column - Content + Image -->
            <div class="col-lg-7">
                <!-- Urgency Badge -->
                <div class="mb-3">
                    <span class="badge bg-danger urgency-badge px-4 py-2 fs-6">
                        <i class="bi bi-lightning-fill"></i> LIMITED TIME OFFER
                    </span>
                </div>

                <!-- Product Title -->
                <h1 class="display-2 fw-bold mb-4">{{ $product->name }}</h1>

                <!-- Product Description -->
                <p class="lead fs-4 mb-4 opacity-90">{{ $product->description }}</p>

                <!-- Trust Indicators -->
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <div class="trust-badge px-4 py-2 rounded-pill">
                        <i class="bi bi-shield-check text-success"></i>
                        <span class="text-dark fw-semibold">Secure Payment</span>
                    </div>
                    <div class="trust-badge px-4 py-2 rounded-pill">
                        <i class="bi bi-arrow-clockwise text-primary"></i>
                        <span class="text-dark fw-semibold">Lifetime Updates</span>
                    </div>
                    <div class="trust-badge px-4 py-2 rounded-pill">
                        <i class="bi bi-headset text-info"></i>
                        <span class="text-dark fw-semibold">24/7 Support</span>
                    </div>
                </div>
            </div>

            <!-- Right Column - Price Box -->
            <div class="col-lg-5">
                <div class="price-box p-5 text-center shadow-lg">
                    <!-- Original Price (Strikethrough) -->
                    <div class="mb-2">
                        <span class="text-white-50 text-decoration-line-through fs-4">
                            ${{ number_format($product->price * 1.5, 0) }}
                        </span>
                    </div>

                    <!-- Current Price -->
                    <div class="mb-3">
                        <span class="display-1 fw-bold text-white">${{ number_format($product->price, 0) }}</span>
                        <span class="text-white-50 fs-5">/one-time</span>
                    </div>

                    <!-- Savings Badge -->
                    <div class="mb-4">
                        <span class="badge bg-warning text-dark px-4 py-2 fs-5">
                            <i class="bi bi-tag-fill"></i> SAVE 33% TODAY!
                        </span>
                    </div>

                    <!-- CTA Button -->
                    @auth
                    <a href="{{ route('checkout.show', $product) }}" 
                       class="btn btn-light btn-lg w-100 py-4 fw-bold mb-3 shadow"
                       onclick="console.log('Checkout clicked!', this.href); return true;">
                        <i class="bi bi-cart-check-fill me-2"></i> GET INSTANT ACCESS NOW
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg w-100 py-4 fw-bold mb-3 shadow">
                        <i class="bi bi-box-arrow-in-right me-2"></i> LOGIN TO PURCHASE
                    </a>
                    @endauth

                    <!-- Guarantee -->
                    <div class="text-white-50 small">
                        <i class="bi bi-shield-fill-check"></i> 30-Day Money-Back Guarantee
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Showcase -->
@if($product->image)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <div class="product-image-wrapper">
                    <div class="product-image">
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Benefits Section (Psychology Marketing) -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Why Choose {{ $product->name }}?</h2>
            <p class="lead text-muted">Join thousands of satisfied customers worldwide</p>
        </div>

        <div class="row g-4">
            @if($product->benefits && count($product->benefits) > 0)
                @foreach($product->benefits as $index => $benefit)
                <div class="col-lg-6">
                    <div class="benefit-item p-4 rounded mb-3">
                        <div class="d-flex align-items-start">
                            <div class="bg-{{ ['success', 'primary', 'warning', 'info', 'danger', 'secondary'][$index % 6] }} bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-{{ $benefit['icon'] ?? 'check-circle-fill' }} text-{{ ['success', 'primary', 'warning', 'info', 'danger', 'secondary'][$index % 6] }} fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">{{ $benefit['title'] }}</h5>
                                <p class="text-muted mb-0">{{ $benefit['description'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Default benefits if none set -->
                <div class="col-lg-6">
                    <div class="benefit-item p-4 rounded mb-3">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-rocket-takeoff-fill text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Instant Setup in 5 Minutes</h5>
                                <p class="text-muted mb-0">No technical skills required. Our step-by-step wizard gets you up and running immediately.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Social Proof / Testimonials -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">What Our Customers Say</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="mb-3">"This plugin transformed how we handle customer inquiries. Response time improved dramatically!"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <strong>JD</strong>
                            </div>
                            <div>
                                <div class="fw-bold">John Doe</div>
                                <small class="text-muted">Marketing Agency Owner</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="mb-3">"Best investment for my WordPress site. Setup was incredibly easy and support is top-notch!"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <strong>SM</strong>
                            </div>
                            <div>
                                <div class="fw-bold">Sarah Miller</div>
                                <small class="text-muted">E-commerce Store Owner</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <p class="mb-3">"Game changer! Our lead conversion rate increased by 250% in the first month alone."</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white rounded-circle p-2 me-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <strong>RJ</strong>
                            </div>
                            <div>
                                <div class="fw-bold">Robert Johnson</div>
                                <small class="text-muted">Real Estate Agent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-5 gradient-bg text-white">
    <div class="container py-5 text-center">
        <h2 class="display-4 fw-bold mb-4">Ready to Transform Your Business?</h2>
        <p class="lead mb-4">Join 10,000+ satisfied customers today. Risk-free with our 30-day money-back guarantee.</p>
        
        @auth
        <a href="{{ route('checkout.show', $product) }}" 
           class="btn btn-light btn-lg px-5 py-4 fw-bold shadow-lg"
           onclick="console.log('Final CTA clicked!', this.href); return true;">
            <i class="bi bi-cart-check-fill me-2"></i> GET STARTED NOW - ${{ number_format($product->price, 0) }}
        </a>
        @else
        <a href="{{ route('login') }}" 
           class="btn btn-light btn-lg px-5 py-4 fw-bold shadow-lg"
           onclick="console.log('Final Login CTA clicked!'); return true;">
            <i class="bi bi-box-arrow-in-right me-2"></i> LOGIN TO PURCHASE
        </a>
        @endauth
        
        
        <div class="mt-4 text-white-50">
            <i class="bi bi-shield-check me-2"></i> Secure checkout • <i class="bi bi-arrow-repeat mx-2"></i> 30-day guarantee • <i class="bi bi-headset mx-2"></i> 24/7 support
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Product page loaded for: {{ $product->slug }}');
        
        // Log all clicks on buttons and links for debugging
        document.addEventListener('click', function(e) {
            const el = e.target.closest('a, button');
            if (el) {
                console.log('Clicked element:', el);
                console.log('Href/Target:', el.href || el.dataset.target || 'no target');
            }
        });
    });
</script>
@endpush
