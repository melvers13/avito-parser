<?php

namespace App\Console\Commands;

use App\Jobs\ParseAvitoJob;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use App\Services\AvitoParser as AvitoParserService;
use Illuminate\Support\Facades\DB;

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
        /**
         * Очистка продукции.
         */
        DB::table('products')->delete(); // если хочешь без модели

        $query = $this->argument('query');
        $this->info("🔍 Запрос: {$query}");

        $parser = new AvitoParserService();
        $totalPages = $parser->getTotalPages($query);
        $this->info("🔢 Всего страниц: {$totalPages['pages']}");

        //dispatch((new ParseAvitoJob('квадроцикл Aodes', 1))->delay(now()->addSeconds(1)));

        $delay = now();

        for ($page = 1; $page <= $totalPages; $page++) {
            $delay = $delay->addSeconds(rand(3, 10));
            dispatch((new ParseAvitoJob($query, $page))->delay($delay));
        }

        $this->info("✅ Все страницы поставлены в очередь.");
    }
}
