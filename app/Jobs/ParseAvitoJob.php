<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AvitoParser;

class ParseAvitoJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected string $query;
    protected int $page;

    public function __construct(string $query, int $page, $delay)
    {
        $this->query = $query;
        $this->page = $page;
        $this->delay($delay);
    }

    public function handle()
    {
        $parser = new AvitoParser();
        $items = $parser->getItems($this->query . '&p=' . $this->page);
        logger()->info("✅ Страница {$this->page} загружена, найдено: " . count($items));

        foreach ($items as $item) {
            Product::create([
                'name' => $item['title'],
                'author' => $item['seller'],
                'price' => $item['price'],
                'url' => $item['link'],
                'page' => $this->page,
            ]);
        }
    }
}
