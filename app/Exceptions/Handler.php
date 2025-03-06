<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($e);
            }
        });
    }

    /**
     * Handle API exceptions and return a consistent JSON response.
     */
    private function handleApiException(Throwable $exception): JsonResponse
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated. Please login to continue.',
                'status' => 'error',
            ], 401);
        }
        
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'You do not have permission to perform this action.',
                'status' => 'error',
            ], 403);
        }
        
        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            
            return response()->json([
                'message' => "No {$modelName} found with the specified identifier.",
                'status' => 'error',
            ], 404);
        }
        
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $exception->validator->errors()->getMessages(),
                'status' => 'error',
            ], 422);
        }
        
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'The requested resource was not found.',
                'status' => 'error',
            ], 404);
        }
        
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'message' => 'The specified method is not allowed for this resource.',
                'status' => 'error',
            ], 405);
        }
        
        if ($exception instanceof HttpException) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'A server error occurred.',
                'status' => 'error',
            ], $exception->getStatusCode());
        }
        
        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1] ?? null;
            
            if ($errorCode == 1062) {  // Duplicate entry
                return response()->json([
                    'message' => 'The record already exists.',
                    'status' => 'error',
                ], 409);
            }
            
            if ($errorCode == 1451) {  // Cannot delete or update a parent row (foreign key constraint)
                return response()->json([
                    'message' => 'This record cannot be deleted because it is referenced by other records.',
                    'status' => 'error',
                ], 409);
            }
            
            return response()->json([
                'message' => 'Database error occurred.',
                'status' => 'error',
            ], 500);
        }
        
        // Default handling for any other exceptions
        return response()->json([
            'message' => $exception->getMessage() ?: 'An unexpected server error occurred.',
            'status' => 'error',
        ], 500);
    }
}