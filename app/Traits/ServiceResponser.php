<?php

namespace App\Traits;

trait ServiceResponser
{
    /**
     * @param mixed $data
     * @param string $message
     * @return array
     */
    protected function finalResultSuccess(mixed $data = [], string $message = 'success'): array
    {
        return ['status' => true, 'data' => $data, 'message' => $message];
    }

    /**
     * @param mixed $dataFail
     * @param string $message
     * @return array
     */
    protected function finalResultFail(mixed $dataFail = [], string $message = ""): array
    {
        return ['status' => false, 'data' => $dataFail, 'message' => $message];
    }

    protected function finalResultError(mixed $errors = []): array
    {
        return ['status' => false, 'errors' => $errors];
    }
}
