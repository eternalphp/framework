<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class View extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'view',
            'class' => \framework\View\View::class
        ];
    }
}