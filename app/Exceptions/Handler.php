<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    }

    use ApiResponse;

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {  // <- matches all /api/* requests
            if ($exception instanceof ValidationException) {
                return $this->errorResponse($exception->errors(), 422);
            }
    
            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return $this->errorResponse('Resource not found', 404);
            }
    
            if ($exception instanceof AuthenticationException) {
                return $this->errorResponse('Unauthenticated', 401);
            }
    
            return $this->errorResponse($exception->getMessage(), 500);
        }
    
        return parent::render($request, $exception);
    }
    
}
