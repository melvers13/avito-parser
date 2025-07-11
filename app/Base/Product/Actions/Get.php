<?php

namespace App\Base\Product\Actions;

use App\Models\Product;
use Illuminate\Support\Collection;

class Get
{
    /**
     * Результаты.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Product::all();
    }
}
