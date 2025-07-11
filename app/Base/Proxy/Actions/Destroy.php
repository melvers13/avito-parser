<?php

namespace App\Base\Proxy\Actions;

use Illuminate\Support\Facades\DB;

class Destroy
{
    /**
     * Очистка списка.
     *
     * @return bool
     */
    public function destroyAll(): bool
    {
        return DB::table('proxies')->delete();
    }
}
