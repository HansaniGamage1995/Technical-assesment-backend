<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('orders', OrderController::class);
Route::middleware('auth:api')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
});
Route::apiResource('products', ProductController::class)->only([
    'index', 'store', 'update', 'destroy', 'edit'
]);
