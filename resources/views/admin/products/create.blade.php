@extends('layouts.admin')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>General Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="form-control @error('name') is-invalid @enderror" placeholder="e.g. CF7 to WhatsApp Gateway">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" required class="form-select @error('type') is-invalid @enderror">
                                <option value="plugin" {{ old('type') == 'plugin' ? 'selected' : '' }}>Plugin</option>
                                <option value="website" {{ old('type') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="addon" {{ old('type') == 'addon' ? 'selected' : '' }}>Addon</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Version <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <input type="text" name="version" value="{{ old('version', '1.0.0') }}" required
                                    class="form-control @error('version') is-invalid @enderror" placeholder="1.0.0">
                            </div>
                            @error('version')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror" 
                                placeholder="Short summary about the product...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-currency-dollar me-2"></i>Pricing & Licensing</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" value="{{ old('price', '0.00') }}" step="0.01" required
                                        class="form-control @error('price') is-invalid @enderror">
                                </div>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">Max Domains <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                    <input type="number" name="max_domains" value="{{ old('max_domains', '1') }}" required
                                        class="form-control @error('max_domains') is-invalid @enderror">
                                </div>
                                <small class="text-muted">Use -1 for unlimited domains</small>
                                @error('max_domains')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Paddle Integration</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Paddle Price ID</label>
                                <input type="text" name="paddle_price_id" value="{{ old('paddle_price_id') }}"
                                    placeholder="pri_..."
                                    class="form-control font-monospace @error('paddle_price_id') is-invalid @enderror">
                                @error('paddle_price_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold">Paddle Product ID</label>
                                <input type="text" name="paddle_product_id" value="{{ old('paddle_product_id') }}"
                                    placeholder="pro_..."
                                    class="form-control font-monospace @error('paddle_product_id') is-invalid @enderror">
                                @error('paddle_product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-zip me-2"></i>Product Files & Assets</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select File <span class="text-danger">*</span></label>
                            <input type="file" name="file" required accept=".zip,.rar,.tar,.gz"
                                class="form-control @error('file') is-invalid @enderror">
                            <small class="text-muted">Max file size: 50MB (.zip, .rar, .tar, .gz)</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product Image/Thumbnail</label>
                            <input type="file" name="image" accept="image/*"
                                class="form-control @error('image') is-invalid @enderror"
                                onchange="previewImage(this, 'productImagePreview')">
                            <small class="text-muted">Recommended: 800x600px (JPG, PNG, WEBP)</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="productImagePreview" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Release Notes & Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Changelog</label>
                            <textarea name="changelog" rows="5" placeholder="- New feature added&#10;- Bug fixes&#10;- Performance improvements"
                                class="form-control @error('changelog') is-invalid @enderror">{{ old('changelog') }}</textarea>
                            @error('changelog')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Requirements</label>
                            <textarea name="requirements" rows="5" placeholder="e.g. &#10;WordPress 5.0+&#10;PHP 7.4+&#10;Contact Form 7 5.0+"
                                class="form-control @error('requirements') is-invalid @enderror">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="form-check form-switch px-md-5">
                            <input class="form-check-input scale-150" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label ms-3 fw-medium" for="is_active">
                                Product Status: Active (Available for purchase and download)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Content -->
            <div class="row mt-4">
                <div class="col-12">
                    @include('admin.products._dynamic_content')
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5 mt-4">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light px-4 border">
                    <i class="bi bi-arrow-left me-2"></i>Back to List
                </a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-cloud-arrow-up me-2"></i>Create Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview) return;
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'border rounded p-3 bg-light';
                
                const label = document.createElement('p');
                label.className = 'text-primary small mb-2 fw-semibold';
                label.textContent = 'Preview:';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-fluid rounded';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                img.alt = 'Preview';
                
                div.appendChild(label);
                div.appendChild(img);
                preview.appendChild(div);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

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

<style>
    .scale-150 {
        transform: scale(1.5);
    }
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection

