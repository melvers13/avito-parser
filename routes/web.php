<?php

use App\Http\Controllers\Web\ResultController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProxyController;

/**
 * Главная.
 */
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::post('/parsing', [HomeController::class, 'parsing'])->name('home.parsing');
Route::get('/parsing/status', [HomeController::class, 'status'])->name('home.status');

/**
 * Результаты.
 */
Route::get('/result', [ResultController::class, 'index'])->name('result.index');

/**
 * Прокси.
 */
Route::get('/proxies', [ProxyController::class, 'index'])->name('proxy.index');
Route::post('/proxies/create', [ProxyController::class, 'create'])->name('proxy.create');
