<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $parse = app(\App\Services\AvitoParser::class)->getItems('квадроцикл Aodes', 1);

    dd($parse);
});
