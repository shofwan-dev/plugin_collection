@extends('layouts.admin')

@section('page-title', 'Edit Product')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">
                    <i class="bi bi-box-seam text-primary me-2"></i> Edit Product
                </h2>
                <p class="text-muted mb-0">{{ $product->name }}</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to Products
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle text-primary me-2"></i> Basic Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Product Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Product Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $product->name) }}" 
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="plugin" {{ old('type', $product->type) == 'plugin' ? 'selected' : '' }}>
                                        <i class="bi bi-plugin"></i> Plugin
                                    </option>
                                    <option value="website" {{ old('type', $product->type) == 'website' ? 'selected' : '' }}>
                                        Website
                                    </option>
                                    <option value="addon" {{ old('type', $product->type) == 'addon' ? 'selected' : '' }}>
                                        Addon
                                    </option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Version -->
                            <div class="col-md-6">
                                <label for="version" class="form-label fw-semibold">
                                    Version <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('version') is-invalid @enderror" 
                                       id="version" 
                                       name="version" 
                                       value="{{ old('version', $product->version) }}" 
                                       placeholder="e.g., 1.0.0"
                                       required>
                                @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">
                                    Price (USD) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price', $product->price) }}" 
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Set to 0 for free products</small>
                            </div>

                            <!-- Max Domains -->
                            <div class="col-md-6">
                                <label for="max_domains" class="form-label fw-semibold">
                                    Max Domains <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('max_domains') is-invalid @enderror" 
                                       id="max_domains" 
                                       name="max_domains" 
                                       value="{{ old('max_domains', $product->max_domains) }}" 
                                       placeholder="1"
                                       min="-1"
                                       required>
                                @error('max_domains')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Use -1 for unlimited domains</small>
                            </div>

                            <!-- Paddle Price ID -->
                            <div class="col-md-6">
                                <label for="paddle_price_id" class="form-label fw-semibold">
                                    Paddle Price ID <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="text" 
                                       class="form-control font-monospace @error('paddle_price_id') is-invalid @enderror" 
                                       id="paddle_price_id" 
                                       name="paddle_price_id" 
                                       value="{{ old('paddle_price_id', $product->paddle_price_id) }}" 
                                       placeholder="pri_01...">
                                @error('paddle_price_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">From Paddle &rarr; Catalog &rarr; Prices (required for checkout)</small>
                            </div>

                            <!-- Paddle Product ID -->
                            <div class="col-md-6">
                                <label for="paddle_product_id" class="form-label fw-semibold">
                                    Paddle Product ID <span class="text-muted">(Optional)</span>
                                </label>
                                <input type="text" 
                                       class="form-control font-monospace @error('paddle_product_id') is-invalid @enderror" 
                                       id="paddle_product_id" 
                                       name="paddle_product_id" 
                                       value="{{ old('paddle_product_id', $product->paddle_product_id) }}" 
                                       placeholder="pro_01...">
                                @error('paddle_product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">From Paddle &rarr; Catalog &rarr; Products</small>
                            </div>


                            <!-- Active Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold d-block">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active (available for download)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description & Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-text text-success me-2"></i> Description & Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Changelog -->
                        <div class="mb-3">
                            <label for="changelog" class="form-label fw-semibold">Changelog</label>
                            <textarea class="form-control @error('changelog') is-invalid @enderror" 
                                      id="changelog" 
                                      name="changelog" 
                                      rows="4" 
                                      placeholder="What's new in this version...">{{ old('changelog', $product->changelog) }}</textarea>
                            <small class="text-muted">List changes, improvements, and bug fixes</small>
                            @error('changelog')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Requirements -->
                        <div class="mb-0">
                            <label for="requirements" class="form-label fw-semibold">Requirements</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                      id="requirements" 
                                      name="requirements" 
                                      rows="3" 
                                      placeholder="e.g., WordPress 5.0+, PHP 7.4+">{{ old('requirements', $product->requirements) }}</textarea>
                            <small class="text-muted">System requirements for this product</small>
                            @error('requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- File Upload -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-earmark-zip text-warning me-2"></i> Product File
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($product->file_name)
                        <!-- Current File Info -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-file-earmark-zip fs-3 me-3"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">Current File</div>
                                    <div class="small">{{ $product->file_name }}</div>
                                    <div class="small text-muted">{{ $product->formatted_file_size }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Upload New File -->
                        <div class="mb-0">
                            <label for="file" class="form-label fw-semibold">
                                Upload New File {{ $product->file_name ? '(Optional)' : '' }}
                            </label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".zip,.rar,.tar,.gz">
                            <small class="text-muted d-block mt-1">
                                Accepted: .zip, .rar, .tar, .gz (Max: 50MB)
                            </small>
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Product Image -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-image text-info me-2"></i> Product Image
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if($product->image)
                        <!-- Current Image -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Image</label>
                            <div class="border rounded p-3 bg-light">
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid rounded"
                                     style="max-height: 200px; object-fit: cover;">
                            </div>
                        </div>
                        @endif

                        <!-- Upload New Image -->
                        <div class="mb-0">
                            <label for="image" class="form-label fw-semibold">
                                Upload New Image {{ $product->image ? '(Optional)' : '' }}
                            </label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   onchange="previewImage(this, 'imagePreview')">
                            <small class="text-muted d-block mt-1">
                                Accepted: JPG, PNG, WEBP (Max: 2MB)
                            </small>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Product Stats -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up text-info me-2"></i> Product Stats
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                            <div>
                                <div class="text-muted small">Downloads</div>
                                <div class="fw-bold fs-4">{{ $product->download_count ?? 0 }}</div>
                            </div>
                            <i class="bi bi-download fs-2 text-primary"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <div class="text-muted small">Created</div>
                                <div class="fw-semibold">{{ $product->created_at->format('d M Y') }}</div>
                            </div>
                            <i class="bi bi-calendar fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Content Sections (Benefits, Testimonials, SEO) -->
        <div class="row mt-4">
            <div class="col-12">
                @include('admin.products._dynamic_content')
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i> Update Product
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
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
let benefitIndex = {{ $product->benefits ? count($product->benefits) : 0 }};

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
let testimonialIndex = {{ $product->testimonials ? count($product->testimonials) : 0 }};

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
