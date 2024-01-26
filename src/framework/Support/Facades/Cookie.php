<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Cookie extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'cookie',
            'class' => \framework\Cookie\Cookie::class
        ];
    }
}