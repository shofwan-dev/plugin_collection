@extends('layouts.admin')

@section('page-title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 fw-bold">
        <i class="bi bi-box-seam text-primary me-2"></i>
        Products
    </h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Add New Product
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">Product</th>
                        <th class="px-4 py-3 fw-semibold">Type</th>
                        <th class="px-4 py-3 fw-semibold">Version</th>
                        <th class="px-4 py-3 fw-semibold">Price</th>
                        <th class="px-4 py-3 fw-semibold">Max Domains</th>
                        <th class="px-4 py-3 fw-semibold">Status</th>
                        <th class="px-4 py-3 fw-semibold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="rounded me-3"
                                     style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 48px; height: 48px;">
                                    <i class="bi bi-box-seam text-primary fs-4"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                    <div class="text-muted small">{{ Str::limit($product->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ ucfirst($product->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                v{{ $product->version }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="fw-bold text-success">
                                ${{ number_format($product->price, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($product->max_domains === -1)
                                <span class="badge bg-info">
                                    <i class="bi bi-infinity"></i> Unlimited
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ $product->max_domains }} {{ Str::plural('domain', $product->max_domains) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($product->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex gap-2 justify-content-end">
                                <!-- View Product Page -->
                                <a href="{{ route('product.show', $product->slug) }}" 
                                   class="btn btn-sm btn-outline-info"
                                   title="View Product Page"
                                   target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($product->file_path)
                                <a href="{{ route('admin.products.download', $product) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center">
                            <div class="text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-2">No products yet.</p>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Add Your First Product
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($products->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>
@endif
@endsection
