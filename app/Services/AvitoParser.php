<?php

namespace App\Services;

use AllowDynamicProperties;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Chrome\ChromeDriverService;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class AvitoParser
{
    const AVITO_ENDPOINT = "https://avito.ru/all/transport?q=";

    /**
     * Получает кол-во объявлений к парсингу.
     *
     * @param string $query
     * @param array|null $proxy
     * @return int[]
     */
    public function getTotalPages(string $query, array $proxy = null): array
    {
        $client = new \GuzzleHttp\Client([
            'proxy' => "http://{$proxy['login']}:{$proxy['password']}@{$proxy['ip']}:{$proxy['port']}",
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.7204.100 Safari/537.36',
            ],
            'timeout' => 60,
            'connect_timeout' => 15,
            'verify' => false,
        ]);

        $url = self::AVITO_ENDPOINT . urlencode($query);

        $response = $client->get($url);
        $html = (string) $response->getBody();

        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

        $countText = $crawler->filter('[data-marker="page-title/count"]')->count()
            ? $crawler->filter('[data-marker="page-title/count"]')->text()
            : null;

        if (!$countText) {
            throw new \Exception('Не удалось найти элемент с количеством объявлений');
        }

        $total = (int) preg_replace('/\D+/', '', $countText);

        return [
            'total' => $total,
            'pages' => (int) ceil($total / 50),
        ];
    }


    /**
     * Получение объявлений с указанной страницы.
     *
     * @param string $query
     * @param int $page
     * @return array
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function getItems(string $query, int $page = 1)
    {

    }

}
