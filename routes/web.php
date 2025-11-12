<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\UserController as CustomerController;

Auth::routes();

// Frontend routes
Route::name('frontend.')->group(function () {
    // Route::get('/', [HomeController::class, 'index'])->name('home');
    // Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
    // Route::get('/product/{slug}', [HomeController::class, 'productDetail'])->name('product.detail');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        // Route::get('/dashboard', [CustomerController::class, 'index'])->name('dashboard');
        // Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
        // Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    });

// Admin routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Users
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class);

        // Categories
        Route::post('categories/import', [CategoryController::class, 'import'])->name('categories.import');
        Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');
        Route::resource('categories', CategoryController::class);

        // Brands
        Route::post('brands/import', [BrandController::class, 'import'])->name('brands.import');
        Route::get('brands/export', [BrandController::class, 'export'])->name('brands.export');
        Route::resource('brands', BrandController::class);

        // Products
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::get('products/image/{product}', [ProductController::class, 'showImage'])->name('products.image');
        Route::resource('products', ProductController::class);

        // Tags
        Route::post('tags/import', [TagController::class, 'import'])->name('tags.import');
        Route::get('tags/export', [TagController::class, 'export'])->name('tags.export');
        Route::resource('tags', TagController::class)->except(['show']);
    });

// Staff routes
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        // Route::get('/', [StaffDashboard::class, 'index'])->name('dashboard');
        // Add staff-specific business routes here later
    });

Route::get('/home', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'customer' => redirect()->route('user.dashboard'),
            default => redirect()->route('frontend.home'),
        };
    }
    return redirect()->route('frontend.home');
})->name('home');
