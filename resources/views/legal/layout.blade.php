@extends('layouts.public')

@section('title', $title)

@section('content')
<div class="min-vh-100 bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-5">
                    <a href="{{ route('home') }}" class="btn btn-link text-decoration-none mb-3">
                        <i class="bi bi-arrow-left me-2"></i> Back to Home
                    </a>
                    <h1 class="display-4 fw-bold mb-3">{{ $title }}</h1>
                    <p class="text-muted">
                        Last Updated: {{ now()->format('F d, Y') }}
                    </p>
                </div>

                <!-- Content Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="legal-content">
                            @yield('legal-content')
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="text-center mt-4">
                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                        <a href="{{ route('legal.terms') }}" class="text-decoration-none {{ request()->routeIs('legal.terms') ? 'fw-bold' : '' }}">
                            Terms of Service
                        </a>
                        <a href="{{ route('legal.privacy') }}" class="text-decoration-none {{ request()->routeIs('legal.privacy') ? 'fw-bold' : '' }}">
                            Privacy Policy
                        </a>
                        <a href="{{ route('legal.refund') }}" class="text-decoration-none {{ request()->routeIs('legal.refund') ? 'fw-bold' : '' }}">
                            Refund Policy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .legal-content h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        color: #1e293b;
    }

    .legal-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        color: #334155;
    }

    .legal-content p {
        margin-bottom: 1rem;
        line-height: 1.8;
        color: #475569;
    }

    .legal-content ul, .legal-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }

    .legal-content li {
        margin-bottom: 0.5rem;
        line-height: 1.7;
        color: #475569;
    }

    .legal-content strong {
        color: #1e293b;
        font-weight: 600;
    }

    .legal-content .highlight {
        background: #f1f5f9;
        padding: 1.5rem;
        border-left: 4px solid #6366f1;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
    }
</style>
@endsection
