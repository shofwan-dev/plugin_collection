@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4 animate__animated animate__fadeInDown">
        <h2 class="mb-1 fw-bold">
            <i class="bi bi-person-circle text-primary me-2"></i> Profile Settings
        </h2>
        <p class="text-muted mb-0">Manage your account information and security</p>
    </div>

    @if(session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
        <i class="bi bi-check-circle me-2"></i> Profile updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
        <i class="bi bi-check-circle me-2"></i> Password updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person text-primary me-2"></i> Profile Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($user->email_verified_at)
                                <small class="text-success">
                                    <i class="bi bi-check-circle me-1"></i> Email verified
                                </small>
                                @else
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-circle me-1"></i> Email not verified
                                </small>
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted small">Member Since</label>
                                <div class="fw-semibold">{{ $user->created_at->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-bar-chart text-success me-2"></i> Account Stats
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded">
                        <div>
                            <div class="text-muted small">Total Orders</div>
                            <div class="fw-bold fs-4">{{ $user->orders()->count() }}</div>
                        </div>
                        <i class="bi bi-cart fs-2 text-primary"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                        <div>
                            <div class="text-muted small">Active Licenses</div>
                            <div class="fw-bold fs-4">{{ $user->licenses()->where('licenses.status', 'active')->count() }}</div>
                        </div>
                        <i class="bi bi-key fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Password -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock text-warning me-2"></i> Update Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <input type="password" 
                                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">New Password</label>
                                <input type="password" 
                                       class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-shield-check me-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="col-lg-4">
            <div class="card border-danger shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-header bg-danger bg-opacity-10 border-danger py-3">
                    <h5 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i> Danger Zone
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <i class="bi bi-trash fs-1 text-danger mb-3"></i>
                    <h6 class="fw-bold mb-2">Delete Account</h6>
                    <p class="text-muted small mb-3">Once deleted, all data will be permanently removed.</p>
                    <button type="button" 
                            class="btn btn-danger w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash me-2"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i> Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>Warning!</strong> This action cannot be undone. All your data will be permanently deleted.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm your password to continue:</label>
                        <input type="password" 
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                               name="password" 
                               placeholder="Enter your password" 
                               required>
                        @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Yes, Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush
@endsection
