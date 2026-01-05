@extends('layouts.public')

@section('title', 'Contact Us - CF7 to WhatsApp')
@section('description', 'Get in touch with us for support, sales, or any questions about CF7 to WhatsApp.')

@section('content')
<!-- Contact Hero -->
<section class="gradient-bg text-white py-5 position-relative overflow-hidden">
    <div class="hero-blob" style="width: 300px; height: 300px; background: rgba(255,255,255,0.1); top: 10%; left: 5%;"></div>
    <div class="hero-blob" style="width: 250px; height: 250px; background: rgba(255,255,255,0.1); bottom: 10%; right: 5%; animation-delay: 2s;"></div>

    <div class="container py-5 position-relative" style="z-index: 1;">
        <div class="text-center">
            <h1 class="display-3 fw-bold mb-4 animate-fadeInUp">Contact Us</h1>
            <p class="lead fs-4 mb-0 opacity-90 animate-fadeInUp" style="animation-delay: 0.1s;">
                Have questions? We're here to help you get started.
            </p>
        </div>
    </div>
</section>

<!-- Contact Info & Form -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Contact Details -->
            <div class="col-lg-5">
                <div class="animate-fadeInUp" style="animation-delay: 0.2s;">
                    <h2 class="fw-bold mb-4">Get in Touch</h2>
                    <p class="text-muted mb-5">
                        Whether you're curious about features, pricing, or even press, we're ready to answer any and all questions.
                    </p>

                    <div class="d-flex flex-column gap-4">
                        @php
                            $contactEmail = \App\Models\Setting::get('contact_email');
                            $contactPhone = \App\Models\Setting::get('contact_phone');
                            $whatsappAdmin = \App\Models\Setting::get('whatsapp_admin_number');
                        @endphp

                        @if($whatsappAdmin)
                        <div class="card border-0 shadow-sm card-hover">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-4">
                                    <i class="bi bi-whatsapp text-success fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">WhatsApp</h5>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappAdmin) }}" target="_blank" class="text-decoration-none text-dark stretched-link">
                                        {{ $whatsappAdmin }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @elseif($contactPhone)
                        <div class="card border-0 shadow-sm card-hover">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-4">
                                    <i class="bi bi-telephone text-primary fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Phone</h5>
                                    <a href="tel:{{ $contactPhone }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $contactPhone }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($contactEmail)
                        <div class="card border-0 shadow-sm card-hover">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle me-4">
                                    <i class="bi bi-envelope text-info fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Email</h5>
                                    <a href="mailto:{{ $contactEmail }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $contactEmail }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="card border-0 shadow-sm card-hover">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-4">
                                    <i class="bi bi-clock text-warning fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Support Hours</h5>
                                    <p class="mb-0 text-muted">Mon - Fri, 9am - 5pm EST</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simple Contact Placeholder / Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg animate-fadeInUp" style="animation-delay: 0.3s;">
                    <div class="card-body p-5">
                        <h3 class="fw-bold mb-4">Send us a message</h3>
                        <form action="#" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" placeholder="john@example.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject</label>
                                    <input type="text" class="form-control" placeholder="How can we help?">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="5" placeholder="Tell us more about your inquiry..."></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-gradient btn-lg w-100 py-3 fw-bold" onclick="event.preventDefault(); alert('This is a demo contact form. Please reach out via WhatsApp or Email for real support.');">
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Preview -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="text-muted">Quick answers to common questions</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Does this work with any WordPress theme?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted pb-4">
                                Yes! Our plugin is designed to be compatible with all WordPress themes that follow standard coding practices. It works directly with Contact Form 7.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Do I need a WhatsApp API account?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted pb-4">
                                Our plugin integrates with various gateways. Some options require an API account while others use standard webhooks. Most of our users start with our recommended gateway providers.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I use it on multiple websites?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted pb-4">
                                This depends on the plan you purchase. Our "Single Site" plan is for one domain, while "Multi-Site" and "Agency" plans allow for more.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <p class="text-muted">Don't see your question? <a href="{{ route('documentation') }}" class="text-primary fw-bold text-decoration-none">Check our full documentation</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
