<?php

namespace framework\Console\output\Writer;

class StreamWriteErr implements WriterInterface
{
    /**
     * Write the content to the stream
     *
     * @param  string $content
     */
    public function write($content)
    {
        fwrite(\STDERR, $content);
    }
}
