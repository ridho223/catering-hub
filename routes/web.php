<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('login.process');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

// Pembeli
Route::get('/', [HomeController::class,'index'] )->name('home');
Route::get('/about', [HomeController::class,'about'] );
Route::get('/contact', [HomeController::class,'contact'] );

Route::get('cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    

// Admin


Route::middleware('auth')->prefix('admin')->group(function(){

    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::get('/profile', [AuthController::class,'profile'])->name('admin.profile');
    Route::get('/profile/edit',[AuthController::class,'editProfile'])->name('admin.profile.edit');
    Route::put('/profile',[AuthController::class,'UpdateProfile'])->name('admin.profile.update');
    Route::get('/profile/password',[AuthController::class,'editPassword'])->name('admin.password.edit');
    Route::put('/profile/password',[AuthController::class,'updatePassword'])->name('admin.password.update');

    Route::resource('categories', CategoryController::class );
    Route::resource('menu', MenuController::class );
    Route::resource('customers',CustomerController::class);
    Route::resource('orders',OrderController::class);

    Route::middleware(['auth', 'superadmin'])->group(function(){
        Route::resource('users', UserController::class);
    });

});




