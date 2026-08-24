<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\WishlistController;
use Illuminate\Support\Facades\Route;

// ── Public ───────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/brand/{brand}', [ProductController::class, 'byBrand'])->name('products.brand');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// ── Cart (works guest + logged-in) ───────────────────────────
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',              [CartController::class, 'index'])->name('index');
    Route::post('/add',          [CartController::class, 'add'])->name('add');
    Route::get('/count',         [CartController::class, 'count'])->name('count');
    Route::patch('/{cartId}',    [CartController::class, 'update'])->name('update');
    Route::delete('/{cartId}',   [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon',     [CartController::class, 'removeCoupon'])->name('coupon.remove');
    Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buynow');
});

// ── Guest-only auth ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',            [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login',           [LoginController::class, 'login']);
    Route::get('/register',         [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',        [RegisterController::class, 'register']);
    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated customer ───────────────────────────────────
Route::middleware('auth')->group(function () {

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/',                  [CheckoutController::class, 'index'])->name('index');
        Route::post('/place',            [CheckoutController::class, 'placeOrder'])->name('place');
        Route::get('/success/{order}',   [CheckoutController::class, 'success'])->name('success');
        Route::post('/razorpay/callback',[CheckoutController::class, 'razorpayCallback'])->name('razorpay.callback');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                      [OrderController::class, 'index'])->name('index');
        Route::get('/{order}',               [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/track',         [OrderController::class, 'track'])->name('track');
        Route::post('/{order}/cancel',       [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/refund',       [OrderController::class, 'claimRefund'])->name('refund');
        Route::post('/{order}/review/{item}',[ReviewController::class, 'store'])->name('review');
    });

    // Profile & addresses
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',                             [ProfileController::class, 'index'])->name('index');
        Route::patch('/',                           [ProfileController::class, 'update'])->name('update');
        Route::patch('/password',                   [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/addresses',                    [ProfileController::class, 'addresses'])->name('addresses');
        Route::post('/addresses',                   [ProfileController::class, 'storeAddress'])->name('addresses.store');
        Route::patch('/addresses/{address}',        [ProfileController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}',       [ProfileController::class, 'deleteAddress'])->name('addresses.delete');
        Route::patch('/addresses/{address}/default',[ProfileController::class, 'setDefaultAddress'])->name('addresses.default');
    });

    // Wishlist
    Route::get('/wishlist',                    [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}',  [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Load admin routes
require __DIR__.'/admin.php';
