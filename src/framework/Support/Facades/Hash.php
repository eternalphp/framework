<?php

namespace framework\Support\Facades;

use framework\Hashing\BcryptHasher;
use framework\Support\Facade;

class Hash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return [
            'name' => 'hash',
            'class' => BcryptHasher::class
        ];
    }
}