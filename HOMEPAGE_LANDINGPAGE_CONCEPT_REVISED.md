# Konsep Revisi: Homepage Manager + Landing Page Builder

## 🎯 Konsep Final

### Structure
```
1. Homepage (/) - Managed separately, current design tetap
2. Landing Pages (/page/{slug}) - Custom pages untuk products
```

### Admin Menu
```
Admin Panel
├─ Homepage Settings (Manage homepage content)
├─ Landing Pages (Create custom product pages)
├─ Products (Manage products & files)
└─ Plans (Manage pricing plans)
```

## 📊 Database Structure

### Table: homepage_settings
```sql
CREATE TABLE homepage_settings (
    id BIGINT PRIMARY KEY,
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    hero_cta_text VARCHAR(100),
    hero_cta_link VARCHAR(255),
    hero_image VARCHAR(255),
    
    -- Features Section
    features_title VARCHAR(255),
    features_subtitle TEXT,
    features JSON, -- Array of features
    
    -- About Section
    about_title VARCHAR(255),
    about_content TEXT,
    about_image VARCHAR(255),
    
    -- CTA Section
    cta_title VARCHAR(255),
    cta_subtitle TEXT,
    cta_button_text VARCHAR(100),
    cta_button_link VARCHAR(255),
    
    -- Selected Products to Display
    featured_product_ids JSON, -- Array of product IDs
    
    -- SEO
    meta_title VARCHAR(255),
    meta_description TEXT,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table: landing_pages (Same as before)
```sql
CREATE TABLE landing_pages (
    id BIGINT PRIMARY KEY,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    hero_title TEXT,
    hero_subtitle TEXT,
    hero_image VARCHAR(255),
    content LONGTEXT,
    is_active BOOLEAN DEFAULT true,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table: landing_page_products (Same as before)
```sql
CREATE TABLE landing_page_products (
    id BIGINT PRIMARY KEY,
    landing_page_id BIGINT,
    product_id BIGINT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (landing_page_id) REFERENCES landing_pages(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

## 🏗️ Implementation

### 1. Models

**HomepageSetting Model:**
```php
class HomepageSetting extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'hero_cta_link',
        'hero_image',
        'features_title',
        'features_subtitle',
        'features',
        'about_title',
        'about_content',
        'about_image',
        'cta_title',
        'cta_subtitle',
        'cta_button_text',
        'cta_button_link',
        'featured_product_ids',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'features' => 'array',
        'featured_product_ids' => 'array',
    ];

    // Get featured products
    public function featuredProducts()
    {
        if (!$this->featured_product_ids) {
            return collect();
        }
        
        return Product::whereIn('id', $this->featured_product_ids)
                      ->with('plans')
                      ->get();
    }

    // Singleton pattern - only one homepage settings
    public static function current()
    {
        return static::firstOrCreate([]);
    }
}
```

**LandingPage Model:**
```php
class LandingPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'content',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'landing_page_products')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order');
    }
}
```

### 2. Routes

```php
// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Homepage Management
    Route::get('homepage', [HomepageController::class, 'edit'])->name('admin.homepage.edit');
    Route::put('homepage', [HomepageController::class, 'update'])->name('admin.homepage.update');
    
    // Landing Pages Management
    Route::resource('landing-pages', LandingPageController::class);
    
    // Products & Plans (existing)
    Route::resource('products', ProductController::class);
    Route::resource('plans', PlanController::class);
});

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing'); // Keep existing
```

### 3. Controllers

**Admin\HomepageController:**
```php
class HomepageController extends Controller
{
    public function edit()
    {
        $homepage = HomepageSetting::current();
        $products = Product::active()->with('plans')->get();
        
        return view('admin.homepage.edit', compact('homepage', 'products'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_cta_text' => 'nullable|string|max:100',
            'hero_cta_link' => 'nullable|string|max:255',
            'features_title' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'about_title' => 'nullable|string|max:255',
            'about_content' => 'nullable|string',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'featured_product_ids' => 'nullable|array',
            'featured_product_ids.*' => 'exists:products,id',
        ]);

        $homepage = HomepageSetting::current();
        $homepage->update($validated);

        return redirect()->route('admin.homepage.edit')
                        ->with('success', 'Homepage updated successfully!');
    }
}
```

**HomeController (Frontend):**
```php
class HomeController extends Controller
{
    public function index()
    {
        $homepage = HomepageSetting::current();
        $featuredProducts = $homepage->featuredProducts();
        
        return view('home', compact('homepage', 'featuredProducts'));
    }
}
```

**LandingPageController (Frontend):**
```php
class LandingPageController extends Controller
{
    public function show($slug)
    {
        $landingPage = LandingPage::where('slug', $slug)
                                  ->where('is_active', true)
                                  ->with('products.plans')
                                  ->firstOrFail();
        
        return view('landing-page', compact('landingPage'));
    }
}
```

## 🎨 Admin Views

### Homepage Settings
**File:** `resources/views/admin/homepage/edit.blade.php`

```blade
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-house-door text-primary me-2"></i> Homepage Settings
    </h2>

    <form method="POST" action="{{ route('admin.homepage.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Hero Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Hero Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control" 
                                   value="{{ old('hero_title', $homepage->hero_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2">{{ old('hero_subtitle', $homepage->hero_subtitle) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">CTA Button Text</label>
                                <input type="text" name="hero_cta_text" class="form-control" 
                                       value="{{ old('hero_cta_text', $homepage->hero_cta_text) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CTA Button Link</label>
                                <input type="text" name="hero_cta_link" class="form-control" 
                                       value="{{ old('hero_cta_link', $homepage->hero_cta_link) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Features Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Features Title</label>
                            <input type="text" name="features_title" class="form-control" 
                                   value="{{ old('features_title', $homepage->features_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features Subtitle</label>
                            <textarea name="features_subtitle" class="form-control" rows="2">{{ old('features_subtitle', $homepage->features_subtitle) }}</textarea>
                        </div>
                        
                        <!-- Features List (Dynamic) -->
                        <div id="features-container">
                            @if($homepage->features)
                                @foreach($homepage->features as $index => $feature)
                                <div class="feature-item mb-2">
                                    <input type="text" name="features[{{ $index }}]" 
                                           class="form-control" value="{{ $feature }}" 
                                           placeholder="Feature {{ $index + 1 }}">
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="addFeature()">
                            <i class="bi bi-plus"></i> Add Feature
                        </button>
                    </div>
                </div>

                <!-- About Section -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">About Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">About Title</label>
                            <input type="text" name="about_title" class="form-control" 
                                   value="{{ old('about_title', $homepage->about_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">About Content</label>
                            <textarea name="about_content" class="form-control" rows="5">{{ old('about_content', $homepage->about_content) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Call-to-Action Section</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">CTA Title</label>
                            <input type="text" name="cta_title" class="form-control" 
                                   value="{{ old('cta_title', $homepage->cta_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CTA Subtitle</label>
                            <textarea name="cta_subtitle" class="form-control" rows="2">{{ old('cta_subtitle', $homepage->cta_subtitle) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="cta_button_text" class="form-control" 
                                       value="{{ old('cta_button_text', $homepage->cta_button_text) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Button Link</label>
                                <input type="text" name="cta_button_link" class="form-control" 
                                       value="{{ old('cta_button_link', $homepage->cta_button_link) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Featured Products -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Featured Products</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Select products to display on homepage</p>
                        @foreach($products as $product)
                        <div class="form-check mb-2">
                            <input type="checkbox" 
                                   name="featured_product_ids[]" 
                                   value="{{ $product->id }}" 
                                   class="form-check-input"
                                   {{ in_array($product->id, $homepage->featured_product_ids ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                {{ $product->name }}
                                <small class="text-muted d-block">{{ $product->plans->count() }} plans</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- SEO -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" 
                                   value="{{ old('meta_title', $homepage->meta_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $homepage->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle me-2"></i> Save Homepage Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let featureIndex = {{ count($homepage->features ?? []) }};

function addFeature() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'feature-item mb-2';
    div.innerHTML = `
        <input type="text" name="features[${featureIndex}]" 
               class="form-control" placeholder="Feature ${featureIndex + 1}">
    `;
    container.appendChild(div);
    featureIndex++;
}
</script>
@endpush
@endsection
```

## 🌐 Frontend (Homepage tetap sama)

**File:** `resources/views/home.blade.php` (Keep existing design, just use dynamic data)

```blade
{{-- Existing homepage design --}}
{{-- Just replace static text with: --}}

<h1>{{ $homepage->hero_title ?? 'Default Title' }}</h1>
<p>{{ $homepage->hero_subtitle ?? 'Default Subtitle' }}</p>

{{-- Features --}}
@foreach($homepage->features ?? [] as $feature)
    <li>{{ $feature }}</li>
@endforeach

{{-- Featured Products --}}
@foreach($featuredProducts as $product)
    {{-- Display product with plans --}}
@endforeach
```

## 📋 Summary

### Struktur Final:
```
1. Homepage (/) 
   - Managed via: Admin → Homepage Settings
   - Design: Keep existing (current design bagus)
   - Content: Dynamic from database

2. Landing Pages (/page/{slug})
   - Managed via: Admin → Landing Pages
   - Purpose: Custom product pages
   - Content: Fully customizable

3. Pricing (/pricing)
   - Keep existing
   - Shows all plans
```

### Admin Menu:
```
Admin Panel
├─ Dashboard
├─ Homepage Settings ← NEW! (Manage homepage)
├─ Landing Pages ← NEW! (Create product pages)
├─ Products (Existing)
├─ Plans (Existing)
└─ Settings (Existing)
```

### Benefits:
- ✅ Homepage tetap dengan design sekarang
- ✅ Homepage content editable dari admin
- ✅ Landing Pages untuk custom product pages
- ✅ Flexible & scalable
- ✅ SEO friendly

**Mau saya implement konsep revisi ini?** 🚀
