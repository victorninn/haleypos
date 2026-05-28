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
use Illuminate\Support\Facades\Route;

// Public parent portal (no login)
Route::get('/', [ParentPortalController::class, 'show'])->name('home');
Route::get('/lookup', [ParentPortalController::class, 'show'])->name('parent.lookup');
Route::get('/children/{child}/qr', [ParentPortalController::class, 'qr'])->name('children.qr');

// Auth
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Authenticated app (admin + staff)
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

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
