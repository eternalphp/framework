<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Router extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'router',
            'class' => \framework\Router\Router::class
        ];
    }
}