<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class Event extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'event',
            'class' => \framework\Event\Event::class
        ];
    }
}