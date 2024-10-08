<?php

use App\Http\Controllers\Api\v1\Account\AccountController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\Category\CategoryController;
use App\Http\Controllers\Api\v1\Currency\CurrencyController;
use App\Http\Controllers\Api\v1\Icon\IconController;
use App\Http\Controllers\Api\v1\Language\LanguageController;
use App\Http\Controllers\Api\v1\Newsletter\NewsletterChannelsController;
use App\Http\Controllers\Api\v1\Newsletter\NewsletterController;
use App\Http\Controllers\Api\v1\Role\RoleController;
use App\Http\Controllers\Api\v1\Search\SearchController;
use App\Http\Controllers\Api\v1\Transaction\TransactionController;
use App\Http\Controllers\Api\v1\Transfer\TransferController;
use App\Http\Controllers\Api\v1\User\UserController;
use App\Http\Controllers\Api\v1\User\UserNewsletterController;
use App\Http\Controllers\Api\v1\User\UserTelegramController;
use App\Http\Controllers\Api\v1\User\UserTransactionController;
use App\Http\Controllers\Api\v1\User\UserTransferController;
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

    Route::apiResource('users.categories', CategoryController::class)->shallow();
    Route::apiResource('users.accounts', AccountController::class)->shallow();
    Route::apiResource('accounts.transfers', TransferController::class)->shallow();
    Route::apiResource('accounts.transactions', TransactionController::class)->shallow();

    Route::prefix('users/{user}')->group(function () {
        Route::get('default_categories', [CategoryController::class, 'default'])->name('users.default_categories');
        Route::patch('settings', [UserController::class, 'updateSettings'])->name('users.update_settings');
        Route::patch('password', [UserController::class, 'updatePassword'])->name('users.update_password');
        Route::get('categories/tree', [CategoryController::class, 'tree'])->name('users.categories.tree');

        Route::prefix('transactions')->group(function () {
            Route::get('/', [UserTransactionController::class, 'index'])->name('users.transactions.index');
            Route::get('min_max', [UserTransactionController::class, 'minMax'])->name('users.transactions.min_max');
            Route::get('info', [UserTransactionController::class, 'transactionsInfo'])->name('users.transactions.info');
        });

        Route::prefix('accounts')->group(function () {
            Route::get('balance', [AccountController::class, 'balance'])->name('users.accounts.balance');
        });

        Route::prefix('transfers')->group(function () {
            Route::get('/', [UserTransferController::class, 'index'])->name('users.transfers.index');
        });

        Route::prefix('telegram')->group(function () {
            Route::get('', [UserTelegramController::class, 'getTelegramUser'])->name('users.telegram.user');
            Route::get('token', [UserTelegramController::class, 'token'])->name('users.telegram.token');
            Route::post('logout', [UserTelegramController::class, 'logout'])->name('users.telegram.logout');
        });

        Route::get('newsletters', [UserNewsletterController::class, 'index'])->name('users.newsletters.index');
        Route::prefix('newsletters/{userNewsletter}')->group(function () {
            Route::post('subscribe',
                [UserNewsletterController::class, 'subscribe'])->name('users.newsletters.subscribe');
            Route::post('unsubscribe',
                [UserNewsletterController::class, 'unsubscribe'])->name('users.newsletters.unsubscribe');
            Route::get('', [UserNewsletterController::class, 'show'])->name('users.newsletters.show');
            Route::patch('', [UserNewsletterController::class, 'update'])->name('users.newsletters.update');
        });
    });

    Route::apiResource('roles', RoleController::class);

    Route::prefix('newsletters')->group(function () {
        Route::get('', [NewsletterController::class, 'index'])->name('newsletters.index');
        Route::get('{newsletter}', [NewsletterController::class, 'show'])->name('newsletters.show');
    });

    Route::prefix('newsletter_channels')->group(function () {
        Route::get('', [NewsletterChannelsController::class, 'index'])->name('newsletter_channels.index');
        Route::get('show{newsletterChanel}',
            [NewsletterChannelsController::class, 'show'])->name('newsletter_channels.show');
    });

    Route::get('search', [SearchController::class, 'search']);
});

Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);
Route::apiResource('icons', IconController::class)->only(['index', 'show']);
