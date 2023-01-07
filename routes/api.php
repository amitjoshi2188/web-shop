<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// V1  related APIs routes declarations.
Route::prefix('v1')->group(function () {
    Route::post('register', [PassportAuthController::class, 'register']);
    Route::post('login', [PassportAuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('get-user', [PassportAuthController::class, 'userInfo']);

        // Orders related routes declarations.
        Route::prefix('orders')->group(function () {
            Route::get('listing', [OrderController::class, 'index']);
            Route::post('add', [OrderController::class, 'store']);
            Route::get('detail/{id?}', [OrderController::class, 'show']);
            Route::post('add-products', [OrderController::class, 'update']);
            Route::delete('{id}', [OrderController::class, 'destroy']);
            Route::get('total-bill/{orderId?}', [OrderController::class, 'calculateTotalCartValue']);
            Route::post('pay-order', [OrderController::class, 'payOrder']);
        });

        // Customers related routes declarations.
        Route::prefix('customers')->group(function () {
            Route::get('listing', [CustomerController::class, 'index']);
        });

        // Products related routes declarations.
        Route::prefix('products')->group(function () {
            Route::get('listing', [ProductController::class, 'index']);
        });
    });
});






