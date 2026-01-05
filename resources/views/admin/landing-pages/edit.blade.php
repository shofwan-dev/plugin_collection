@extends('layouts.admin')

@section('title', 'Edit Landing Page')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            <h2 class="fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Page: {{ $landingPage->title }}</h2>
        </div>
        <a href="{{ route('landing-page.show', $landingPage->slug) }}" target="_blank" class="btn btn-outline-dark">
            <i class="bi bi-eye me-1"></i> View Live
        </a>
    </div>

    <form method="POST" action="{{ route('admin.landing-pages.update', $landingPage->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $landingPage->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Slug (lp/slug-here)</label>
                                <div class="input-group">
                                    <span class="input-group-text">lp/</span>
                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $landingPage->slug) }}" required>
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
                            <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $landingPage->hero_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2">{{ old('hero_subtitle', $landingPage->hero_subtitle) }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Hero Image</label>
                            @if($landingPage->hero_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $landingPage->hero_image) }}" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                            @endif
                            <input type="file" name="hero_image" class="form-control">
                            <small class="text-muted">Upload a new image to replace the existing one. Max 2MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">Extra Content</h5>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="content" class="form-control" rows="10">{{ old('content', $landingPage->content) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status & Products -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="mb-0">Page Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $landingPage->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Is Active (Visible to Public)</label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_homepage" id="is_homepage" value="1" {{ $landingPage->is_homepage ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_homepage">Set as Homepage</label>
                            <small class="text-muted d-block">This page will appear on the root (/) URL.</small>
                        </div>

                        <hr>

                        <label class="form-label fw-bold mb-3 text-info">Select Featured Products</label>
                        <div class="products-list" style="max-height: 400px; overflow-y: auto;">
                            @php $selectedIds = $landingPage->products->pluck('id')->toArray(); @endphp
                            @foreach($products as $product)
                            <div class="form-check mb-2 p-2 bg-light rounded border">
                                <input class="form-check-input ms-0" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="prod_{{ $product->id }}"
                                    {{ in_array($product->id, $selectedIds) ? 'checked' : '' }}>
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
                            <label class="form-label fw-bold">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $landingPage->meta_title) }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="4">{{ old('meta_description', $landingPage->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid shadow">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save2 me-2"></i> Update Page
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
