<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SackSizeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::resource('products', ProductController::class)->except(['show']);

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::resource('sack-sizes', SackSizeController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['sack-sizes' => 'sackSize']);
    });
});
