<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Lang extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'language',
            'class' => \framework\Language\Language::class
        ];
    }
}