@extends('layouts.public')

@section('title', 'Checkout Cancelled')

@section('content')
<section class="py-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-bold mb-4">Checkout Cancelled</h1>
        <p class="text-xl text-gray-600 mb-8">Your payment was cancelled. No charges were made.</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('pricing') }}" class="btn btn-primary">
                Back to Pricing
            </a>
            <a href="{{ route('home') }}" class="btn btn-secondary">
                Go to Homepage
            </a>
        </div>
    </div>
</section>
@endsection
