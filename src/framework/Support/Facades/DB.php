<?php

namespace framework\Support\Facades;

use framework\Database\Eloquent\Model;
use framework\Support\Facade;

class DB extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'db',
            'class' => Model::class
        ];
    }
}