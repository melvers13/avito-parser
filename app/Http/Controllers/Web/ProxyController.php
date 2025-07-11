<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProxyController extends Controller
{
    /**
     * Прокси.
     *
     * @return View
     */
    public function index(): View
    {
        $proxies = app(\App\Base\Proxy\Actions\Get::class)->getAll();

        return view('proxy.index', compact('proxies'));
    }

    /**
     * Загрузка прокси.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'list' => 'required',
        ]);

        $list = $request->input('list');
        $lines = explode("\n", trim($list));
        $proxies = [];

        /**
         * Подготовка массива для вставки.
         */
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = explode(':', $line);

            if (count($parts) !== 4) {
                continue;
            }

            [$ip, $port, $login, $password] = $parts;

            $proxies[] = [
                'ip' => $ip,
                'port' => $port,
                'login' => $login,
                'password' => $password,
            ];
        }

        if (!empty($proxies)) {
            /**
             * Очистка списка.
             */
            app(\App\Base\Proxy\Actions\Destroy::class)->destroyAll();

            /**
             * Вставка.
             */
            app(\App\Base\Proxy\Actions\Create::class)->insert($proxies);
        }

        return redirect()->back();
    }
}
