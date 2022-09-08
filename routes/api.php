<?php

use App\Http\Controllers\v1\AdminController;
use App\Http\Controllers\v1\AuthController;
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


Route::post('v1/auth/login', [AuthController::class, 'login']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
], function () {
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->group(function(){
    Route::prefix('v1')->group(function(){
        Route::post('administrators/', [AdminController::class, 'store']);
    });
});
