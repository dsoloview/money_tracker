<?php

use App\Http\Controllers\Api\v1\Account\AccountController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Category\CategoryController;
use App\Http\Controllers\Api\v1\Currency\CurrencyController;
use App\Http\Controllers\Api\v1\Language\LanguageController;
use App\Http\Controllers\Api\v1\Role\RoleController;
use App\Http\Controllers\Api\v1\Transaction\TransactionController;
use App\Http\Controllers\Api\v1\Transfer\TransferController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\User\UserTransactionController;
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

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/settings', [UserController::class, 'updateSettings'])->name('users.update_settings');
    Route::patch('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update_password');
    Route::get('users/{user}/transactions', [UserTransactionController::class, 'index'])->name('users.transactions.index');
    Route::get('users/{user}/transactions/min_max', [UserTransactionController::class, 'minMax'])->name('users.transactions.min_max');
    Route::apiResource('users.categories', CategoryController::class)->shallow();
    Route::apiResource('users.accounts', AccountController::class)->shallow();
    Route::get('users/{user}/accounts/balance', [AccountController::class, 'balance'])->name('users.accounts.balance');
    Route::apiResource('accounts.transfers', TransferController::class)->shallow();
    Route::apiResource('accounts.transactions', TransactionController::class)->shallow();
    Route::prefix('users/{user}')->group(function () {
        Route::get('default_categories', [CategoryController::class, 'default'])->name('users.default_categories');
    });

    Route::apiResource('roles', RoleController::class);
});

Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);
