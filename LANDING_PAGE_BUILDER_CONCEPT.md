# Konsep Baru: Landing Page Builder

## 🎯 Konsep

### Current Flow (Sekarang)
```
Homepage → Pricing Page (fixed)
```

### New Flow (Baru)
```
Homepage → Landing Page (customizable)
Admin → Landing Pages → Create/Edit Landing Page → Select Products
```

## 📊 Struktur Database

### Table: landing_pages
```sql
CREATE TABLE landing_pages (
    id BIGINT PRIMARY KEY,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    hero_title TEXT,
    hero_subtitle TEXT,
    hero_image VARCHAR(255),
    content LONGTEXT, -- HTML/JSON content
    is_active BOOLEAN DEFAULT true,
    is_homepage BOOLEAN DEFAULT false, -- Set as homepage
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table: landing_page_products (Pivot)
```sql
CREATE TABLE landing_page_products (
    id BIGINT PRIMARY KEY,
    landing_page_id BIGINT,
    product_id BIGINT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (landing_page_id) REFERENCES landing_pages(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

## 🏗️ Implementasi

### 1. Migration
```bash
php artisan make:migration create_landing_pages_table
php artisan make:migration create_landing_page_products_table
```

### 2. Models

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
        'is_homepage',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_homepage' => 'boolean',
    ];

    // Relationship: Landing page has many products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'landing_page_products')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order');
    }
}
```

**Product Model (Update):**
```php
class Product extends Model
{
    // ... existing code ...

    // Relationship: Product can be in many landing pages
    public function landingPages()
    {
        return $this->belongsToMany(LandingPage::class, 'landing_page_products')
                    ->withPivot('sort_order');
    }
}
```

### 3. Routes

**Admin Routes:**
```php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Landing Pages Management
    Route::resource('landing-pages', LandingPageController::class);
    Route::post('landing-pages/{landingPage}/set-homepage', [LandingPageController::class, 'setHomepage'])
         ->name('landing-pages.set-homepage');
});

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/page/{slug}', [LandingPageController::class, 'show'])->name('landing-page.show');
```

### 4. Controllers

**Admin\LandingPageController:**
```php
class LandingPageController extends Controller
{
    public function index()
    {
        $landingPages = LandingPage::with('products')->latest()->paginate(10);
        return view('admin.landing-pages.index', compact('landingPages'));
    }

