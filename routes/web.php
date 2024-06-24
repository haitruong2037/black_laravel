<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('admin')->name('admin.')->group(function ($router) {
    Route::middleware('guest:admin')->group(function ($router) {
        Route::get('/login', [AuthController::class, 'showLoginView'])->name('login_view');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::get('/forgot-password', [AuthController::class, 'forgotPasswordCreate'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}/{email}', [AuthController::class, 'resetPasswordCreate'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPasswordStore'])->name('password.update');
    });
    
    Route::middleware('auth.admin')->group(function ($router) {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::prefix('profile')->name('profile.')->group(function ($router) {
            Route::get('/update', [ProfileController::class, 'viewProfile'])->name('view');
            Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
            Route::get('/update-password', [ProfileController::class, 'updatePasswordCreate'])->name('password.request');
            Route::post('/update-password', [ProfileController::class, 'updatePasswordStore'])->name('password.update');
        });

        Route::prefix('manager_admin')->name('manager_admin.')->group(function ($router) {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/create', [AdminController::class, 'create'])->name('create');
            Route::post('/store', [AdminController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'show'])->name('show');
            Route::post('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
            Route::delete('/{id}/destroy', [AdminController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function ($router) {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('{id}/edit', [CategoryController::class, 'show'])->name('show');
            Route::post('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::delete('{id}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('products')->name('products.')->group(function ($router) {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/store', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ProductController::class, 'show'])->name('show');
            Route::post('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::delete('/{id}/destroy', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/image/{imageId}/edit/', [ProductController::class, 'setMainImage'])->name('setMainImage');
            Route::delete('/{id}/image/{imageId}/destroy/', [ProductController::class, 'destroyImage'])->name('destroyImage');
        });

        Route::prefix('orders')->name('orders.')->group(function ($router) {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{id}/edit', [OrderController::class, 'show'])->name('show');
            Route::post('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::get('/{id}/detail', [OrderController::class, 'detailOrder'])->name('detail');
            Route::delete('/{id}/delete', [OrderController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('users')->name('users.')->group(function ($router) {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('{id}/edit', [UserController::class, 'show'])->name('show');
            Route::post('{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::get('{id}/detail', [UserController::class, 'userDetail'])->name('detail');
            Route::delete('{id}/delete', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('comments')->name('comments.')->group(function ($router) {
            Route::get('/', [CommentController::class, 'index'])->name('index');
            Route::delete('{id}/delete', [CommentController::class, 'destroy'])->name('destroy');

        });
        Route::prefix('sliders')->name('sliders.')->group(function ($router) {
            Route::get('/', [SliderController::class, 'index'])->name('index');
            Route::get('/create', [SliderController::class, 'create'])->name('create');
            Route::get('store', [SliderController::class, 'store'])->name('store');
            Route::get('update', [SliderController::class, 'update'])->name('update');
            Route::get('{id}/edit', [SliderController::class, 'show'])->name('show');
            Route::get('{id}/edit', [SliderController::class, 'edit'])->name('edit');
            Route::get('{id}/delete', [SliderController::class, 'delete'])->name('delete');
        });
        
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });
});
