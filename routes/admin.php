<?php

use App\Http\Controllers\Admin\BrandAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CouponAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ReviewAdminController;
use App\Http\Controllers\Admin\ShippingAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/',                          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics/sales',           [DashboardController::class, 'salesData'])->name('analytics.sales');
    Route::get('/analytics/export',          [DashboardController::class, 'exportOrders'])->name('analytics.export');

    // Products
    Route::resource('products', ProductAdminController::class);
    Route::patch('products/{product}/toggle',         [ProductAdminController::class, 'toggle'])->name('products.toggle');
    Route::post('products/{product}/images',          [ProductAdminController::class, 'uploadImages'])->name('products.images.upload');
    Route::delete('products/images/{image}',          [ProductAdminController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('products/{product}/variants',        [ProductAdminController::class, 'storeVariant'])->name('products.variants.store');
    Route::delete('products/variants/{variant}',      [ProductAdminController::class, 'deleteVariant'])->name('products.variants.delete');

    // Categories & Brands
    Route::resource('categories', CategoryAdminController::class);
    Route::resource('brands',     BrandAdminController::class);

    // Orders
    Route::resource('orders', OrderAdminController::class)->only(['index', 'show', 'update']);
    Route::patch('orders/{order}/status',   [OrderAdminController::class, 'updateStatus'])->name('orders.status');
    Route::patch('orders/{order}/tracking', [OrderAdminController::class, 'updateTracking'])->name('orders.tracking');
    // Invoice PDF
    Route::get('orders/{order}/invoice',         [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/preview',  [\App\Http\Controllers\Admin\InvoiceController::class, 'preview'])->name('orders.invoice.preview');

    // Users
    Route::resource('users', UserAdminController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::patch('users/{user}/toggle',     [UserAdminController::class, 'toggle'])->name('users.toggle');

    // Coupons
    Route::resource('coupons', CouponAdminController::class);

    // Shipping zones
    Route::resource('shipping', ShippingAdminController::class)->parameters(['shipping' => 'shipping']);

    // Reviews
    Route::get('reviews',                   [ReviewAdminController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve',[ReviewAdminController::class, 'approve'])->name('reviews.approve');
    Route::patch('reviews/{review}/reject', [ReviewAdminController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}',       [ReviewAdminController::class, 'destroy'])->name('reviews.destroy');
});
