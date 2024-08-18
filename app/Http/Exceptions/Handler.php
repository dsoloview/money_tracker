<?php

namespace App\Http\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
                if ($e instanceof ValidationException) {
                    $errors = collect($e->errors())->undot();

                    return response()->json([
                        'type' => 'validation_error',
                        'message' => 'The given data was invalid.',
                        'errors' => $errors,
                    ], 422);
                }

                if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    return response()->json([
                        'type' => 'not_found',
                        'message' => 'Resource not found.',
                    ], 404);
                }

                if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                    return response()->json([
                        'type' => 'unauthorized',
                        'message' => 'This action is unauthorized.',
                    ], 403);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'type' => 'unauthenticated',
                        'message' => $e->getMessage(),
                    ], 401);
                }

                if (app()->environment('local', 'testing')) {
                    return response()->json([
                        'type' => 'internal_server_error',
                        'message' => 'Internal server error.',
                        'exception' => $e->getMessage(),
                        'file' => $e->getFile(),
                    ], 500);
                } else {
                    return response()->json([
                        'type' => 'internal_server_error',
                        'message' => 'Internal server error.',
                    ], 500);
                }
            }
        });
    }
}
