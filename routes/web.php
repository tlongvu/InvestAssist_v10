<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/dashboard/sync', [\App\Http\Controllers\DashboardController::class, 'syncPrices'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.sync');

Route::get('/dashboard/realtime', [\App\Http\Controllers\DashboardController::class, 'realtime'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.realtime');

// Dashboard balance update popup (all users can update their own balance)
Route::post('/dashboard/update-balance', [\App\Http\Controllers\DashboardController::class, 'updateBalance'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.update-balance');

// Dashboard asset history endpoint (for period filters)
Route::get('/dashboard/asset-history', [\App\Http\Controllers\DashboardController::class, 'assetHistory'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.asset-history');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.update-settings');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Registering resourceful routes
    Route::resource('stocks', \App\Http\Controllers\StockController::class);
    Route::resource('cash-flows', \App\Http\Controllers\CashFlowController::class);
    Route::resource('stock-transactions', \App\Http\Controllers\StockTransactionController::class);
    Route::resource('asset-histories', \App\Http\Controllers\AssetHistoryController::class)->only(['index', 'store', 'destroy']);

    // Read-only listing for exchanges & industries (used in dropdowns for all users)
    Route::get('/exchanges', [\App\Http\Controllers\ExchangeController::class, 'index'])->name('exchanges.index');
    Route::get('/industries', [\App\Http\Controllers\IndustryController::class, 'index'])->name('industries.index');

    // Balance update (a user-specific action, not admin-only)
    Route::post('/exchange-balances', [\App\Http\Controllers\ExchangeController::class, 'updateBalances'])->name('exchange-balances.update');
});

// Admin routes – chỉ admin mới vào được
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    // Admin-only CRUD for exchanges
    Route::resource('exchanges', \App\Http\Controllers\Admin\ExchangeAdminController::class);
    // Admin-only CRUD for industries
    Route::resource('industries', \App\Http\Controllers\Admin\IndustryAdminController::class);
});

require __DIR__.'/auth.php';
