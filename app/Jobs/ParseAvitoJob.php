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
    protected array $proxies;

    public function __construct(string $query, int $page, array $proxies)
    {
        $this->query = $query;
        $this->page = $page;
        $this->proxies = $proxies;
    }

    public function handle(): void
    {
        $parser = new AvitoParser();
        $url = $this->query . '&p=' . $this->page;

        foreach ($this->proxies as $index => $proxy) {
            try {
                push_event("🔄 Стр. {$this->page}: Пробуем прокси {$proxy['ip']}:{$proxy['port']}");

                $items = $parser->getItems($this->query, $this->page, [
                    'ip' => $proxy['ip'],
                    'port' => $proxy['port'],
                    'login' => $proxy['login'],
                    'password' => $proxy['password'],
                ]);

                push_event("✅ Страница {$this->page} загружена через {$proxy['ip']}, найдено: " . count($items));

                foreach ($items as $item) {
                    Product::create([
                        'name' => $item['title'],
                        'author' => $item['seller'],
                        'price' => $item['price'],
                        'url' => $item['link'],
                        'page' => $this->page,
                        'location' => $item['location'],
                    ]);
                }

                return; // Успешно, выходим
            } catch (\Throwable $e) {
                push_event("⚠️ Прокси {$proxy['ip']} не сработал на стр. {$this->page}");
            }
        }

        push_event("⛔ Страница {$this->page} была пропущена.");
    }
}
