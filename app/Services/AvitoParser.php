<?php

namespace App\Services;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Chrome\ChromeDriverService;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\Client;

class AvitoParser
{
    const AVITO_ENDPOINT = "https://www.avito.ru/all/transport?q=";

    /**
     * Создание Selenium драйвера.
     *
     * @return RemoteWebDriver
     */
    private function createDriver(): RemoteWebDriver
    {
        $host = config('services.selenium.host');

        $options = new ChromeOptions();
        $options->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1280,720',
            '--user-agent="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.7204.100 Safari/537.36"'
        ]);

        $options->setExperimentalOption('prefs', [
            'profile.managed_default_content_settings.images' => 2,
            'profile.managed_default_content_settings.stylesheets' => 2,
            'profile.managed_default_content_settings.fonts' => 2,
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        return RemoteWebDriver::create($host, $capabilities);
    }

    /**
     * Получение количества страниц по запросу.
     *
     * @param string $query
     * @return array
     */
    public function getTotalPages(string $query): array
    {
        $url = self::AVITO_ENDPOINT . urlencode($query);
        $driver = $this->createDriver();
        $driver->get($url);

        $driver->manage()->timeouts()->implicitlyWait(5);
        $countText = $driver->findElement(WebDriverBy::cssSelector('[data-marker="page-title/count"]'))->getText();
        $driver->quit();

        $total = (int) preg_replace('/\D+/', '', $countText);

        //file_put_contents(storage_path('app/debug.html'), $driver->getPageSource());

        return [
            'total' => $total,
            'pages' => ceil($total / 50)
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
    public function getItems(string $query, int $page = 1): array
    {
        $url = self::AVITO_ENDPOINT . urlencode($query) . "&p=$page";
        $driver = $this->createDriver();
        $driver->get($url);

        $driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('[data-marker="item"]')
            )
        );

        $items = [];
        $elements = $driver->findElements(WebDriverBy::cssSelector('[data-marker="item"]'));

        foreach ($elements as $element) {
            $title = null;
            $link = null;
            $price = null;
            $seller = null;

            $titleNodes = $element->findElements(WebDriverBy::cssSelector('[itemprop="name"]'));
            if ($titleNodes) {
                $title = $titleNodes[0]->getText();
            }

            $linkNodes = $element->findElements(WebDriverBy::cssSelector('a[itemprop="url"]'));
            if ($linkNodes) {
                $href = $linkNodes[0]->getAttribute('href');
                if ($href && !str_starts_with($href, 'http')) {
                    $href = 'https://www.avito.ru' . $href;
                }
                $link = $href;
            }

            $priceNodes = $element->findElements(WebDriverBy::cssSelector('[data-marker="item-price"]'));
            if ($priceNodes) {
                $price = $priceNodes[0]->getText();
            }

            $sellerNodes = $element->findElements(WebDriverBy::cssSelector('a[href*="/brands/"] > p'));
            if ($sellerNodes) {
                $seller = trim($sellerNodes[0]->getText());
            }

            if ($title && $link) {
                $items[] = compact('title', 'link', 'price', 'seller');
            }
        }

        $driver->quit();
        return $items;
    }

}
