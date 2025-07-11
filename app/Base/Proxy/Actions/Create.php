<?php

namespace App\Base\Proxy\Actions;

use Illuminate\Support\Facades\DB;

class Create
{
    /**
     * Загрузка прокси.
     *
     * @param array $proxies
     * @return bool
     */
    public function insert(array $proxies): bool
    {
        return DB::table('proxies')->insert($proxies);
    }
}
