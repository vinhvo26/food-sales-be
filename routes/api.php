<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

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
Route::prefix('/auth')->group(function () {
    Route::post('/create', [AuthenController::class, 'createUser']);
    Route::post('/edit', [AuthenController::class, 'editUser']);
    Route::get('/delete/{id}', [AuthenController::class, 'deleteUser']);
    Route::post('/login', [AuthenController::class, 'loginUser']);
    Route::post('/forgot-password', [AuthenController::class, 'handleForgotpassword']);
    Route::post('/getAllUser', [AuthenController::class, 'getListUser']);

});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/check', [UserController::class, 'getUser']);
    Route::get('/logout', [AuthenController::class, 'logoutUser']);
});

Route::prefix('/product')->group(function () {
    Route::post('/create', [ProductController::class, 'createProduct']);
    Route::post('/edit', [ProductController::class, 'editProduct']);
    Route::get('/delete/{id}', [ProductController::class, 'deleteProduct']);
    Route::post('/getAllProduct', [ProductController::class, 'getListProduct']);
});
