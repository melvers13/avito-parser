<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $fillable = [
        'ip',
        'port',
        'login',
        'password',
    ];

    public $timestamps = false;
}
