<?php

use App\Http\Controllers\v1\AdminController;
use App\Http\Controllers\v1\AgentController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\TransactionController;
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
    Route::get('/refresh', [AuthController::class, 'refresh']);
});

Route::middleware('auth:api')->group(function(){
    Route::prefix('v1')->group(function(){
        /**
         * Admin endpoints
         */
        Route::post('admins/', [AdminController::class, 'store']);
        Route::get('admins/', [AdminController::class, 'index']);
        Route::get('admins/{admin}', [AdminController::class, 'show']);
        Route::get('admins/deactivation/{admin}', [AdminController::class, 'deactivate']);
        Route::put('admins/{admin}', [AdminController::class, 'update']);

        /**
         * Agent endpoints
         */
        Route::post('agents/', [AgentController::class, 'store']);
        Route::get('agents/', [AgentController::class, 'index']);
        Route::get('agents/{agent}', [AgentController::class, 'show']);
        Route::get('agents/deactivation/{agent}', [AgentController::class, 'deactivate']);
        Route::put('agents/{agent}', [AgentController::class, 'update']);

        /**
         * Dashboard endpoints
         */
        Route::post('transactions', [TransactionController::class, 'store']);
        Route::get('transactions', [TransactionController::class, 'index']);
        Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
        Route::get('transactions/by-user/{user}', [TransactionController::class, 'indexByUser']);
    });
});
