@extends('layouts.admin')

@section('title', 'Create Landing Page')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <h2 class="fw-bold"><i class="bi bi-file-earmark-plus text-primary me-2"></i> Create Landing Page</h2>
        <p class="text-muted">Fill in the details to create a custom landing page.</p>
    </div>

    <form method="POST" action="{{ route('admin.landing-pages.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Page Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Slug (lp/slug-here)</label>
                                <div class="input-group">
                                    <span class="input-group-text">lp/</span>
                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                                </div>
                                @error('slug')<div class="invalid-feedback text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0">Hero Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2">{{ old('hero_subtitle') }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Hero Image</label>
                            <input type="file" name="hero_image" class="form-control">
                            <small class="text-muted">Recommended: 1920x1080px. Max 2MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">Extra Content (Markdown/HTML supported)</h5>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="content" class="form-control" rows="10">{{ old('content') }}</textarea>
                        <small class="text-muted">This content will appear between the Hero and the Products section.</small>
                    </div>
                </div>

                <!-- Benefits & Testimonials -->
                @include('admin.landing-pages._dynamic_content', ['landingPage' => null])
            </div>

            <div class="col-lg-4">
                <!-- Status & Products -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="mb-0">Page Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label fw-bold" for="is_active">Is Active (Visible to Public)</label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_homepage" id="is_homepage" value="1">
                            <label class="form-check-label fw-bold" for="is_homepage">Set as Homepage</label>
                            <small class="text-muted d-block">This will replace the main homepage / route.</small>
                        </div>

                        <hr>

                        <label class="form-label fw-bold mb-3">Select Products</label>
                        <div class="products-list" style="max-height: 300px; overflow-y: auto;">
                            @foreach($products as $product)
                            <div class="form-check mb-2 p-2 bg-light rounded border">
                                <input class="form-check-input ms-0" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="prod_{{ $product->id }}">
                                <label class="form-check-label ms-2 fw-semibold" for="prod_{{ $product->id }}">
                                    {{ $product->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-3">
                        <h5 class="mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold text-dark">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="4">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid shadow">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save2 me-2"></i> Create Page
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Benefits Management
    let benefitIndex = 0;
    
    function addBenefit() {
        const container = document.getElementById('benefits-container');
        const benefitHtml = `
            <div class="benefit-item border rounded p-3 mb-3" data-index="${benefitIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Benefit #${benefitIndex + 1}</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeBenefit(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small">Title</label>
                        <input type="text" class="form-control" name="benefits[${benefitIndex}][title]" placeholder="e.g., Instant Setup">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Icon (Bootstrap Icon)</label>
                        <input type="text" class="form-control" name="benefits[${benefitIndex}][icon]" value="check-circle-fill" placeholder="e.g., rocket-takeoff-fill">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Description</label>
                        <textarea class="form-control" name="benefits[${benefitIndex}][description]" rows="2" placeholder="Describe this benefit..."></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', benefitHtml);
        benefitIndex++;
    }

    function removeBenefit(button) {
        if (confirm('Remove this benefit?')) {
            button.closest('.benefit-item').remove();
        }
    }

    // Testimonials Management
    let testimonialIndex = 0;
    
    function addTestimonial() {
        const container = document.getElementById('testimonials-container');
        const testimonialHtml = `
            <div class="testimonial-item border rounded p-3 mb-3" data-index="${testimonialIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Testimonial #${testimonialIndex + 1}</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeTestimonial(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label small">Customer Name</label>
                        <input type="text" class="form-control" name="testimonials[${testimonialIndex}][name]" placeholder="e.g., John Doe">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Position/Company</label>
                        <input type="text" class="form-control" name="testimonials[${testimonialIndex}][position]" placeholder="e.g., CEO at Company">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small">Rating (1-5)</label>
                        <select class="form-select" name="testimonials[${testimonialIndex}][rating]">
                            <option value="5" selected>5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Testimonial Content</label>
                        <textarea class="form-control" name="testimonials[${testimonialIndex}][content]" rows="3" placeholder="What did the customer say..."></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', testimonialHtml);
        testimonialIndex++;
    }

    function removeTestimonial(button) {
        if (confirm('Remove this testimonial?')) {
            button.closest('.testimonial-item').remove();
        }
    }
</script>
@endpush
@endsection
