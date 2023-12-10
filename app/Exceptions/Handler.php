<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e) {
            if (request()->is('api/*')) {

                if (config('app.debug')) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'trace' => $e->getTrace(),
                    ], 500);
                }
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'message' => 'Resource not found.',
                    ], 404);
                }

                if ($e instanceof AuthorizationException) {
                    return response()->json([
                        'message' => 'This action is unauthorized.',
                    ], 403);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'message' => $e->getMessage(),
                    ], 401);
                }

                return response()->json([
                    'message' => 'Internal server error.',
                ], 500);
            }
        });
    }
}
