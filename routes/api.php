<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\RestaurantApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


$frontEndUrlBase = config('app.frontend_url_base');
Route::domain("{subdomain}.$frontEndUrlBase")->middleware('validateSubdomain')->group(function () {
    // Route::get('/client', ClientApiController::class); // Deprecated/Removed

    Route::get('/restaurant', RestaurantApiController::class);
    Route::get('/categories', CategoryApiController::class);
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{productId}', [ProductApiController::class, 'show']);

    Route::post('/orders', [OrderApiController::class, 'store'])
        ->middleware('throttle:orders');
});
