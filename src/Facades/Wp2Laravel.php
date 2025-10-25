<?php

namespace RezaQsr\Wp2Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Wp2Laravel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'wp2laravel';
    }
}
