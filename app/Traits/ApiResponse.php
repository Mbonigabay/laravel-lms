<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Build success response
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'trace_id' => request()->attributes->get('trace_id'),
            ],
        ], $code);
    }

    /**
     * Build error response
     *
     * @param  string  $message
     * @param  int  $code
     * @param  mixed  $data
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'trace_id' => request()->attributes->get('trace_id'),
            ],
        ], $code);
    }
}
