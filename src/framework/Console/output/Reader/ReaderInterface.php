<?php

namespace framework\Console\output\Reader;

interface ReaderInterface
{
    /**
     * @return string
     */
    public function line();

    /**
     * @return string
     */
    public function multiLine();
}
