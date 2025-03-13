<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return success response.
     *
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, ?string $message = null, int $code = 200)
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
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(?string $message = null, $errors = null, int $code = 400)
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
}
