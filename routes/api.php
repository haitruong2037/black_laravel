<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CommentController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\UserAddressController;
use App\Http\Controllers\User\WishlistController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [AuthController::class, 'resetPasswordStore']);
    Route::post('/verify-email', [AuthController::class, 'sendMailVerify']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyMail']);

    Route::middleware(['auth.jwt'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/change-pass', [AuthController::class, 'changePassWord']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
    });
});

Route::middleware(['auth.jwt'])->group(function () {
    Route::prefix('profile')->group(function ($router) {
        Route::get('/', [ProfileController::class, 'viewProfile']);
        Route::post('/', [ProfileController::class, 'updateProfile']);

        Route::prefix('addresses')->group(function ($router) {
            Route::get('/', [UserAddressController::class, 'index']);
            Route::post('/', [UserAddressController::class, 'store']);
            Route::get('/{id}', [UserAddressController::class, 'show']);
            Route::post('/{id}', [UserAddressController::class, 'update']);
            Route::delete('/{id}', [UserAddressController::class, 'delete']);
        });
    });

    Route::prefix('carts')->group(function ($router) {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'storeOrUpdate']);
        Route::post('/{id}', [CartController::class, 'update']);
        Route::delete('/{id}', [CartController::class, 'delele']);
    });

    Route::prefix('orders')->group(function ($router) {
        Route::post('/store', [OrderController::class, 'store']);
        Route::get('/history', [OrderController::class, 'orderHistory']);
        Route::post('/cancel', [OrderController::class, 'cancelOrder']);
    });
    
    Route::group(['prefix' => 'wishlist'], function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/store', [WishlistController::class, 'store']);
        Route::delete('/{product_id}/destroy', [WishlistController::class, 'destroy']);
    });

    Route::get('/', function () {
        echo 'Welcome to TechStore!';
    });
});

Route::get('/index', [HomeController::class, 'index']);

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
});

Route::prefix('products')->group(function ($router) {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/news', [ProductController::class, 'newProducts']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/{id}/related', [ProductController::class, 'relatedProducts']);
    Route::get('/{id}/comments', [CommentController::class, 'show']);
    Route::post('/{id}/comments', [CommentController::class, 'store'])->middleware('auth.jwt');
    Route::post('/{id}/comments/{commentId}/reply', [CommentController::class, 'reply'])->middleware('auth.jwt');

});
