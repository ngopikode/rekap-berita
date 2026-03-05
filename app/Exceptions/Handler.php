<?php

namespace App\Exceptions;

use App\Helpers\TelegramHelper;
use App\Traits\ApiResponserTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 *
 * Centralized exception handling with API response standardization
 * and production-grade Telegram reporting.
 */
class Handler extends ExceptionHandler
{
    use ApiResponserTrait;

    /**
     * Inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Exceptions that should NOT be reported to Telegram.
     *
     * @var array<int, string>
     */
    protected array $dontReportToTelegram = [
        AuthenticationException::class,
        ValidationException::class,
        NotFoundHttpException::class,
        MethodNotAllowedHttpException::class,
    ];

    /**
     * Register exception handling callbacks.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {

            // =========================
            // HANDLE API REQUEST
            // =========================
            if ($request->is('api/*')) {
                return $this->handleApiException($e, $request);
            }

            // =========================
            // REPORT WEB ERROR TO TELEGRAM
            // =========================
            $this->reportToTelegramIfNeeded($e, $request);

            // Let Laravel handle normal web rendering
            return null;
        });
    }

    /**
     * Handle API exceptions with standardized JSON response.
     *
     * @throws RandomException
     */
    private function handleApiException(Throwable $e, Request $request): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return $this->failResponse(
                $e->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                $e->getMessage()
            );
        }

        if ($e instanceof AuthenticationException) {
            return $this->failResponse(
                [],
                ResponseAlias::HTTP_UNAUTHORIZED,
                'Unauthenticated.'
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->failResponse(
                [],
                ResponseAlias::HTTP_NOT_FOUND,
                'Resource or Endpoint Not Found.'
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->failResponse(
                [],
                ResponseAlias::HTTP_METHOD_NOT_ALLOWED,
                'Method Not Allowed.'
            );
        }

        $statusCode = $this->resolveStatusCode($e);

        // Only report serious API errors (>=500)
        if ($statusCode >= 500) {
            $this->reportToTelegramIfNeeded($e, $request, $statusCode);
        }

        return $this->errorResponse(
            $e,
            'Internal Server Error',
            $statusCode,
            $request
        );
    }

    /**
     * Determine HTTP status code from exception.
     */
    private function resolveStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        }

        return ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Report error to Telegram safely and selectively.
     * @throws RandomException
     */
    private function reportToTelegramIfNeeded(
        Throwable $e,
        Request   $request,
        ?int      $statusCode = null
    ): void
    {

        // Skip in local environment
        if (app()->environment('local')) {
            return;
        }

        // Skip excluded exceptions
        foreach ($this->dontReportToTelegram as $exceptionClass) {
            if ($e instanceof $exceptionClass) {
                return;
            }
        }

        $statusCode ??= $this->resolveStatusCode($e);

        // Only report critical errors
        if ($statusCode < 500) {
            return;
        }

        TelegramHelper::reportToTelegram(
            errors: $e,
            request: $request,
            code: $statusCode
        );
    }
}
