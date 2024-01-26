<?php

namespace framework\Support\Facades;

use framework\Config\Repository;
use framework\Support\Facade;

class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'config',
            'class' => Repository::class
        ];
    }
}