<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\LandingCheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Auth\AuthController;

Route::get('/login',[AuthController::class,'login'])->name('login');
Route::get('/token_refresh',[AuthController::class,'create_token'])->name('create-token');
Route::post('/login',[AuthController::class,'authenticate'])->name('login');
Route::post('/logout',[AuthController::class,'destroy'])->name('logout');


Route::prefix('register')
->group(function(){
    Route::get('/',[AuthController::class,'register'])->name('register');
    Route::post('/',[AuthController::class,'store'])->name('register');
    Route::get('/success',[AuthController::class,'success'])->name('register.success');
});

Route::prefix('welcome')
->middleware('auth')
->group(function(){
    Route::get('/',[WelcomeController::class,'index'])->name('welcome');
    Route::get('/store',[WelcomeController::class,'create'])->name('welcome.create');
    Route::post('/store',[WelcomeController::class,'save_store'])->name('welcome.store');
    Route::get('/warehouse',[WelcomeController::class,'warehouse'])->name('welcome.warehouse');
    Route::post('/warehouse',[WelcomeController::class,'save_warehouse'])->name('welcome.warehouse');
});

Route::prefix('/')
->middleware('auth','completed')
->group(function(){
    Route::get('/',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
});

Route::prefix('/product')
->middleware('auth')
->group(function(){
    Route::get('/',[ProductController::class,'index'])->name('product');
    Route::post('/store',[ProductController::class,'store'])->name('product.store');
    Route::post('/update/{id}',[ProductController::class,'update'])->name('product.update');
    Route::get('/create',[ProductController::class,'create'])->name('product.create');
    Route::get('/edit/{id}',[ProductController::class,'edit'])->name('product.edit');
    Route::get('/{id}',[ProductController::class,'show'])->name('product.show');
    Route::delete('destroy/{id}',[ProductController::class,'destroy'])->name('product.delete');
    Route::post('destroy_check',[ProductController::class,'destroy_check'])->name('product.array.delete');

    Route::prefix('/checkout')
    ->group(function(){
        Route::get('/show/{id}',[CheckoutController::class,'create'])->name('product.checkout.create');
        Route::post('/show/{id}',[CheckoutController::class,'store'])->name('product.checkout.store');
        Route::post('/update/{id}',[CheckoutController::class,'update'])->name('product.checkout.update');
        Route::get('/edit/{id}',[CheckoutController::class,'edit'])->name('product.checkout.edit');
    });
});

Route::prefix('/orders')
->middleware('auth')
->group(function(){
    Route::get('/',[OrderController::class,'index'])->name('orders');
    Route::get('/invoice/{id}',[OrderController::class,'invoice'])->name('invoice');
});

Route::prefix('/team')
->middleware('auth')
->group(function(){
    Route::get('/',[TeamController::class,'index'])->name('team');
    Route::get('/create',[TeamController::class,'create'])->name('team.create');
    Route::post('/store',[TeamController::class,'store'])->name('team.store');
    Route::get('/edit/{id}',[TeamController::class,'edit'])->name('team.edit');
    Route::post('/update/{id}',[TeamController::class,'update'])->name('team.update');
    Route::post('/destroy}',[TeamController::class,'destroy'])->name('team.destroy');
});

Route::prefix('/profile')
->middleware('auth')
->group(function(){
    Route::get('/',[ProfileController::class,'index'])->name('profile');
    Route::post('/update',[ProfileController::class,'update'])->name('profile.update');
    Route::post('/change_password',[ProfileController::class,'change_password'])->name('profile.change_password');
});

Route::prefix('/landing')
->group(function(){
    Route::get('/{url}',[LandingCheckoutController::class,'index']);
    Route::post('/{url}',[LandingCheckoutController::class,'store'])->name('create-order');
    Route::get('/{url}/thanks/{id}',[LandingCheckoutController::class,'success'])->name('success');
    
    
    Route::get('/{url}/payment_confirmation/{order_id}',[PaymentController::class,'index']);
    Route::post('/{url}/payment_confirmation/{order_id}',[PaymentController::class,'store'])->name('create-confirmation');
    Route::get('/payment_success/{payment_id}',[PaymentController::class,'success'])->name('payment-success');
    Route::get('/orders/{id}',[OrderController::class,'show'])->name('order-detail');
});
