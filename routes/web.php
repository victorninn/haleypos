<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\PlaySessionController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Superadmin\AuthController as SuperadminAuthController;
use App\Http\Controllers\Superadmin\BusinessController as SaBusinessController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboard;
use App\Http\Controllers\Superadmin\SubscriptionController as SaSubscriptionController;
use Illuminate\Support\Facades\Route;

// Home is now the tenant login screen
Route::get('/', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Public parent portal — scoped to one tenant via slug, no login required
Route::get('/{business:slug}/lookup', [ParentPortalController::class, 'show'])->name('parent.lookup');
Route::get('/{business:slug}/children/{child}/qr', [ParentPortalController::class, 'qr'])->name('children.qr');

// Subscription expired landing (auth required so we can show business info)
Route::get('/subscription/expired', function () {
    return view('subscription.expired');
})->middleware('auth')->name('subscription.expired');

/*
|--------------------------------------------------------------------------
| Superadmin (platform control plane)
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->group(function () {
    Route::get('/login', [SuperadminAuthController::class, 'show'])->middleware('guest')->name('superadmin.login');
    Route::post('/login', [SuperadminAuthController::class, 'store'])->middleware('guest')->name('superadmin.login.store');
    Route::post('/logout', [SuperadminAuthController::class, 'destroy'])->middleware('auth')->name('superadmin.logout');

    Route::middleware(['auth', 'superadmin'])->group(function () {
        Route::get('/', SuperadminDashboard::class)->name('superadmin.dashboard');
        Route::get('/dashboard', SuperadminDashboard::class)->name('superadmin.dashboard.alias');

        Route::get('/businesses/archived', [SaBusinessController::class, 'archived'])->name('superadmin.businesses.archived');
        Route::get('/businesses/trashed',  [SaBusinessController::class, 'trashed'])->name('superadmin.businesses.trashed');
        Route::post('/businesses/{id}/restore-deleted', [SaBusinessController::class, 'restoreDeleted'])->name('superadmin.businesses.restoreDeleted');
        Route::delete('/businesses/{id}/force',         [SaBusinessController::class, 'forceDelete'])->name('superadmin.businesses.forceDelete');

        Route::get('/businesses',                       [SaBusinessController::class, 'index'])->name('superadmin.businesses.index');
        Route::get('/businesses/create',                [SaBusinessController::class, 'create'])->name('superadmin.businesses.create');
        Route::post('/businesses',                      [SaBusinessController::class, 'store'])->name('superadmin.businesses.store');
        Route::get('/businesses/{business}',            [SaBusinessController::class, 'show'])->name('superadmin.businesses.show');
        Route::get('/businesses/{business}/edit',       [SaBusinessController::class, 'edit'])->name('superadmin.businesses.edit');
        Route::put('/businesses/{business}',            [SaBusinessController::class, 'update'])->name('superadmin.businesses.update');
        Route::delete('/businesses/{business}',         [SaBusinessController::class, 'destroy'])->name('superadmin.businesses.destroy');

        Route::post('/businesses/{business}/deactivate', [SaBusinessController::class, 'deactivate'])->name('superadmin.businesses.deactivate');
        Route::post('/businesses/{business}/reactivate', [SaBusinessController::class, 'reactivate'])->name('superadmin.businesses.reactivate');
        Route::post('/businesses/{business}/archive',    [SaBusinessController::class, 'archive'])->name('superadmin.businesses.archive');
        Route::post('/businesses/{business}/restore',    [SaBusinessController::class, 'restore'])->name('superadmin.businesses.restore');

        Route::post('/businesses/{business}/subscription',               [SaSubscriptionController::class, 'update'])->name('superadmin.businesses.subscription.update');
        Route::post('/businesses/{business}/subscription/reactivate',    [SaSubscriptionController::class, 'reactivate'])->name('superadmin.businesses.subscription.reactivate');
    });
});

/*
|--------------------------------------------------------------------------
| Tenant app — requires active business + active subscription
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/display', [DashboardController::class, 'display'])->name('dashboard.display');


    // Children
    Route::resource('children', ChildController::class);

    // Sessions
    Route::get('/sessions/start', [PlaySessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions/start', [PlaySessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [PlaySessionController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{session}/extend', [PlaySessionController::class, 'extend'])->name('sessions.extend');
    Route::post('/sessions/{session}/end', [PlaySessionController::class, 'end'])->name('sessions.end');

    // Packages
    Route::resource('packages', PackageController::class)->except(['show']);

    // Receipts
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/print', [ReceiptController::class, 'print'])->name('receipts.print');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/reports/export-month', [ReportController::class, 'exportMonth'])->name('reports.exportMonth');

    // Settings & Staff (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::post('/staff/{user}/toggle', [StaffController::class, 'toggle'])->name('staff.toggle');
    });
});
