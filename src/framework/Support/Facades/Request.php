<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Request extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'request',
            'class' => \framework\Http\Request::class
        ];
    }
}