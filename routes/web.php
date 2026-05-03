<?php

use App\Http\Controllers\CartController;
use App\Livewire\ProductHome;
use App\Livewire\ProductDetail;
use App\Livewire\Checkout;
use App\Livewire\OrderSuccess;
use App\Livewire\PaymentSandbox;
use App\Livewire\ProfilePage;
use App\Http\Controllers\VNPayController;
use Illuminate\Support\Facades\Route;

Route::get('/', ProductHome::class)->name('home');
Route::get('/product/{slug}', ProductDetail::class)->name('product.detail');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::middleware('auth')->group(function () {
    Route::get('/profile', ProfilePage::class)->name('profile');          // ← Livewire ProfilePage
    Route::get('/checkout', Checkout::class)->name('checkout');
    Route::get('/order/{invoiceNumber}', OrderSuccess::class)->name('order.success');
    Route::get('/order/failed/{invoiceNumber}', App\Livewire\OrderFailed::class)->name('order.failed');
    Route::get('/payment/sandbox/{invoiceNumber}', PaymentSandbox::class)->name('payment.sandbox');
    Route::get('/payment/sandbox/{invoiceNumber}/callback', [PaymentSandbox::class, 'callback'])
         ->name('payment.sandbox.callback');
});

// VNPay
Route::get('/payment/vnpay/return', [VNPayController::class, 'return'])->name('payment.vnpay.return');
Route::post('/payment/vnpay/ipn', [VNPayController::class, 'ipn'])->name('payment.vnpay.ipn');

Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';