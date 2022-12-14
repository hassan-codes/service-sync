<?php

use App\Http\Controllers\v1\AdminController;
use App\Http\Controllers\v1\AgentController;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\NewPasswordController;
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
Route::group(['prefix' => 'v1/auth'], function(){
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword']);
    Route::post('/reset-password', [NewPasswordController::class, 'resetPassword'])->name('password.reset');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'
], function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [AuthController::class, 'updatePassword']);
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
        Route::get('transactions/by-date/date-from/{date_from}/date-to/{date_to}', [TransactionController::class, 'indexByDateRange']);
    });
});
