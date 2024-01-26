<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Logger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'logger',
            'class' => \framework\Logger\Logger::class
        ];
    }
}