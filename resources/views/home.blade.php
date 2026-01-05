@extends('layouts.public')

@section('title', $homepage->meta_title ?? 'CF7 to WhatsApp - Premium WordPress Plugins')
@section('description', $homepage->meta_description ?? 'Discover our collection of premium WordPress plugins for WhatsApp integration. Transform your website with cutting-edge solutions.')

@push('styles')
<style>
    .product-card {
        position: relative;
        overflow: hidden;
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent,
            rgba(255, 255, 255, 0.1),
            transparent
        );
        transform: rotate(45deg);
        transition: all 0.6s;
    }

    .product-card:hover::before {
        left: 100%;
    }

    .product-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 25px 50px rgba(99, 102, 241, 0.25);
    }

    .type-badge {
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .feature-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        font-size: 2rem;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section gradient-bg text-white position-relative">
    <!-- Animated Blobs -->
    <div class="hero-blob" style="width: 400px; height: 400px; background: #8b5cf6; top: 10%; left: 10%;"></div>
    <div class="hero-blob" style="width: 350px; height: 350px; background: #ec4899; top: 50%; right: 10%; animation-delay: 2s;"></div>
    <div class="hero-blob" style="width: 300px; height: 300px; background: #6366f1; bottom: 10%; left: 50%; animation-delay: 4s;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <h1 class="display-1 fw-bold mb-4 animate-fadeInUp">
                    {!! nl2br(e($homepage->hero_title)) !!}
                </h1>
                <p class="lead fs-3 mb-5 animate-fadeInUp" style="animation-delay: 0.2s; opacity: 0.9;">
                    {{ $homepage->hero_subtitle }}
                </p>
                <div class="d-flex gap-3 justify-content-center mb-5 animate-fadeInUp" style="animation-delay: 0.4s;">
                    @if($homepage->hero_cta_text)
                    <a href="{{ $homepage->hero_cta_link }}" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                        <i class="bi bi-rocket-takeoff"></i> {{ $homepage->hero_cta_text }}
                    </a>
                    @endif
                    @if($homepage->hero_secondary_cta_text)
                    <a href="{{ $homepage->hero_secondary_cta_link }}" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold">
                        <i class="bi bi-book"></i> {{ $homepage->hero_secondary_cta_text }}
                    </a>
                    @endif
                </div>

                <!-- Stats -->
                <div class="row g-4 mt-5 animate-fadeInUp" style="animation-delay: 0.6s;">
                    <div class="col-6 col-md-3">
                        <div class="stat-card p-4">
                            <h2 class="display-4 fw-bold mb-0">{{ \App\Models\Product::active()->count() }}+</h2>
                            <p class="mb-0 opacity-75">{{ $homepage->stats_products_label }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card p-4">
                            <h2 class="display-4 fw-bold mb-0">50K+</h2>
                            <p class="mb-0 opacity-75">{{ $homepage->stats_downloads_label }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card p-4">
                            <h2 class="display-4 fw-bold mb-0">99.9%</h2>
                            <p class="mb-0 opacity-75">{{ $homepage->stats_uptime_label }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card p-4">
                            <h2 class="display-4 fw-bold mb-0">24/7</h2>
                            <p class="mb-0 opacity-75">{{ $homepage->stats_support_label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-3 fw-bold mb-3">
                <span class="gradient-text">{{ $homepage->products_title }}</span>
            </h2>
            <p class="lead text-muted fs-4">
                {{ $homepage->products_subtitle }}
            </p>
        </div>

        @if($landingPages->count() > 0)
        <div class="row g-4">
            @foreach($landingPages as $index => $page)
            <div class="col-lg-4 col-md-6">
                <div class="card product-card h-100 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Landing Page Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-gradient type-badge px-3 py-2">
                                <i class="bi bi-file-earmark-richtext"></i> Landing Page
                            </span>
                            @if($page->is_homepage)
                            <span class="badge bg-primary fs-6 px-3 py-2"><i class="bi bi-house-fill"></i></span>
                            @endif
                        </div>

                        <h3 class="h4 fw-bold mb-3">{{ $page->title }}</h3>
                        
                        <p class="text-muted mb-4">
                            {{ Str::limit($page->hero_subtitle ?? $page->content, 120) }}
                        </p>

                        <!-- Footer -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <small class="text-muted">
                                <i class="bi bi-box-seam"></i> {{ $page->products->count() }} {{ $page->products->count() > 1 ? 'Products' : 'Product' }}
                            </small>
                            <small class="text-success">
                                <i class="bi bi-check-circle-fill"></i> Active
                            </small>
                        </div>

                        <a href="{{ route('landing-page.show', $page->slug) }}" class="btn btn-gradient w-100 py-3 fw-bold">
                            <i class="bi bi-arrow-right-circle"></i> Explore Offers
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h3 class="mt-4">No Landing Pages Available</h3>
            <p class="text-muted">Check back soon for amazing offers!</p>
        </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-3 fw-bold mb-3">
                <span class="gradient-text">{{ $homepage->features_title }}</span>
            </h2>
            <p class="lead text-muted">{{ $homepage->features_subtitle }}</p>
        </div>

        <div class="row g-4">
            @foreach($homepage->features_items ?? [] as $feature)
            <div class="col-lg-3 col-md-6">
                <div class="text-center feature-card p-4">
                    <div class="feature-icon mx-auto" style="background: linear-gradient(135deg, {{ $feature['color'] ?? '#6366f1' }}22, {{ $feature['color'] ?? '#6366f1' }}44);">
                        <i class="bi {{ $feature['icon'] ?? 'bi-check' }}" style="color: {{ $feature['color'] ?? '#6366f1' }};"></i>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $feature['title'] ?? '' }}</h5>
                    <p class="text-muted mb-0">{{ $feature['description'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 gradient-bg text-white position-relative overflow-hidden">
    <!-- Animated Blobs -->
    <div class="hero-blob" style="width: 400px; height: 400px; background: rgba(255,255,255,0.1); top: -10%; left: -5%;"></div>
    <div class="hero-blob" style="width: 350px; height: 350px; background: rgba(255,255,255,0.1); bottom: -10%; right: -5%; animation-delay: 2s;"></div>

    <div class="container py-5 position-relative" style="z-index: 1;">
        <div class="text-center">
            <h2 class="display-3 fw-bold mb-4">
                {{ $homepage->cta_title }}
            </h2>
            <p class="lead fs-4 mb-5 opacity-90">
                {{ $homepage->cta_subtitle }}
            </p>
            <div class="d-flex gap-3 justify-content-center">
                @if($homepage->cta_primary_text)
                <a href="{{ $homepage->cta_primary_link }}" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                    <i class="bi bi-rocket-takeoff"></i> {{ $homepage->cta_primary_text }}
                </a>
                @endif
                @if($homepage->cta_secondary_text)
                <a href="{{ $homepage->cta_secondary_link }}" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold">
                    <i class="bi bi-person-plus"></i> {{ $homepage->cta_secondary_text }}
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.animate-fadeInUp').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.8s ease-out';
        observer.observe(el);
    });
</script>
@endpush
@endsection
