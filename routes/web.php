<?php

// Save as: routes/web.php

use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoWalletController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Customer landing page (resources/views/welcome.blade.php)
Route::view('/', 'welcome');

// Demo wallet: opened from the QR code on a phone. No login, but the links are signed and expire.
Route::middleware('signed:relative')->group(function () {
    Route::get('/demo-wallet/{order}', [DemoWalletController::class, 'show'])->name('wallet.show');
    Route::post('/demo-wallet/{order}/pay', [DemoWalletController::class, 'pay'])->name('wallet.pay');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Admin
    // NOTE: only 'auth' protects these. Make sure the controllers (or a role
    // middleware) block normal customers from opening /admin/*.
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('services', AdminServiceController::class);
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payments
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('orders.payment.store');
    Route::get('/orders/{order}/pay-gcash', [PaymentController::class, 'showGcashCheckout'])->name('orders.gcash.checkout');
    Route::get('/orders/{order}/payment-status', [PaymentController::class, 'status'])->name('orders.payment.status');
    Route::post('/orders/{order}/pay-cod', [PaymentController::class, 'selectCod'])->name('orders.cod.select');

    // Orders
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::resource('orders', OrderController::class);
});

require __DIR__.'/auth.php';