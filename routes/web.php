<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TagController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::resource('categories', CategoryController::class);

    Route::post('/brands/import', [BrandController::class, 'import'])->name('brands.import');
    Route::get('/brands/export', [BrandController::class, 'export'])->name('brands.export');
    Route::resource('brands', BrandController::class);

    Route::post('products/import', [\App\Http\Controllers\Admin\ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [\App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
    Route::get('products/image/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'showImage'])->name('products.image');

    Route::post('tags/import', [TagController::class, 'import'])->name('tags.import');
    Route::get('tags/export', [TagController::class, 'export'])->name('tags.export');
    Route::resource('tags', TagController::class)->except(['show', 'create', 'edit']);
});
