<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;

trait HttpResponses
{
    protected function success(array $data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Request was successful.',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(array $data, ?string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'An error has occurred...',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
