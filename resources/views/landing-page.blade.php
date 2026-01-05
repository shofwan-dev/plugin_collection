@extends('layouts.public')

@section('title', $landingPage->meta_title ?? $landingPage->title)
@section('description', $landingPage->meta_description ?? $landingPage->hero_subtitle)

@push('styles')
<style>
    .lp-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }
    .lp-hero-image {
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    .product-card {
        border: none;
        transition: all 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .content-area {
        font-size: 1.1rem;
        line-height: 1.8;
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="lp-hero text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-3 fw-bold mb-4 animate-fadeInUp">{{ $landingPage->hero_title }}</h1>
                <p class="lead mb-5 opacity-75 animate-fadeInUp" style="animation-delay: 0.2s;">
                    {{ $landingPage->hero_subtitle }}
                </p>
                <div class="animate-fadeInUp" style="animation-delay: 0.4s;">
                    <a href="#pricing" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-lg">
                        View Packages
                    </a>
                </div>
            </div>
            @if($landingPage->hero_image)
            <div class="col-lg-5 d-none d-lg-block">
                <img src="{{ asset('storage/' . $landingPage->hero_image) }}" alt="{{ $landingPage->title }}" class="img-fluid lp-hero-image animate-fadeInUp" style="animation-delay: 0.6s;">
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Extra Content -->
@if($landingPage->content)
<section class="py-5 bg-white">
    <div class="container py-5 content-area">
        {!! nl2br(e($landingPage->content)) !!}
    </div>
</section>
@endif

<!-- Benefits Section -->
@if($landingPage->benefits && count($landingPage->benefits) > 0)
<section class="py-5 bg-light position-relative overflow-hidden">
    <!-- Decorative Background -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.05;">
        <div class="position-absolute" style="top: 10%; left: 10%; width: 300px; height: 300px; background: radial-gradient(circle, var(--primary) 0%, transparent 70%);"></div>
        <div class="position-absolute" style="bottom: 10%; right: 10%; width: 400px; height: 400px; background: radial-gradient(circle, var(--secondary) 0%, transparent 70%);"></div>
    </div>

    <div class="container py-5 position-relative">
        <div class="text-center mb-5 animate-fadeInUp">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                <i class="bi bi-star-fill me-2"></i>Why Choose Us
            </span>
            <h2 class="display-4 fw-bold mb-3">Powerful Features & Benefits</h2>
            <p class="text-muted lead mb-0">Discover what makes us the best choice for your business</p>
        </div>
        
        <div class="row g-4">
            @foreach($landingPage->benefits as $index => $benefit)
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card h-100 animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="benefit-card-inner">
                        <div class="benefit-icon-wrapper mb-4">
                            @php
                                $iconName = $benefit['icon'] ?? 'check-circle-fill';
                                $iconName = str_replace(['bi-', 'bi '], '', $iconName);
                                $iconName = trim($iconName);
                            @endphp
                            <div class="benefit-icon-circle">
                                <i class="bi bi-{{ $iconName }}"></i>
                            </div>
                            <div class="benefit-icon-glow"></div>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $benefit['title'] ?? 'Benefit' }}</h4>
                        <p class="text-muted mb-0">{{ $benefit['description'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.benefit-card {
    position: relative;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.95) 100%);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(99, 102, 241, 0.1);
    overflow: hidden;
}

.benefit-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    border-radius: 20px;
    z-index: 0;
}

.benefit-card:hover::before {
    opacity: 0.05;
}

.benefit-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 60px rgba(99, 102, 241, 0.25);
    border-color: rgba(99, 102, 241, 0.3);
}

.benefit-card-inner {
    position: relative;
    z-index: 1;
    text-align: center;
}

.benefit-icon-wrapper {
    position: relative;
    display: inline-block;
}

.benefit-icon-circle {
    position: relative;
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    transition: all 0.4s ease;
    z-index: 2;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
}

.benefit-card:hover .benefit-icon-circle {
    transform: rotate(360deg) scale(1.1);
    box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5);
}

.benefit-icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
    z-index: 1;
}

@keyframes pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
}