    public function create()
    {
        $products = Product::active()->get();
        return view('admin.landing-pages.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:landing_pages',
            'hero_title' => 'nullable|string',
            'hero_subtitle' => 'nullable|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'is_homepage' => 'boolean',
            'product_ids' => 'array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $landingPage = LandingPage::create($validated);

        // Attach products
        if ($request->has('product_ids')) {
            foreach ($request->product_ids as $index => $productId) {
                $landingPage->products()->attach($productId, [
                    'sort_order' => $index
                ]);
            }
        }

        // Set as homepage if requested
        if ($request->is_homepage) {
            LandingPage::where('id', '!=', $landingPage->id)
                       ->update(['is_homepage' => false]);
        }

        return redirect()->route('admin.landing-pages.index')
                        ->with('success', 'Landing page created!');
    }

    public function edit(LandingPage $landingPage)
    {
        $landingPage->load('products');
        $products = Product::active()->get();
        return view('admin.landing-pages.edit', compact('landingPage', 'products'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        // Similar to store...
        
        // Sync products
        $productIds = $request->product_ids ?? [];
        $syncData = [];
        foreach ($productIds as $index => $productId) {
            $syncData[$productId] = ['sort_order' => $index];
        }
        $landingPage->products()->sync($syncData);
        
        return redirect()->route('admin.landing-pages.index')
                        ->with('success', 'Landing page updated!');
    }
}
```

**HomeController (Update):**
```php
class HomeController extends Controller
{
    public function index()
    {
        // Get homepage landing page
        $landingPage = LandingPage::where('is_homepage', true)
                                  ->where('is_active', true)
                                  ->with('products.plans')
                                  ->first();

        if (!$landingPage) {
            // Fallback to default
            return view('home');
        }

        return view('landing-page', compact('landingPage'));
    }
}
```

## 🎨 Admin Views

### Landing Pages Index
**File:** `resources/views/admin/landing-pages/index.blade.php`

```blade
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Landing Pages</h2>
        <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Create Landing Page
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Homepage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($landingPages as $page)
                    <tr>
                        <td>{{ $page->title }}</td>
                        <td><code>{{ $page->slug }}</code></td>
                        <td>{{ $page->products->count() }} products</td>
                        <td>
                            <span class="badge bg-{{ $page->is_active ? 'success' : 'secondary' }}">
                                {{ $page->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if($page->is_homepage)
                            <span class="badge bg-primary">Homepage</span>
                            @else
                            <form method="POST" action="{{ route('admin.landing-pages.set-homepage', $page) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary">Set as Homepage</button>
                            </form>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.landing-pages.edit', $page) }}" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

### Create/Edit Landing Page
**File:** `resources/views/admin/landing-pages/create.blade.php`

```blade
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <h2 class="fw-bold mb-4">Create Landing Page</h2>

    <form method="POST" action="{{ route('admin.landing-pages.store') }}">
        @csrf

        <div class="row g-4">
            <!-- Basic Info -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Slug</label>
                            <input type="text" name="slug" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Hero Title</label>
                            <input type="text" name="hero_title" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Content</label>
                            <textarea name="content" class="form-control" rows="10"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_homepage" value="1" class="form-check-input">
                            <label class="form-check-label">Set as Homepage</label>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="card">
                    <div class="card-header">
                        <h5>Products</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Select products to display on this landing page</p>
                        @foreach($products as $product)
                        <div class="form-check mb-2">
                            <input type="checkbox" 
                                   name="product_ids[]" 
                                   value="{{ $product->id }}" 
                                   class="form-check-input">
                            <label class="form-check-label">
                                {{ $product->name }}
                                <small class="text-muted">({{ $product->plans->count() }} plans)</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Create Landing Page</button>
            <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
```

## 🌐 Frontend View

**File:** `resources/views/landing-page.blade.php`

```blade
@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <h1>{{ $landingPage->hero_title }}</h1>
    <p>{{ $landingPage->hero_subtitle }}</p>
</section>

<!-- Content -->
<section class="content">
    {!! $landingPage->content !!}
</section>

<!-- Products/Pricing -->
<section class="pricing">
    <h2>Choose Your Plan</h2>
    <div class="row">
        @foreach($landingPage->products as $product)
            @foreach($product->plans as $plan)
            <div class="col-md-4">
                <div class="pricing-card">
                    <h3>{{ $plan->name }}</h3>
                    <div class="price">${{ $plan->price }}</div>
                    <ul>
                        @foreach($plan->features as $feature)
                        <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('checkout.show', $plan) }}" class="btn btn-primary">
                        Get Started
                    </a>
                </div>
            </div>
            @endforeach
        @endforeach
    </div>
</section>
@endsection
```

## 🎯 Benefits

1. **Flexible**: Admin bisa buat multiple landing pages
2. **Customizable**: Setiap landing page bisa punya products berbeda
3. **Reusable**: Products bisa dipakai di multiple landing pages
4. **Easy Management**: Drag & drop products, set homepage
5. **SEO Friendly**: Custom meta title & description per page

## 📝 Implementation Steps

1. ✅ Create migrations
2. ✅ Create models with relationships
3. ✅ Create controllers
4. ✅ Create admin views (CRUD)
5. ✅ Create frontend view
6. ✅ Update routes
7. ✅ Test functionality

## 🚀 Usage Flow

**Admin:**
1. Go to "Landing Pages" menu
2. Click "Create Landing Page"
3. Fill in title, hero, content
4. Select products to display
5. Check "Set as Homepage" if needed
6. Save

**Frontend:**
- Homepage shows the landing page marked as "is_homepage"
- Other landing pages accessible via `/page/{slug}`

---

**Mau saya implement konsep ini?** 🚀
