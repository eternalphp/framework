<?php

namespace framework\Support\Facades;

use framework\Support\Facade;

class File extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'file',
            'class' => \framework\Http\File::class
        ];
    }
}