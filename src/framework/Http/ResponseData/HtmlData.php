<?php

namespace framework\Http\ResponseData;

use framework\Http\DataFormat;
use framework\Http\Response;

class HtmlData extends DataFormat
{
    public function format(Response $response)
    {
        if(!is_string($this->data))
            $this->data = var_export($this->data);

        return $this->data;
    }
}