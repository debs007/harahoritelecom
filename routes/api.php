<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ExchangeController;

// ── Public routes ─────────────────────────────────────────────────────────────
Route::post('/auth/register',         [AuthController::class, 'register']);
Route::post('/auth/login',            [AuthController::class, 'login']);
Route::post('/auth/forgot-password',  [AuthController::class, 'forgotPassword']);

// Home / Discovery
Route::get('/home',     [HomeController::class, 'index']);

// Products - public
Route::get('/products',                         [ProductController::class, 'index']);
Route::get('/products/search',                  [ProductController::class, 'search']);
Route::get('/products/{slug}',                  [ProductController::class, 'show']);
Route::get('/categories',                       [ProductController::class, 'categories']);
Route::get('/categories/{slug}/products',       [ProductController::class, 'byCategory']);
Route::get('/brands',                           [ProductController::class, 'brands']);
Route::get('/brands/{slug}/products',           [ProductController::class, 'byBrand']);
Route::get('/coupons/active',                   [ProductController::class, 'activeCoupons']);

// ── Auth required ─────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout',     [AuthController::class, 'logout']);
    Route::get('/auth/me',          [AuthController::class, 'me']);

    // Profile
    Route::patch('/profile',                            [ProfileController::class, 'update']);
    Route::patch('/profile/password',                   [ProfileController::class, 'updatePassword']);
    Route::get('/profile/addresses',                    [ProfileController::class, 'addresses']);
    Route::post('/profile/addresses',                   [ProfileController::class, 'storeAddress']);
    Route::patch('/profile/addresses/{address}',        [ProfileController::class, 'updateAddress']);
    Route::delete('/profile/addresses/{address}',       [ProfileController::class, 'deleteAddress']);
    Route::patch('/profile/addresses/{address}/default',[ProfileController::class, 'setDefault']);

    // Cart
    Route::get('/cart',             [CartController::class, 'index']);
    Route::post('/cart/add',        [CartController::class, 'add']);
    Route::patch('/cart/{id}',      [CartController::class, 'update']);
    Route::delete('/cart/{id}',     [CartController::class, 'remove']);
    Route::post('/cart/coupon',     [CartController::class, 'applyCoupon']);
    Route::delete('/cart/coupon',   [CartController::class, 'removeCoupon']);
    Route::get('/cart/summary',     [CartController::class, 'summary']);

    // Wishlist
    Route::get('/wishlist',         [WishlistController::class, 'index']);
    Route::post('/wishlist/{slug}', [WishlistController::class, 'toggle']);

    // Orders — IMPORTANT: fixed routes before parameterised ones
    Route::get('/orders',                           [OrderController::class, 'index']);
    Route::post('/orders',                          [OrderController::class, 'store']);
    Route::post('/orders/razorpay/verify',          [OrderController::class, 'verifyPayment']); // ← BEFORE {number}
    Route::get('/shipping-zones',                   [OrderController::class, 'shippingZones']);
    Route::get('/orders/{number}',                  [OrderController::class, 'show']);
    Route::delete('/orders/{number}',               [OrderController::class, 'cancel']);
    Route::post('/orders/{number}/refund',          [OrderController::class, 'claimRefund']);   // ← AFTER fixed routes

    // Reviews
    Route::post('/products/{slug}/reviews',     [ReviewController::class, 'store']);
    Route::get('/reviews/mine',                 [ReviewController::class, 'mine']);

    // Exchange
    Route::get('/exchange/{slug}',              [ExchangeController::class, 'getOffer']);
    Route::post('/exchange/estimate',           [ExchangeController::class, 'estimate']);
    Route::post('/exchange/verify-imei',        [ExchangeController::class, 'verifyImei']);
});
