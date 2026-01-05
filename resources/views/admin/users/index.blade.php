@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add New User
        </a>
    </div>
    <div class="col-md-6">
        <form method="GET" class="d-flex gap-2 justify-content-md-end">
            <select name="role" class="form-select form-select-sm" style="width: auto;">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
            <input type="text" name="search" placeholder="Search users..." class="form-control form-control-sm" style="width: 250px;" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search"></i> Filter
            </button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-people text-primary me-2"></i>
            All Users
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 fw-semibold">Name</th>
                        <th class="px-4 py-3 fw-semibold">Email</th>
                        <th class="px-4 py-3 fw-semibold">Role</th>
                        <th class="px-4 py-3 fw-semibold">Orders</th>
                        <th class="px-4 py-3 fw-semibold">Licenses</th>
                        <th class="px-4 py-3 fw-semibold">Joined</th>
                        <th class="px-4 py-3 fw-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info-subtle text-info small">You</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                {{ $user->email }}
                            </a>
                            @if($user->email_verified_at)
                                <i class="bi bi-patch-check-fill text-success small" title="Verified"></i>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($user->is_admin)
                                <span class="badge bg-danger-subtle text-danger border border-danger">
                                    <i class="bi bi-shield-fill"></i> Admin
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary">
                                    <i class="bi bi-person"></i> Customer
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark">{{ $user->orders()->count() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-light text-dark">{{ $user->licenses()->count() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="small">{{ $user->created_at->format('d M Y') }}</div>
                            <div class="small text-muted">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle Admin">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                <p class="mb-0">No users found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
