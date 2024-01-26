<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Response extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'response',
            'class' => \framework\Http\Response::class
        ];
    }
}