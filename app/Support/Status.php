<?php

namespace App\Support;

class Status
{
    /**
     * Success response.
     *
     * @param string|null $message
     * @param array $data
     * @return array
     */
    public static function success(string $message = NULL, array $data = []): array
    {
        $message = $message ?? 'Операция успешно выполнена.';

        return array_merge([
            'status' => 'success',
            'message' => $message
        ], $data);
    }

    /**
     * Error response.
     *
     * @param string|null $message
     * @param array $data
     * @return array
     */
    public static function error(string $message = NULL, array $data = []): array
    {
        $message = $message ?? 'Произошла ошибка.';

        return array_merge([
            'status' => 'error',
            'message' => $message
        ], $data);
    }
}
