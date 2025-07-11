<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    /**
     * Результаты.
     *
     * @return View
     */
    public function index(): View
    {
        $products = app(\App\Base\Product\Actions\Get::class)->getAll();

        return view('result.index', compact('products'));
    }
}
