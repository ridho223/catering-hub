<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPasswordController;
use App\Http\Controllers\CustomerVerificationController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
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
    ->middleware('throttle:8,1')
    ->name('customer.login.submit');
Route::get('/customer/register', [CustomerAuthController::class, 'register'])
    ->name('customer.register');
Route::post('/customer/register', [CustomerAuthController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('customer.register.submit');
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
    ->name('customer.logout');

// Customer: lupa & reset password
Route::get('/customer/forgot-password', [CustomerPasswordController::class, 'request'])
    ->name('customer.password.request');
Route::post('/customer/forgot-password', [CustomerPasswordController::class, 'email'])
    ->middleware('throttle:5,1')
    ->name('customer.password.email');
Route::get('/customer/reset-password/{token}', [CustomerPasswordController::class, 'reset'])
    ->name('customer.password.reset');
Route::post('/customer/reset-password', [CustomerPasswordController::class, 'update'])
    ->middleware('throttle:5,1')
    ->name('customer.password.update');

// Customer: verifikasi email
Route::middleware('auth:customer')->group(function () {
    Route::get('/customer/email/verify', [CustomerVerificationController::class, 'notice'])
        ->name('customer.verification.notice');
    Route::post('/customer/email/verification-notification', [CustomerVerificationController::class, 'send'])
        ->middleware('throttle:3,1')
        ->name('customer.verification.send');
});
Route::get('/customer/email/verify/{id}/{hash}', [CustomerVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('customer.verification.verify');

Route::get('/customer/register/check-email', [CustomerAuthController::class, 'checkEmail'])
    ->name('customer.register.check');
Route::post('/customer/register/resend', [CustomerAuthController::class, 'resend'])
    ->middleware('throttle:3,1')->name('customer.register.resend');
Route::get('/customer/register/confirm/{token}', [CustomerAuthController::class, 'confirm'])
    ->middleware(['signed', 'throttle:10,1'])->name('customer.register.confirm');

Route::get('/customer/auth/google', [CustomerAuthController::class, 'googleRedirect'])
    ->middleware('throttle:10,1')->name('customer.google.redirect');
Route::get('/customer/auth/google/callback', [CustomerAuthController::class, 'googleCallback'])
    ->middleware('throttle:10,1')->name('customer.google.callback');

// Public

Route::get('/', [HomeController::class,'index'] )->name('home');
Route::get('/about', [HomeController::class,'about'])->name('about');
Route::get('/contact', [HomeController::class,'contact'])->name('contact');
Route::get('/paket',[PackageController::class,'customerIndex'])->name('customer.packages.index');
Route::get('/paket/{package}',[PackageController::class,'customerShow'])->name('customer.packages.show');
Route::get('/prasmanan',[PackageController::class,'customerPrasmananIndex'])->name('customer.prasmanan.index');
Route::get('/prasmanan/{package}',[PackageController::class,'customerPrasmananShow'])->name('customer.prasmanan.show');

// Cart

Route::get('cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add/{package}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Customer Sudah Login

// Checkout & bayar: wajib email terverifikasi (bisa dimatikan lewat REQUIRE_EMAIL_VERIFICATION=false)
Route::middleware(['auth:customer', 'customer.verified'])->group(function(){
    Route::get('/checkout',[CheckoutController::class,'index'])->name('checkout.index');
    Route::post('/checkout',[CheckoutController::class,'store'])->name('checkout.store');
    Route::get('/pesanan-saya/{order}/pembayaran', [PaymentController::class,'create'])->name('customer.payment.create');
    Route::post('/pesanan-saya/{order}/pembayaran', [PaymentController::class,'store'])->name('customer.payment.store');
});

Route::middleware('auth:customer')->group(function(){
    Route::get('/checkout/success/{order}',[CheckoutController::class,'success'])->name('checkout.success');
    Route::get('/pesanan-saya', [CustomerOrderController::class,'index'])->name('customer.orders.index');
    Route::post('/pesanan-saya/{order}/batal', [CustomerOrderController::class,'cancel'])->name('customer.orders.cancel');
    Route::get('/pesanan-saya/{order}', [CustomerOrderController::class,'show'])->name('customer.orders.show');
    Route::get('/pesanan-saya/{order}/struk', [CustomerOrderController::class, 'receipt'])->name('customer.orders.receipt'); 

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/profile', [CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
    Route::put('/customer/profile/password', [CustomerAuthController::class, 'updatePassword'])->name('customer.profile.password');

});
 

// Admin

Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->middleware('throttle:6,1')->name('login.process');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->group(function(){

    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::get('/laporan', [DashboardController::class, 'report'])->name('reports.index');
    Route::get('/laporan/unduh', [DashboardController::class, 'exportReport'])->name('reports.export');

    Route::get('/profile', [AuthController::class,'profile'])->name('admin.profile');
    Route::get('/profile/edit',[AuthController::class,'editProfile'])->name('admin.profile.edit');
    Route::put('/profile',[AuthController::class,'UpdateProfile'])->name('admin.profile.update');
    Route::get('/profile/password',[AuthController::class,'editPassword'])->name('admin.password.edit');
    Route::put('/profile/password',[AuthController::class,'updatePassword'])->name('admin.password.update');
        
    Route::get('/payments', [AdminPaymentController::class,'index'])->name('admin.payments.index');
    Route::get('/payments/{id}', [AdminPaymentController::class,'show'])->name('admin.payments.show');
    Route::get('/payments/{id}/bukti', [AdminPaymentController::class,'proof'])->name('admin.payments.proof');
    Route::post('/payments/{id}/verify', [AdminPaymentController::class,'verify'])->name('admin.payments.verify');
    Route::post('/payments/{id}/reject', [AdminPaymentController::class,'reject'])->name('admin.payments.reject');


    // 'show' dihapus: method-nya kosong (halaman putih) dan tidak dipakai di UI.
    Route::resource('categories', CategoryController::class )->except('show');
    Route::resource('menu', MenuController::class )->except('show');
    Route::resource('packages', PackageController::class);
    Route::resource('customers',CustomerController::class);
    Route::resource('orders',OrderController::class);   
    
    Route::patch(
        '/orders/{order}/shipping',
        [OrderController::class, 'updateShipping']
    )->name('orders.shipping.update');
    Route::post('/orders/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/tunai', [AdminPaymentController::class, 'storeCash'])->name('admin.orders.cash');

    Route::get('/notifikasi/poll', [AdminNotificationController::class, 'poll'])->name('admin.notifications.poll');
    Route::get('/notifikasi/{id}/buka', [AdminNotificationController::class, 'open'])->name('admin.notifications.open');
    Route::post('/notifikasi/baca-semua', [AdminNotificationController::class, 'readAll'])->name('admin.notifications.read-all');

    Route::middleware(['auth', 'superadmin'])->group(function(){
        Route::resource('users', UserController::class)->except('show');
    });

});