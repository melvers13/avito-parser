<?php

namespace App\Console\Commands;

use App\Jobs\ParseAvitoJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use App\Services\AvitoParser as AvitoParserService;

class AvitoParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avito:parse {query}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Постраничный парсинг Avito с задержками';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = $this->argument('query');
        $this->info("🔍 Запрос: {$query}");

        $parser = new AvitoParserService();
        $totalPages = $parser->getTotalPages($query);
        $this->info("🔢 Всего страниц: {$totalPages['pages']}");

        $delay = now();
        $batch = [];

        for ($page = 1; $page <= $totalPages; $page++) {
            $delay = $delay->addSeconds(rand(3, 10));
            $batch[] = new ParseAvitoJob($query, $page, $delay);
        }

        Bus::batch($batch)->dispatch();

        $this->info("✅ Все страницы поставлены в очередь.");
    }
}
