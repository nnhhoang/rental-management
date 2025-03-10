<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Return success response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message ?? trans('messages.success'),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return error response.
     *
     * @param string|null $message
     * @param mixed $errors
     * @param int $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message = null, $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message ?? trans('messages.error'),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return validation error response.
     *
     * @param mixed $errors
     * @param string|null $message
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, string $message = null): JsonResponse
    {
        return $this->errorResponse(
            $message ?? trans('validation.validation_failed'),
            $errors,
            422
        );
    }

    /**
     * Return not found response.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(
            $message ?? trans('messages.not_found'),
            null,
            404
        );
    }

    /**
     * Return unauthorized response.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(
            $message ?? trans('messages.unauthorized'),
            null,
            401
        );
    }

    /**
     * Return forbidden response.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = null): JsonResponse
    {
        return $this->errorResponse(
            $message ?? trans('messages.forbidden'),
            null,
            403
        );
    }
}