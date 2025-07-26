<?php

namespace App\Jobs;

use App\Services\AvitoParser as AvitoParserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class StartParserJob implements ShouldQueue
{
    use Queueable;

    protected string $query;

    /**
     * Create a new job instance.
     */
    public function __construct(string $query)
    {
        $this->query = $query;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * Очистка продукции.
         */
       // DB::table('products')->delete();

        push_event("🎯 Начата работа парсера Avito по запросу: {$this->query}.");

        /**
         * Загрузка прокси.
         */
        $proxies = app(\App\Base\Proxy\Actions\Get::class)->getAll();
        $proxies_count = $proxies->count();
        push_event("🌐 Используется прокси: {$proxies_count}.");

        push_event("🔍 Получаем количество объявлений.");

        $parser = new AvitoParserService();
        $total = null;

        foreach ($proxies as $index => $proxy) {
            push_event("🔄 Пробуем прокси #".($index + 1).": {$proxy->ip}:{$proxy->port}");

            try {
                $total = $parser->getTotalPages($this->query, [
                    'ip' => $proxy->ip,
                    'port' => $proxy->port,
                    'login' => $proxy->login,
                    'password' => $proxy->password,
                ]);

                push_event("✅ Успешно получены данные через {$proxy->ip}:{$proxy->port}.");
                push_event("📰 Объявлений: {$total['total']}");
                break; // успешно — выходим из цикла
            } catch (\Throwable $e) {
                push_event("❌ Ошибка с прокси {$proxy->ip}: {$e->getMessage()}");
            }
        }

        if (!$total) {
            push_event("⛔ Не удалось получить количество объявлений ни с одним из прокси. Парсинг остановлен.");
            return;
        }

        /**
         * Расчёт времени парсинга.
         */
        // todo
        /*
        $delay_seconds = 150; // 2.5 минуты между запросами для каждого прокси
        $cycles = ceil($total['pages'] / $proxies_count);
        $total_seconds = $cycles * $delay_seconds;

        $hours = floor($total_seconds / 3600);
        $minutes = floor(($total_seconds % 3600) / 60);

        push_event("⏱️ Расчёт времени парсинга: {$hours} ч {$minutes} мин (страниц: {$total['pages']}, прокси: {$proxies_count}, интервал: {$delay_seconds} сек).");

        $delay = now();
        $page = 1;
        $total_pages = $total['pages'];

        while ($page <= $total_pages) {
            dispatch((new ParseAvitoJob($this->query, $page, $proxies->toArray()))
                ->delay($delay));

            push_event("📦 Задача для страницы {$page} поставлена на {$delay->format('H:i:s')}");

            $page++;
            $delay = $delay->addSeconds($delay_seconds); // ⬅️ каждая следующая страница через 2.5 мин
        }
        */
        $delay_seconds = 150; // 2.5 минуты между запросами для каждого прокси
        $skipped_pages = [
            1, 2, 3, 4, 5, 6, 17, 32, 33, 34, 35, 36, 46, 47, 57, 61, 62, 63,
            65, 66, 70, 71, 72, 79, 80, 81, 85, 86, 87, 89, 90, 91, 92, 93, 94,
            95, 96, 97, 98, 99, 100, 101, 102, 106, 107, 108, 113, 114, 115, 123,
            125, 126, 128, 129, 130, 131, 132, 133, 134, 135, 137, 138, 146, 147,
            149, 150, 152, 153, 154, 155, 156, 157, 158, 159, 173, 174, 175, 176,
            177, 184, 185, 186, 188, 189, 192
        ];

        $total_pages = count($skipped_pages);
        $cycles = ceil($total_pages / $proxies_count);
        $total_seconds = $cycles * $delay_seconds;

        $hours = floor($total_seconds / 3600);
        $minutes = floor(($total_seconds % 3600) / 60);

        push_event("⏱️ Расчёт времени парсинга: {$hours} ч {$minutes} мин (страниц: {$total_pages}, прокси: {$proxies_count}, интервал: {$delay_seconds} сек).");

        $delay = now();

        foreach ($skipped_pages as $page) {
            dispatch((new ParseAvitoJob($this->query, $page, $proxies->toArray()))
                ->delay($delay));

            push_event("📦 Задача для страницы {$page} поставлена на {$delay->format('H:i:s')}");

            $delay = $delay->addSeconds($delay_seconds);
        }


    }
}
