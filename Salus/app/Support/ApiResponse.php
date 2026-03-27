<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Operation reussie', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data ?? (object) [],
            'message' => $message,
        ], $status);
    }

    protected function error(array $errors = [], string $message = 'Erreur', int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors ?: (object) [],
            'message' => $message,
        ], $status);
    }
}
