<?php

use Illuminate\Http\Request;
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

Route::get('/user_list', [\App\Http\Controllers\AuthController::class, 'userList']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/check-token', function () {
    return response()->json(['message' => 'Token is valid', 'code' => 200]);
})->middleware('auth:sanctum');
//Route::post('/create_store', [\App\Http\Controllers\StoreController::class, 'create']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create_store', [\App\Http\Controllers\StoreController::class, 'create']);
    Route::get('/get_store/{id}', [\App\Http\Controllers\StoreController::class, 'getList']);
    Route::get('/get_store_detail/{id}', [\App\Http\Controllers\StoreController::class, 'show']);
    Route::put('/delete_store/{id}', [\App\Http\Controllers\StoreController::class, 'delete']);
    Route::post('/edit_store/{id}', [\App\Http\Controllers\StoreController::class, 'edit']);
    Route::get('/get_store_all/{id}', [\App\Http\Controllers\StoreController::class, 'getListAll']);

    Route::post('/create_product', [\App\Http\Controllers\ProductController::class, 'create']);
    Route::get('/get_product/{id}', [\App\Http\Controllers\ProductController::class, 'getList']);
    Route::get('/get_product_by_store_id/{id}', [\App\Http\Controllers\ProductController::class, 'getListByStoreId']);
    Route::get('/get_product_detail/{id}', [\App\Http\Controllers\ProductController::class, 'show']);
    Route::put('/delete_product/{id}', [\App\Http\Controllers\ProductController::class, 'delete']);
    Route::post('/edit_product/{id}', [\App\Http\Controllers\ProductController::class, 'edit']);
});

