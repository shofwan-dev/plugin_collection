@extends('layouts.admin')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">
                    <i class="bi bi-plus-square text-primary me-2"></i> Add New Product
                </h2>
                <p class="text-muted mb-0">Create a new product, plugin, or website asset.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Back to Products
            </a>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Main Content (Left) -->
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
                            <div class="col-md-7">
                                <label for="name" class="form-label fw-semibold">
                                    Product Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g. CF7 to WhatsApp Gateway"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div class="col-md-5">
                                <label for="type" class="form-label fw-semibold">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="plugin" {{ old('type') == 'plugin' ? 'selected' : '' }}>Plugin</option>
                                    <option value="website" {{ old('type') == 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="addon" {{ old('type') == 'addon' ? 'selected' : '' }}>Addon</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Version -->
                            <div class="col-md-4">
                                <label for="version" class="form-label fw-semibold">
                                    Version <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                    <input type="text" 
                                           class="form-control @error('version') is-invalid @enderror" 
                                           id="version" 
                                           name="version" 
                                           value="{{ old('version', '1.0.0') }}" 
                                           placeholder="1.0.0"
                                           required>
                                </div>
                                @error('version')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div class="col-md-4">
                                <label for="price" class="form-label fw-semibold">
                                    Price (USD) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price', '0.00') }}" 
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                                @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Max Domains -->
                            <div class="col-md-4">
                                <label for="max_domains" class="form-label fw-semibold">
                                    Max Domains <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                    <input type="number" 
                                           class="form-control @error('max_domains') is-invalid @enderror" 
                                           id="max_domains" 
                                           name="max_domains" 
                                           value="{{ old('max_domains', '1') }}" 
                                           min="-1"
                                           required>
                                </div>
                                @error('max_domains')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Use -1 for unlimited</small>
                            </div>

                            <!-- Paddle Price ID -->
                            <div class="col-md-6">
                                <label for="paddle_price_id" class="form-label fw-semibold">
                                    Paddle Price ID
                                </label>
                                <input type="text" 
                                       class="form-control font-monospace @error('paddle_price_id') is-invalid @enderror" 
                                       id="paddle_price_id" 
                                       name="paddle_price_id" 
                                       value="{{ old('paddle_price_id') }}" 
                                       placeholder="pri_...">
                                @error('paddle_price_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Paddle Product ID -->
                            <div class="col-md-6">
                                <label for="paddle_product_id" class="form-label fw-semibold">
                                    Paddle Product ID
                                </label>
                                <input type="text" 
                                       class="form-control font-monospace @error('paddle_product_id') is-invalid @enderror" 
                                       id="paddle_product_id" 
                                       name="paddle_product_id" 
                                       value="{{ old('paddle_product_id') }}" 
                                       placeholder="pro_...">
                                @error('paddle_product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Short Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Summary of the product...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="changelog" class="form-label fw-semibold">Changelog</label>
                                <textarea class="form-control @error('changelog') is-invalid @enderror" 
                                          id="changelog" 
                                          name="changelog" 
                                          rows="5" 
                                          placeholder="- Initial release&#10;- Bug fixes...">{{ old('changelog') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="requirements" class="form-label fw-semibold">Requirements</label>
                                <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                          id="requirements" 
                                          name="requirements" 
                                          rows="5" 
                                          placeholder="e.g. WordPress 5.0+, PHP 7.4+">{{ old('requirements') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right) -->
            <div class="col-lg-4">
                <!-- File Upload -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-earmark-zip text-warning me-2"></i> Product File
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-0">
                            <label for="file" class="form-label fw-semibold">
                                Select File <span class="text-danger">*</span>
                            </label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".zip,.rar,.tar,.gz"
                                   required>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> Max size: 50MB (.zip, .rar, .tar, .gz)
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
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Upload Image</label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   onchange="previewImage(this, 'productImagePreview')">
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="productImagePreview"></div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-toggle-on text-primary me-2"></i> Publishing
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch card-p-3 bg-light rounded border p-3">
                            <input class="form-check-input ms-0 me-2 mt-1" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">
                                Active Product
                            </label>
                            <p class="small text-muted mb-0 mt-1">Visible and available for purchase.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- SEO Meta Tags -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-search text-success me-2"></i>SEO Meta Tags</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" placeholder="SEO optimized title" maxlength="60">
                            <small class="text-muted">Recommended: 50-60 characters</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" placeholder="SEO optimized description" maxlength="160">{{ old('meta_description') }}</textarea>
                            <small class="text-muted">Recommended: 150-160 characters</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card border-0 shadow-sm mt-4 mb-5">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" onclick="window.history.back()" class="btn btn-light border px-4">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-cloud-arrow-up me-2"></i> Create Product
                    </button>
                </div>
            </div>
        </div>
    </form>
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
                div.className = 'border rounded p-2 bg-light mt-2';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-fluid rounded';
                img.style.maxHeight = '250px';
                img.style.width = '100%';
                img.style.objectFit = 'cover';
                
                div.appendChild(img);
                preview.appendChild(div);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

</script>
@endpush

<style>
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    .card-header h5 {
        font-size: 1.1rem;
    }
    .font-monospace {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        font-size: 0.875rem;
    }
</style>
@endsection
