<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\RajaongkirController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderpaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('/')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/bank', [BankController::class, 'index']);
        Route::post('/bank', [BankController::class, 'store']);
        Route::delete('/bank/{id}', [BankController::class, 'destory']);

        Route::prefix('/products')
            ->group(function () {
                // Rute dengan middleware 'auth:sanctum'
                Route::post('/', [ProductController::class, 'store']);
                Route::post('/check_url', [ProductController::class, 'check_url']);
                Route::delete('destroy/{id}', [ProductController::class, 'destroy'])->name('product.delete');

                // Rute tanpa middleware 'auth:sanctum'
                Route::post('/upload', [ProductController::class, 'upload'])->withoutMiddleware('auth:sanctum');
            });

        Route::prefix('/collections')
            ->group(function () {
                Route::get('/', [CollectionController::class, 'index']);
            });

        Route::prefix('/order')
            ->group(function () {
                Route::get('/list', [OrderController::class, 'list']);
                Route::post('/change_order_status', [OrderController::class, 'change_order_status']);
                Route::post('/change_payment_status', [OrderController::class, 'change_payment_status']);
            });
        Route::prefix('/payment')
            ->group(function () {
                Route::post('/change_payment_status', [OrderpaymentController::class, 'change_payment_status']);
                Route::get('/list', [OrderpaymentController::class, 'list']);
                Route::post('/destroy', [OrderpaymentController::class, 'destroy']);
            });
    });
Route::prefix('/auth')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
    });

Route::prefix('/dashboard')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/statistic', [DashboardController::class, 'statistic']);
    });

Route::group(['prefix' => '/rajaongkir'], function () {
    Route::get('province', [RajaongkirController::class, 'province']);
    Route::get('city', [RajaongkirController::class, 'city']);
    Route::get('autocomplete', [RajaongkirController::class, 'autocomplete']);
    Route::get('kecamatan', [RajaongkirController::class, 'kecamatan']);
    Route::get('expedisi', [RajaongkirController::class, 'expedisi']);
});

Route::prefix('/inventory')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/list/{id}', [InventoryController::class, 'list']);
        Route::post('/add', [InventoryController::class, 'add']);
        Route::post('/reduce', [InventoryController::class, 'reduce']);
        Route::post('/adjustment', [InventoryController::class, 'adjustment']);
    });
