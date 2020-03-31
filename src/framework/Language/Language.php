<?php

namespace framework\Language;

use ArrayAccess;
use framework\Support\Arr;
use framework\Config\ConfigInterface;
use framework\Config\Repository;

class Language extends Repository
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }
}
