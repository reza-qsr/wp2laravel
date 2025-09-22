<?php

namespace RezaQsr\Wp2Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Wp2Laravel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RezaQsr\Wp2Laravel\Wp2LaravelManager::class;
    }
}
