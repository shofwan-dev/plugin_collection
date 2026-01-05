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
