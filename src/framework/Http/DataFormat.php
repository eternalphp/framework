<?php

namespace framework\Http;

use Exception;

abstract class DataFormat{
	
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    abstract public function format(Response $response);
}