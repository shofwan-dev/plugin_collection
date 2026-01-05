<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Plan;

// Get first product
$product = Product::first();

if (!$product) {
    echo "No product found! Please create a product first.\n";
    exit(1);
}

// Update all plans to link to this product
$updated = Plan::query()->update(['product_id' => $product->id]);

echo "✅ Success!\n";
echo "Linked {$updated} plans to product: {$product->name}\n";
echo "\nPlans:\n";

foreach (Plan::all() as $plan) {
    echo "  - {$plan->name} (${$plan->price}) → Product ID: {$plan->product_id}\n";
}
