<?php

namespace App\Traits;

use App\Helpers\TelegramHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

/**
 * Trait ApiResponserTrait
 *
 * Standardized API response structure.
 *
 * @package App\Traits
 */
trait ApiResponserTrait
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $headers
     * @return JsonResponse
     */
    protected function successResponse(
        mixed  $data = [],
        string $message = 'Data fetched successfully',
        int    $code = ResponseAlias::HTTP_OK,
        array  $headers = []
    ): JsonResponse
    {
        if (isset($data['wrapper-v2']) && isset($data['headers']) && is_array($data['headers'])) {
            $headers = array_merge($headers, $data['headers']);
            $data = $data['records'];
        }

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code)->withHeaders($headers);
    }

    /**
     * Return a client error JSON response (4xx range).
     *
     * @param mixed $errors
     * @param int $code
     * @param string|null $message
     * @return JsonResponse
     */
    protected function failResponse(
        mixed   $errors = [],
        int     $code = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
        ?string $message = null
    ): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => 'error',
            'message' => $message ?? 'Unprocessable Entity',
            'errors' => $errors
        ], $code);
    }

    /**
     * Return a server error JSON response (5xx range) and triggers Telegram Helper.
     *
     * @param mixed $errors
     * @param string $message
     * @param int $code
     * @param Request|null $request
     * @return JsonResponse
     * @throws RandomException
     */
    protected function errorResponse(
        mixed   $errors = [],
        string  $message = "Internal Server Error",
        int     $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        Request $request = null
    ): JsonResponse
    {
        // Memanggil Static Method dari Class Helpers
        $requestId = TelegramHelper::reportToTelegram($errors, $request, $message, $code);

        $response = [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'request_id' => $requestId,
        ];

        if (config('app.debug') && ($errors instanceof Throwable)) {
            $response['debug'] = [
                'class' => get_class($errors),
                'file' => $errors->getFile(),
                'line' => $errors->getLine(),
                'message' => $errors->getMessage(),
            ];
        }

        return response()->json($response, $code);
    }
}
