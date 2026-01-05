# Sinkronisasi Plans dengan Products

## Problem
- Landing page menampilkan 3 Plans (pricing tiers)
- Product edit page tidak ada Plans
- Plans dan Products terpisah

## Solution
Menghubungkan Plans dengan Products menggunakan relationship.

## Step 1: Migration (SUDAH DIBUAT)
File: `database/migrations/2026_01_04_125319_add_product_id_to_plans_table.php`

Menambahkan kolom `product_id` ke table `plans`:
```php
$table->foreignId('product_id')->nullable()->after('id')->constrained()->onDelete('cascade');
```

## Step 2: Run Migration
```bash
php artisan migrate
```

## Step 3: Update Models

### Plan Model
File: `app/Models/Plan.php`

Tambahkan ke `$fillable`:
```php
protected $fillable = [
    'product_id',  // ← ADD THIS
    'name',
    'slug',
    // ...
];
```

Tambahkan relationship:
```php
/**
 * Get the product that owns the plan
 */
public function product()
{
    return $this->belongsTo(Product::class);
}
```

### Product Model
File: `app/Models/Product.php`

Tambahkan relationship:
```php
/**
 * Get plans for this product
 */
public function plans()
{
    return $this->hasMany(Plan::class)->ordered();
}
```

## Step 4: Update Seeder

File: `database/seeders/DatabaseSeeder.php`

Update untuk link plans ke product:
```php
// Create product
$product = Product::create([
    'name' => 'CF7 to WhatsApp Gateway',
    'type' => 'plugin',
    'version' => '1.0.0',
    // ...
]);

// Create plans for this product
$plans = [
    [
        'product_id' => $product->id,  // ← Link to product
        'name' => 'Single Site',
        'price' => 29,
        'max_domains' => 1,
        // ...
    ],
    [
        'product_id' => $product->id,  // ← Link to product
        'name' => '5 Sites',
        'price' => 79,
        'max_domains' => 5,
        'is_popular' => true,
        // ...
    ],
    [
        'product_id' => $product->id,  // ← Link to product
        'name' => 'Unlimited',
        'price' => 149,
        'max_domains' => -1,
        // ...
    ],
];

foreach ($plans as $planData) {
    Plan::create($planData);
}
```

## Step 5: Update Product Edit Page

Tambahkan section untuk manage plans di `resources/views/admin/products/edit.blade.php`:

```blade
<!-- Plans Section -->
<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-tag text-success me-2"></i> Pricing Plans
                </h5>
                <a href="{{ route('admin.plans.create', ['product' => $product->id]) }}" 
                   class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Add Plan
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            @if($product->plans->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Max Domains</th>
                            <th>Popular</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->plans as $plan)
                        <tr>
                            <td class="fw-semibold">{{ $plan->name }}</td>
                            <td>${{ number_format($plan->price, 2) }}</td>
                            <td>{{ $plan->max_domains === -1 ? 'Unlimited' : $plan->max_domains }}</td>
                            <td>
                                @if($plan->is_popular)
                                <span class="badge bg-warning">⭐ Popular</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.plans.edit', $plan) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-tag fs-1 mb-2"></i>
                <p>No pricing plans yet. Add your first plan!</p>
            </div>
            @endif
        </div>
    </div>
</div>
```

## Step 6: Update Controllers

### PricingController
File: `app/Http/Controllers/PricingController.php`

Update query untuk filter by product:
```php
public function index()
{
    // Get active product (or specific product)
    $product = Product::where('is_active', true)->first();
    
    // Get plans for this product
    $plans = Plan::where('product_id', $product->id)
        ->active()
        ->ordered()
        ->get();
    
    return view('pricing', compact('plans', 'product'));
}
```

## Struktur Final

```
Product: CF7 to WhatsApp Gateway
├─ Plan 1: Single Site ($29)
│  ├─ Max Domains: 1
│  └─ Features: [...]
├─ Plan 2: 5 Sites ($79) ⭐ Popular
│  ├─ Max Domains: 5
│  └─ Features: [...]
└─ Plan 3: Unlimited ($149)
   ├─ Max Domains: -1
   └─ Features: [...]
```

## Benefits

✅ **Sinkronisasi**: Plans terhubung dengan Product
✅ **Flexibility**: Bisa punya multiple products dengan plans berbeda
✅ **Management**: Edit plans dari product edit page
✅ **Consistency**: Landing page dan admin panel sinkron

## Next Steps

1. Run migration: `php artisan migrate`
2. Update Plan model (add product_id to fillable + relationship)
3. Update Product model (add plans relationship)
4. Update seeder (link existing plans to product)
5. Update product edit page (add plans section)
6. Update pricing controller (filter by product)

## Testing

1. Buka product edit page
2. Lihat section "Pricing Plans"
3. Add/Edit plans
4. Cek landing page - plans harus sinkron
