<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\StartParserJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Главная.
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $status = $request->status;

        return view('home.index', compact('status'));
    }

    /**
     * Запуск парсера.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function parsing(Request $request): RedirectResponse
    {
        /**
         * Очистка лога.
         */
        Cache::forget('parser:events');

        /**
         * Запуск парсера.
         */
        StartParserJob::dispatch($request->q);

        return redirect()->route('home.index', ['status' => 'start']);
    }

    /**
     * Статус.
     *
     * @return View
     */
    public function status(): View
    {
        $events = Cache::get('parser:events', []);

        return view('home.status', compact('events'));
    }
}
