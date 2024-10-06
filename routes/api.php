<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductC;
use App\Http\Controllers\AddressC;
use App\Http\Controllers\ReviewC;
use App\Http\Controllers\CouponC;
use App\Http\Controllers\AuthC;
use App\Http\Controllers\UserC;
use App\Http\Controllers\CartC;

// Public routes
Route::post('/login', [AuthC::class, 'login']);
Route::post('/register', [UserC::class, 'store']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::apiResource('users', UserC::class)->except(['store','update']);
    Route::post('/users/{id}', [UserC::class, 'update']);

    // Product routes
    Route::apiResource('products', ProductC::class)->except('update');
    Route::post('/products/{id}', [ProductC::class, 'update']);

    // Review routes
    Route::apiResource('reviews', ReviewC::class)->except(['update']);
    Route::post('/products/{productId}/reviews/{id}', [ReviewC::class, 'update']);

    // Coupon routes
    Route::apiResource('coupons', CouponC::class)->except(['update']);
    Route::post('/coupons/{id}', [CouponC::class, 'update']);

    // Cart routes
    Route::get('/cart', [CartC::class, 'index']);
    Route::post('/cart/add/{id}', [CartC::class, 'add']);
    Route::post('/cart/remove/{productId}', [CartC::class, 'remove']);
    Route::post('/cart/apply-coupon', [CartC::class, 'applyCoupon']);
    

    // Address routes
    Route::apiResource('addresses', AddressC::class)->except('update');
    Route::post('/addresses/{id}', [AddressC::class, 'update']);

    // Logout route
    Route::post('/logout', [AuthC::class, 'logout']);
});

