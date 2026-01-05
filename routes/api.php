<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// License API v1
Route::prefix('v1')->middleware(['throttle:60,1', 'api.logger'])->group(function () {
    Route::prefix('license')->name('api.license.')->group(function () {
        Route::post('/activate', [LicenseController::class, 'activate'])->name('activate');
        Route::post('/verify', [LicenseController::class, 'validate'])->name('verify');
        Route::post('/deactivate', [LicenseController::class, 'deactivate'])->name('deactivate');
        Route::post('/check-update', [LicenseController::class, 'checkUpdate'])->name('check-update');
        Route::get('/download', [LicenseController::class, 'download'])->name('download');
        Route::get('/check/{license_key}', [LicenseController::class, 'check'])->name('check');
    });
});
