<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Cache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'cache',
            'class' => \framework\Cache\Cache::class
        ];
    }
}