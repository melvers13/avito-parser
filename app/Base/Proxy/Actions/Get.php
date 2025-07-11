<?php

namespace App\Base\Proxy\Actions;

use App\Models\Proxy;
use Illuminate\Support\Collection;

class Get
{
    /**
     * Прокси.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Proxy::all();
    }
}
