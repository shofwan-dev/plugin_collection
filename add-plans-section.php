<?php

$file = __DIR__ . '/resources/views/admin/products/edit.blade.php';
$content = file_get_contents($file);

$plansSection = <<<'BLADE'

        <!-- Pricing Plans Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-tag text-success me-2"></i> Pricing Plans
                            </h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                {{ $product->plans->count() }} {{ $product->plans->count() === 1 ? 'Plan' : 'Plans' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if($product->plans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Plan Name</th>
                                        <th>Price</th>
                                        <th>Max Domains</th>
                                        <th>Status</th>
                                        <th class="text-center">Popular</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->plans as $plan)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-box-seam text-primary fs-5 me-2"></i>
                                                <span class="fw-semibold">{{ $plan->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-5">${{ number_format($plan->price, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($plan->max_domains === -1)
                                            <span class="badge bg-info px-3 py-2">
                                                <i class="bi bi-infinity me-1"></i> Unlimited
                                            </span>
                                            @else
                                            <span class="badge bg-secondary px-3 py-2">{{ $plan->max_domains }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plan->is_active)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i> Active
                                            </span>
                                            @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="bi bi-x-circle me-1"></i> Inactive
                                            </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($plan->is_popular)
                                            <span class="badge bg-warning text-dark px-3 py-2">⭐ Popular</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-tag fs-1 text-muted mb-3 d-block"></i>
                            <h6 class="text-muted mb-2">No Pricing Plans Yet</h6>
                            <p class="text-muted small">Plans are managed separately in Plans section</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
BLADE;

// Find the position to insert (before "Action Buttons")
$marker = '        <!-- Action Buttons -->';
$position = strpos($content, $marker);

if ($position !== false) {
    // Insert the plans section before Action Buttons
    $newContent = substr($content, 0, $position) . $plansSection . "\n" . substr($content, $position);
    file_put_contents($file, $newContent);
    echo "✅ Plans section added successfully!\n";
    echo "Location: Before 'Action Buttons' section\n";
} else {
    echo "❌ Could not find 'Action Buttons' marker\n";
    echo "Please add manually\n";
}
