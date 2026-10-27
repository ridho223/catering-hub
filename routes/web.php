<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Customer Auth

Route::get('/customer/login', [CustomerAuthController::class, 'login'])
    ->name('customer.login');
Route::post('/customer/login', [CustomerAuthController::class, 'authenticate'])
    ->name('customer.login.submit');
Route::get('/customer/register', [CustomerAuthController::class, 'register'])
    ->name('customer.register');
Route::post('/customer/register', [CustomerAuthController::class, 'store'])
    ->name('customer.register.submit');
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
    ->name('customer.logout');

// Public

Route::get('/', [HomeController::class,'index'] )->name('home');
Route::get('/about', [HomeController::class,'about'])->name('about');
Route::get('/contact', [HomeController::class,'contact'])->name('contact');
Route::get('/paket',[PackageController::class,'customerIndex'])->name('customer.packages.index');
Route::get('/paket/{package}',[PackageController::class,'customerShow'])->name('customer.packages.show');

// Cart

Route::get('cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add/{package}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Customer Sudah Login

Route::middleware('auth:customer')->group(function(){
    Route::get('/checkout',[CheckoutController::class,'index'])->name('checkout.index');
    Route::post('/checkout',[CheckoutController::class,'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}',[CheckoutController::class,'success'])->name('checkout.success');
    Route::get('/pesanan-saya', [CustomerOrderController::class,'index'])->name('customer.orders.index');
    Route::get('/pesanan-saya/{order}/pembayaran', [PaymentController::class,'create'])->name('customer.payment.create');
    Route::post('/pesanan-saya/{order}/pembayaran', [PaymentController::class,'store'])->name('customer.payment.store');
    Route::get('/pesanan-saya/{order}', [CustomerOrderController::class,'show'])->name('customer.orders.show');

});
 

// Admin

Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('login.process');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->group(function(){

    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::get('/profile', [AuthController::class,'profile'])->name('admin.profile');
    Route::get('/profile/edit',[AuthController::class,'editProfile'])->name('admin.profile.edit');
    Route::put('/profile',[AuthController::class,'UpdateProfile'])->name('admin.profile.update');
    Route::get('/profile/password',[AuthController::class,'editPassword'])->name('admin.password.edit');
    Route::put('/profile/password',[AuthController::class,'updatePassword'])->name('admin.password.update');
        
    Route::get('/payments', [AdminPaymentController::class,'index'])->name('admin.payments.index');
    Route::get('/payments/{id}', [AdminPaymentController::class,'show'])->name('admin.payments.show');
    Route::post('/payments/{id}/verify', [AdminPaymentController::class,'verify'])->name('admin.payments.verify');
    Route::post('/payments/{id}/reject', [AdminPaymentController::class,'reject'])->name('admin.payments.reject');


    Route::resource('categories', CategoryController::class );
    Route::resource('menu', MenuController::class );
    Route::resource('packages', PackageController::class);
    Route::resource('customers',CustomerController::class);
    Route::resource('orders',OrderController::class);    

    Route::patch(
        '/orders/{order}/shipping',
        [OrderController::class, 'updateShipping']
    )->name('orders.shipping.update');

    Route::middleware(['auth', 'superadmin'])->group(function(){
        Route::resource('users', UserController::class);
    });

});




