<?php

namespace framework\Http\ResponseData;

use framework\Http\DataFormat;
use framework\Http\Response;

class JsonData extends DataFormat
{
    public function format(Response $response)
    {
        $data = json_encode($this->data);

        return $data;
    }
}