<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    protected function response(mixed $data = null, string $message = '', int $statusCode = Response::HTTP_OK, bool $success = true): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public function success(mixed $data = null, ?string $message = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->response($data, $message ?? __('success.operation'), $statusCode);
    }

    public function error(string $message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, mixed $data = null): JsonResponse
    {
        return $this->response($data, $message, $statusCode, false);
    }
}