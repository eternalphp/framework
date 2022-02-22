<?php

namespace framework\Console\output\Writer;

interface WriterInterface
{
    /**
     * @param  string $content
     *
     * @return void
     */
    public function write($content);
}
