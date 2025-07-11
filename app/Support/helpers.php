<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('push_event')) {
    /**
     * Добавление события в кэш.
     *
     * @param string $message
     */
    function push_event(string $message): void
    {
        $id = uniqid('event_', true);

        $event = [
            'id' => $id,
            'message' => $message,
            'started_at' => now()->toDateTimeString(),
        ];

        // Получаем текущие события
        $events = Cache::get('parser:events', []);

        // Добавляем новое
        $events[$id] = $event;

        // Обновляем кэш
        Cache::put('parser:events', $events);
    }

}
