@extends('layouts.admin')

@section('title', 'Landing Pages')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="bi bi-file-earmark-plus text-primary me-2"></i> Landing Pages</h2>
            <p class="text-muted">Manage your custom landing pages for specific products or campaigns.</p>
        </div>
        <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Create New Page
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Page Details</th>
                            <th class="py-3">Slug</th>
                            <th class="py-3">Products</th>
                            <th class="py-3 text-center">Homepage</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($landingPages as $page)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold">{{ $page->title }}</div>
                                <div class="small text-muted">{{ $page->hero_title }}</div>
                            </td>
                            <td class="py-3">
                                <code class="bg-light px-2 py-1 rounded">/lp/{{ $page->slug }}</code>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-info text-white rounded-pill px-3">
                                    {{ $page->products->count() }} Products
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                @if($page->is_homepage)
                                <span class="badge bg-primary px-3"><i class="bi bi-house-fill me-1"></i> Default Home</span>
                                @else
                                <span class="text-muted small">No</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($page->is_active)
                                <span class="badge bg-success-subtle text-success px-3">Active</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger px-3">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('landing-page.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Live">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.landing-pages.edit', $page->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" 
                                            onclick="if(confirm('Are you sure you want to delete this page?')) document.getElementById('delete-form-{{ $page->id }}').submit();">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $page->id }}" action="{{ route('admin.landing-pages.destroy', $page->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-x display-1 d-block mb-3"></i>
                                <p>No landing pages found. Start by creating one!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($landingPages->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $landingPages->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