.benefit-card h4 {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endif

<!-- Testimonials Section -->
@if($landingPage->testimonials && count($landingPage->testimonials) > 0)
<section class="py-5 bg-gradient-light">
    <div class="container py-5">
        <div class="text-center mb-5 animate-fadeInUp">
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-3">
                <i class="bi bi-chat-quote-fill me-2"></i>Testimonials
            </span>
            <h2 class="display-4 fw-bold mb-3">What Our Customers Say</h2>
            <p class="text-muted lead mb-0">Real feedback from real users who love our product</p>
        </div>
        
        @if(count($landingPage->testimonials) > 3)
        <!-- Carousel for more than 3 testimonials -->
        <div class="testimonial-slider-wrapper position-relative">
            <div class="swiper testimonialSwiper">
                <div class="swiper-wrapper pb-5">
                    @foreach($landingPage->testimonials as $index => $testimonial)
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <div class="quote-icon">
                                <i class="bi bi-quote"></i>
                            </div>
                            
                            <!-- Rating Stars -->
                            <div class="mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= ($testimonial['rating'] ?? 5) ? '-fill' : '' }} text-warning"></i>
                                @endfor
                            </div>
                            
                            <!-- Testimonial Content -->
                            <p class="testimonial-content mb-4">"{{ $testimonial['content'] ?? '' }}"</p>
                            
                            <!-- Customer Info -->
                            <div class="customer-info">
                                <div class="avatar-wrapper">
                                    <div class="avatar-circle">
                                        <strong>{{ strtoupper(substr($testimonial['name'] ?? 'U', 0, 1)) }}</strong>
                                    </div>
                                </div>
                                <div class="customer-details">
                                    <h6 class="mb-0 fw-bold">{{ $testimonial['name'] ?? 'Anonymous' }}</h6>
                                    <small class="text-muted">{{ $testimonial['position'] ?? 'Customer' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
        @else
        <!-- Grid for 3 or less testimonials -->
        <div class="row g-4 justify-content-center">
            @foreach($landingPage->testimonials as $index => $testimonial)
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="quote-icon">
                        <i class="bi bi-quote"></i>
                    </div>
                    
                    <!-- Rating Stars -->
                    <div class="mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= ($testimonial['rating'] ?? 5) ? '-fill' : '' }} text-warning"></i>
                        @endfor
                    </div>
                    
                    <!-- Testimonial Content -->
                    <p class="testimonial-content mb-4">"{{ $testimonial['content'] ?? '' }}"</p>
                    
                    <!-- Customer Info -->
                    <div class="customer-info">
                        <div class="avatar-wrapper">
                            <div class="avatar-circle">
                                <strong>{{ strtoupper(substr($testimonial['name'] ?? 'U', 0, 1)) }}</strong>
                            </div>
                        </div>
                        <div class="customer-details">
                            <h6 class="mb-0 fw-bold">{{ $testimonial['name'] ?? 'Anonymous' }}</h6>
                            <small class="text-muted">{{ $testimonial['position'] ?? 'Customer' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
.bg-gradient-light {
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
}

.testimonial-card {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
    border-color: rgba(99, 102, 241, 0.2);
}

.quote-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 3rem;
    color: rgba(99, 102, 241, 0.1);
    line-height: 1;
}

.testimonial-content {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #6c757d;
    font-style: italic;
    flex-grow: 1;
}

.customer-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.avatar-circle {
    width: 55px;
    height: 55px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
}

.customer-details h6 {
    color: #212529;
    margin-bottom: 0.25rem;
}

/* Swiper Customization */
.testimonial-slider-wrapper {
    padding: 0 50px;
}

.swiper {
    padding-bottom: 50px !important;
}

.swiper-slide {
    height: auto;
}

.swiper-button-next,
.swiper-button-prev {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    background: var(--primary);
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
}

.swiper-button-next:after,
.swiper-button-prev:after {
    font-size: 20px;
    font-weight: bold;
    color: var(--primary);
}

.swiper-button-next:hover:after,
.swiper-button-prev:hover:after {
    color: white;
}

.swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: var(--primary);
    opacity: 0.3;
    transition: all 0.3s ease;
}

.swiper-pagination-bullet-active {
    opacity: 1;
    width: 30px;
    border-radius: 6px;
}

@media (max-width: 768px) {
    .testimonial-slider-wrapper {
        padding: 0 20px;
    }
    
    .swiper-button-next,
    .swiper-button-prev {
        display: none;
    }
}
</style>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    @if(count($landingPage->testimonials) > 3)
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.testimonialSwiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
        });
    });
    @endif
</script>
@endif

<!-- Products/Pricing -->
<section id="pricing" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Choose Your Package</h2>
            <p class="text-muted">Explore our products and select the perfect plan for your needs</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($products as $product)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 product-card shadow-sm text-center p-4">
                    <div class="card-body">
                        <!-- Product Badge -->
                        <div class="mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                <i class="bi bi-plugin me-1"></i> {{ $product->type }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h3 class="h2 fw-bold mb-3">{{ $product->name }}</h3>
                        
                        <!-- Product Description -->
                        <p class="text-muted mb-4">{{ Str::limit($product->description, 100) }}</p>

                        <!-- Pricing Info -->
                        <div class="mb-4">
                            <div class="h3 fw-bold text-primary mb-0">${{ number_format($product->price, 0) }}</div>
                            <small class="text-muted">One-time payment</small>
                        </div>

                        <!-- Quick Features -->
                        <ul class="list-unstyled mb-4 text-start">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Version {{ $product->version }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> {{ $product->formatted_file_size }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Lifetime Updates</li>
                        </ul>

                        <!-- CTA Button -->
                        <a href="{{ route('product.show', $product->slug) }}" class="btn btn-primary w-100 py-3 fw-bold">
                            <i class="bi bi-eye me-1"></i> View Details & Pricing
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Footer CTA -->
<section class="py-5 bg-dark text-white">
    <div class="container text-center py-4">
        <h2 class="fw-bold mb-4">Still have questions?</h2>
        <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-5">Contact Support</a>
    </div>
</section>
@endsection
