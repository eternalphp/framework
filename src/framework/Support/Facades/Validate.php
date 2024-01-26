<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Validate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'validate',
            'class' => \framework\Validate\Validate::class
        ];
    }
}