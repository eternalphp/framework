<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Filesystem extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'filesystem',
            'class' => \framework\Filesystem\Filesystem::class
        ];
    }
}