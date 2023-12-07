<?php

use App\Http\Controllers\api\v1\Auth\AuthController;
use App\Http\Controllers\api\v1\Currency\CurrencyController;
use App\Http\Controllers\api\v1\Language\LanguageController;
use App\Http\Controllers\api\v1\Role\RoleController;
use App\Http\Controllers\api\v1\User\UserController;
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

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
    Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);
});
