@extends('layouts.public')

@section('title', 'Pricing - CF7 to WhatsApp')
@section('description', 'Choose the perfect plan for your needs. Simple, transparent pricing with no hidden fees.')

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .animate-scaleIn {
        animation: scaleIn 0.6s ease-out forwards;
    }

    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .pricing-card {
        transition: all 0.3s ease;
    }

    .pricing-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }

    .feature-check {
        animation: scaleIn 0.3s ease-out;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6 animate-fadeInUp">
            Simple, Transparent Pricing
        </h1>
        <p class="text-xl md:text-2xl text-indigo-100 max-w-3xl mx-auto animate-fadeInUp" style="animation-delay: 0.2s;">
            Choose the perfect plan for your needs. No hidden fees, cancel anytime.
        </p>
    </div>

    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- Pricing Cards -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $index => $plan)
            <div class="pricing-card relative bg-white rounded-2xl shadow-xl border-2 {{ $plan->is_popular ? 'border-indigo-500 ring-4 ring-indigo-100' : 'border-gray-200' }} overflow-hidden animate-scaleIn" style="animation-delay: {{ $index * 0.15 }}s;">
                
                @if($plan->is_popular)
                <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-center py-2 text-sm font-semibold">
                    ⭐ MOST POPULAR
                </div>
                <div class="pt-10"></div>
                @endif

                <div class="p-8">
                    <!-- Plan Name -->
                    <h3 class="text-3xl font-bold mb-2 {{ $plan->is_popular ? 'text-indigo-600' : 'text-gray-900' }}">
                        {{ $plan->name }}
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-gray-600 mb-6 min-h-[48px]">{{ $plan->description }}</p>
                    
                    <!-- Price -->
                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-6xl font-extrabold {{ $plan->is_popular ? 'gradient-text' : 'text-gray-900' }}">
                                ${{ number_format($plan->price, 0) }}
                            </span>
                            <span class="text-xl text-gray-600 ml-2">/{{ $plan->billing_period }}</span>
                        </div>
                        @if($plan->price > 0)
                        <p class="text-sm text-gray-500 mt-2">One-time payment, lifetime access</p>
                        @endif
                    </div>

                    <!-- Features -->
                    <ul class="space-y-4 mb-8">
                        @foreach($plan->features as $featureIndex => $feature)
                        <li class="flex items-start feature-check" style="animation-delay: {{ ($index * 0.15) + ($featureIndex * 0.05) }}s;">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700 flex-1">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <!-- CTA Button -->
                    <a href="{{ route('checkout.show', $plan) }}" 
                       class="block w-full text-center py-4 px-6 rounded-xl font-semibold text-lg transition-all duration-300 {{ $plan->is_popular ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}"
                       style="position: relative; z-index: 10; pointer-events: auto;">
                        Get Started Now
                    </a>
                </div>

                <!-- Badge for max domains -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Max Domains:</span>
                        <span class="font-bold {{ $plan->is_popular ? 'text-indigo-600' : 'text-gray-900' }}">
                            {{ $plan->max_domains === -1 ? 'Unlimited' : $plan->max_domains }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Money Back Guarantee -->
        <div class="mt-16 text-center animate-fadeInUp" style="animation-delay: 0.6s;">
            <div class="inline-flex items-center bg-green-50 border-2 border-green-200 rounded-full px-8 py-4">
                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <div class="text-left">
                    <div class="font-bold text-green-900">30-Day Money Back Guarantee</div>
                    <div class="text-sm text-green-700">Not satisfied? Get a full refund, no questions asked.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-12">
            <span class="gradient-text">Frequently Asked Questions</span>
        </h2>

        <div class="space-y-6">
            @php
            $faqs = [
                [
                    'question' => 'Can I change my plan later?',
                    'answer' => 'Yes! You can upgrade or downgrade your plan at any time. The price difference will be prorated.'
                ],
                [
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept all major credit cards (Visa, MasterCard, American Express) through Stripe.'
                ],
                [
                    'question' => 'Is there a free trial?',
                    'answer' => 'We offer a 30-day money-back guarantee, which is better than a free trial. Try it risk-free!'
                ],
                [
                    'question' => 'Do you offer refunds?',
                    'answer' => 'Yes, we offer a 30-day money-back guarantee. If you\'re not satisfied, contact us for a full refund.'
                ],
                [
                    'question' => 'Can I use one license on multiple domains?',
                    'answer' => 'It depends on your plan. Single Site allows 1 domain, 5 Sites allows 5 domains, and Unlimited allows unlimited domains.'
                ]
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $faq['question'] }}</h3>
                <p class="text-gray-600">{{ $faq['answer'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-6 animate-fadeInUp">
            Still Have Questions?
        </h2>
        <p class="text-xl mb-8 text-indigo-100 animate-fadeInUp" style="animation-delay: 0.2s;">
            Our support team is here to help you choose the right plan
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fadeInUp" style="animation-delay: 0.4s;">
            <a href="#" class="btn btn-white btn-lg hover:scale-105 transition-transform">
                Contact Support
            </a>
            <a href="{{ route('documentation') }}" class="btn btn-outline-white btn-lg hover:bg-white hover:text-indigo-600 transition-all">
                View Documentation
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = entry.target.classList.contains('animate-scaleIn') 
                    ? 'scale(1)' 
                    : 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fadeInUp, .animate-scaleIn').forEach(el => {
        el.style.opacity = '0';
        if (el.classList.contains('animate-scaleIn')) {
            el.style.transform = 'scale(0.9)';
        } else {
            el.style.transform = 'translateY(30px)';
        }
        el.style.transition = 'all 0.8s ease-out';
        observer.observe(el);
    });
</script>
@endpush
@endsection
