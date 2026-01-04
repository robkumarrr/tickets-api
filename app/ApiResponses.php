<?php

namespace App;

trait ApiResponses
{

    protected function ok($message, $data = []): \Illuminate\Http\JsonResponse
    {
        return $this->success($message, $data, 200);
    }
    protected function success($message, $data, $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }

    protected function error($message, $statusCode): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }
}
