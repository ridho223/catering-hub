<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'] );
Route::get('/about', [HomeController::class,'about'] );
Route::get('/contact', [HomeController::class,'contact'] );

Route::resource('categories', CategoryController::class );
