<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LicenseController as AdminLicenseController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pricing', [HomeController::class, 'index'])->name('pricing'); // Alias for home page
Route::get('/product/{product:slug}', [HomeController::class, 'product'])->name('product.show');
Route::get('/documentation', [HomeController::class, 'documentation'])->name('documentation');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Legal Pages
Route::get('/terms-of-service', [App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy-policy', [App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/refund-policy', [App\Http\Controllers\LegalController::class, 'refund'])->name('legal.refund');

// Checkout Success/Cancel (must be before checkout/{product} route)
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Checkout
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{product:slug}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{product:slug}', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/save-data', [CheckoutController::class, 'saveData'])->name('checkout.save-data');
});

// Envato Verification
Route::middleware('auth')->group(function () {
    Route::get('/verify-envato-purchase', [\App\Http\Controllers\EnvatoVerificationController::class, 'show'])->name('envato.show');
    Route::post('/verify-envato-purchase', [\App\Http\Controllers\EnvatoVerificationController::class, 'verify'])->name('envato.verify');
});

// Webhooks
Route::post('/webhook/stripe', [WebhookController::class, 'stripe'])->name('webhook.stripe');
Route::post('/webhook/paddle', [WebhookController::class, 'paddle'])->name('webhook.paddle');

// Customer Dashboard
Route::middleware(['auth', 'verified', 'redirect.role'])->group(function () {
    // Main dashboard route
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard sub-routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/licenses', [CustomerDashboardController::class, 'licenses'])->name('licenses');
        
        // Customer Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [CustomerOrderController::class, 'show'])->name('show');
            Route::put('/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('cancel');
        });
    });
});

// Admin Dashboard
Route::middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Licenses
    Route::prefix('licenses')->name('licenses.')->group(function () {
        Route::get('/', [AdminLicenseController::class, 'index'])->name('index');
        Route::get('/{license}', [AdminLicenseController::class, 'show'])->name('show');
        Route::post('/{license}/suspend', [AdminLicenseController::class, 'suspend'])->name('suspend');
        Route::post('/{license}/activate', [AdminLicenseController::class, 'activate'])->name('activate');
        Route::post('/{license}/deactivate-domain', [AdminLicenseController::class, 'deactivateDomain'])->name('deactivate-domain');
    });
    
    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
        Route::put('/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
    });
    
    // Products
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::get('/products/{product}/download', [\App\Http\Controllers\Admin\ProductController::class, 'download'])->name('products.download');
    Route::post('/products/{product}/duplicate', [\App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
    
    // Users
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('/users/{user}/toggle-admin', [\App\Http\Controllers\Admin\UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    
    // Homepage Management
    Route::get('homepage', [\App\Http\Controllers\Admin\HomepageController::class, 'edit'])->name('homepage.edit');
    Route::put('homepage', [\App\Http\Controllers\Admin\HomepageController::class, 'update'])->name('homepage.update');

    // Landing Pages
    Route::resource('landing-pages', \App\Http\Controllers\Admin\LandingPageController::class);
    
    // Fallback POST route for Nginx servers that don't handle PUT properly
    Route::post('landing-pages/{landing_page}/update', [\App\Http\Controllers\Admin\LandingPageController::class, 'update'])
        ->name('landing-pages.update-post');
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('index');
        Route::put('/', [AdminSettingController::class, 'update'])->name('update');
        Route::post('/test-whatsapp', [AdminSettingController::class, 'testWhatsApp'])->name('test-whatsapp');
        Route::post('/test-email', [AdminSettingController::class, 'testEmail'])->name('test-email');
        Route::post('/test-paddle', [AdminSettingController::class, 'testPaddle'])->name('test-paddle');
    });
});

// Public Landing Page Slug
Route::get('/lp/{slug}', [\App\Http\Controllers\HomeController::class, 'landingPage'])->name('landing-page.show');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

