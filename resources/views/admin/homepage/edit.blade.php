@extends('layouts.admin')

@section('title', 'Homepage Settings')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-4">
        <h2 class="fw-bold"><i class="bi bi-house-gear text-primary me-2"></i> Sync Homepage Settings</h2>
        <p class="text-muted">Directly synchronize and manage existing homepage sections.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.homepage.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- HERO SECTION -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i> Hero Section</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $homepage->hero_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2">{{ old('hero_subtitle', $homepage->hero_subtitle) }}</textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Primary CTA Text</label>
                                <input type="text" name="hero_cta_text" class="form-control" value="{{ old('hero_cta_text', $homepage->hero_cta_text) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Primary CTA Link</label>
                                <input type="text" name="hero_cta_link" class="form-control" value="{{ old('hero_cta_link', $homepage->hero_cta_link) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Secondary CTA Text</label>
                                <input type="text" name="hero_secondary_cta_text" class="form-control" value="{{ old('hero_secondary_cta_text', $homepage->hero_secondary_cta_text) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Secondary CTA Link</label>
                                <input type="text" name="hero_secondary_cta_link" class="form-control" value="{{ old('hero_secondary_cta_link', $homepage->hero_secondary_cta_link) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATS LABELS -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i> Stats Labels</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Label 1 (Products)</label>
                                <input type="text" name="stats_products_label" class="form-control" value="{{ old('stats_products_label', $homepage->stats_products_label) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Label 2 (Downloads)</label>
                                <input type="text" name="stats_downloads_label" class="form-control" value="{{ old('stats_downloads_label', $homepage->stats_downloads_label) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Label 3 (Uptime)</label>
                                <input type="text" name="stats_uptime_label" class="form-control" value="{{ old('stats_uptime_label', $homepage->stats_uptime_label) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Label 4 (Support)</label>
                                <input type="text" name="stats_support_label" class="form-control" value="{{ old('stats_support_label', $homepage->stats_support_label) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FEATURES SECTION -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-grid-fill me-2"></i> Why Choose Us</h5>
                        <button type="button" class="btn btn-sm btn-light" onclick="addFeatureItem()">
                            <i class="bi bi-plus-circle me-1"></i> Add Feature
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Section Title</label>
                                <input type="text" name="features_title" class="form-control" value="{{ old('features_title', $homepage->features_title) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Section Subtitle</label>
                                <input type="text" name="features_subtitle" class="form-control" value="{{ old('features_subtitle', $homepage->features_subtitle) }}">
                            </div>
                        </div>
                        
                        <div id="features-items-list" class="row g-3">
                            @foreach($homepage->features_items ?? [] as $index => $item)
                            <div class="col-md-6 feature-card-item">
                                <div class="p-3 border rounded bg-light position-relative">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.feature-card-item').remove()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <div class="mb-2">
                                        <label class="small fw-bold">Icon (Bootstrap Icon Class)</label>
                                        <input type="text" name="features_items[{{ $index }}][icon]" class="form-control form-control-sm" value="{{ $item['icon'] }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold">Title</label>
                                        <input type="text" name="features_items[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $item['title'] }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold">Description</label>
                                        <input type="text" name="features_items[{{ $index }}][description]" class="form-control form-control-sm" value="{{ $item['description'] }}">
                                    </div>
                                    <div>
                                        <label class="small fw-bold">Color (Hex)</label>
                                        <input type="color" name="features_items[{{ $index }}][color]" class="form-control form-control-color w-100" value="{{ $item['color'] }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- CTA BOTTOM SECTION -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning py-3 text-dark">
                        <h5 class="mb-0"><i class="bi bi-rocket-takeoff-fill me-2"></i> Bottom CTA</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">CTA Title</label>
                            <input type="text" name="cta_title" class="form-control" value="{{ old('cta_title', $homepage->cta_title) }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">CTA Subtitle</label>
                            <input type="text" name="cta_subtitle" class="form-control" value="{{ old('cta_subtitle', $homepage->cta_subtitle) }}">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Primary Button</label>
                                <div class="input-group">
                                    <input type="text" name="cta_primary_text" class="form-control" placeholder="Text" value="{{ old('cta_primary_text', $homepage->cta_primary_text) }}">
                                    <input type="text" name="cta_primary_link" class="form-control" placeholder="Link" value="{{ old('cta_primary_link', $homepage->cta_primary_link) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Secondary Button</label>
                                <div class="input-group">
                                    <input type="text" name="cta_secondary_text" class="form-control" placeholder="Text" value="{{ old('cta_secondary_text', $homepage->cta_secondary_text) }}">
                                    <input type="text" name="cta_secondary_link" class="form-control" placeholder="Link" value="{{ old('cta_secondary_link', $homepage->cta_secondary_link) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- FEATURED LANDING PAGES -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-richtext me-2"></i> Featured Landing Pages</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="small text-muted mb-3">Select landing pages to show on the main homepage loop.</p>
                        @foreach($landingPages as $page)
                        <div class="form-check mb-2 p-2 border rounded {{ $page->is_active ? 'bg-light' : 'bg-secondary bg-opacity-10' }}">
                            <input class="form-check-input" type="checkbox" name="featured_landing_page_ids[]" value="{{ $page->id }}" id="lp_{{ $page->id }}" 
                                {{ in_array($page->id, $homepage->featured_landing_page_ids ?? []) ? 'checked' : '' }}
                                {{ !$page->is_active ? 'disabled' : '' }}>
                            <label class="form-check-label fw-semibold" for="lp_{{ $page->id }}">
                                {{ $page->title }}
                                @if(!$page->is_active)
                                <span class="badge bg-secondary ms-1">Inactive</span>
                                @endif
                                @if($page->is_homepage)
                                <span class="badge bg-primary ms-1"><i class="bi bi-house-fill"></i></span>
                                @endif
                            </label>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-link-45deg"></i> /lp/{{ $page->slug }}
                            </div>
                        </div>
                        @endforeach
                        @if($landingPages->isEmpty())
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-inbox"></i>
                            <p class="mb-0 small">No landing pages yet.</p>
                            <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus"></i> Create One
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- SEO -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i> SEO Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $homepage->meta_title) }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="4">{{ old('meta_description', $homepage->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow">
                        <i class="bi bi-save2-fill me-2"></i> Save Changes
                    </button>
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-dark">
                        <i class="bi bi-eye-fill me-2"></i> View Live
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let featureCount = {{ count($homepage->features_items ?? []) }};
    function addFeatureItem() {
        const list = document.getElementById('features-items-list');
        const div = document.createElement('div');
        div.className = 'col-md-6 feature-card-item';
        div.innerHTML = `
            <div class="p-3 border rounded bg-light position-relative">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.feature-card-item').remove()">
                    <i class="bi bi-trash"></i>
                </button>
                <div class="mb-2">
                    <label class="small fw-bold">Icon (Bootstrap Icon Class)</label>
                    <input type="text" name="features_items[${featureCount}][icon]" class="form-control form-control-sm" placeholder="bi-cpu">
                </div>
                <div class="mb-2">
                    <label class="small fw-bold">Title</label>
                    <input type="text" name="features_items[${featureCount}][title]" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                    <label class="small fw-bold">Description</label>
                    <input type="text" name="features_items[${featureCount}][description]" class="form-control form-control-sm">
                </div>
                <div>
                    <label class="small fw-bold">Color (Hex)</label>
                    <input type="color" name="features_items[${featureCount}][color]" class="form-control form-control-color w-100" value="#6366f1">
                </div>
            </div>
        `;
        list.appendChild(div);
        featureCount++;
    }
</script>
@endpush
@endsection
