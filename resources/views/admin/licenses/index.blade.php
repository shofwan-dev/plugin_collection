@extends('layouts.admin')

@section('title', 'Licenses')
@section('page-title', 'License Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-key text-primary me-2"></i>
                    All Licenses
                </h5>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex gap-2 justify-content-md-end">
                    <select name="status" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <input type="text" name="search" placeholder="Search license key..." class="form-control form-control-sm" style="width: 200px;" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">License Key</th>
                        <th class="px-4 py-3 fw-semibold">Customer</th>
                        <th class="px-4 py-3 fw-semibold">Plan</th>
                        <th class="px-4 py-3 fw-semibold">Status</th>
                        <th class="px-4 py-3 fw-semibold">Domains</th>
                        <th class="px-4 py-3 fw-semibold">Expires</th>
                        <th class="px-4 py-3 fw-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenses as $license)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.licenses.show', $license) }}" class="text-primary text-decoration-none">
                                <code class="bg-light px-2 py-1 rounded">{{ $license->license_key }}</code>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            @if($license->user)
                                <div class="fw-semibold">{{ $license->user->name }}</div>
                                <div class="small text-muted">{{ $license->user->email }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark border">{{ $license->plan->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($license->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success">
                                    <i class="bi bi-check-circle-fill"></i> Active
                                </span>
                            @elseif($license->status === 'expired')
                                <span class="badge bg-danger-subtle text-danger border border-danger">
                                    <i class="bi bi-x-circle-fill"></i> Expired
                                </span>
                            @elseif($license->status === 'suspended')
                                <span class="badge bg-warning-subtle text-warning border border-warning">
                                    <i class="bi bi-pause-circle-fill"></i> Suspended
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">
                                    {{ ucfirst($license->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $activatedCount = count($license->activated_domains ?? []);
                                $maxDomains = $license->max_domains === -1 ? '∞' : $license->max_domains;
                                $percentage = $license->max_domains === -1 ? 0 : ($activatedCount / $license->max_domains * 100);
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold">{{ $activatedCount }} / {{ $maxDomains }}</span>
                                @if($license->max_domains !== -1)
                                    <div class="progress" style="width: 60px; height: 6px;">
                                        <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             style="width: {{ min($percentage, 100) }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($license->expires_at)
                                <div class="small">{{ $license->expires_at->format('d M Y') }}</div>
                                @if($license->expires_at->isPast())
                                    <span class="badge bg-danger-subtle text-danger small">Expired</span>
                                @elseif($license->expires_at->diffInDays() <= 30)
                                    <span class="badge bg-warning-subtle text-warning small">{{ $license->expires_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-muted small">{{ $license->expires_at->diffForHumans() }}</span>
                                @endif
                            @else
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-infinity"></i> Lifetime
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.licenses.show', $license) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <p class="mb-0">No licenses found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($licenses->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $licenses->links() }}
    </div>
    @endif
</div>
@endsection
