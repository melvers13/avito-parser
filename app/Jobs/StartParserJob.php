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
        DB::table('products')->delete();

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
        $delay_seconds = 150; // 2.5 минуты между запросами для каждого прокси
        $cycles = ceil($total['pages'] / $proxies_count);
        $total_seconds = $cycles * $delay_seconds;

        $hours = floor($total_seconds / 3600);
        $minutes = floor(($total_seconds % 3600) / 60);

        push_event("⏱️ Расчёт времени парсинга: {$hours} ч {$minutes} мин (страниц: {$total['pages']}, прокси: {$proxies_count}, интервал: {$delay_seconds} сек).");

        $delay = now();

        $page = 1;
        $total_pages = $total['pages'];
        //$total_pages = 2;

        while ($page <= $total_pages) {
            foreach ($proxies as $proxy) {
                if ($page > $total_pages) {
                    break; // всё
                }

                dispatch((new ParseAvitoJob($this->query, $page, $proxies->toArray()))
                    ->delay($delay));

                $page++;
            }

            // Ждём 2.5 минуты между "волнами"
            $delay = $delay->addSeconds($delay_seconds);
        }

    }
}
