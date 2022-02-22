<?php

namespace framework\Console\output\Writer;

class StreamWrite implements WriterInterface
{
    /**
     * Write the content to the stream
     *
     * @param  string $content
     */
    public function write($content)
    {
        fwrite(\STDOUT, $content);
    }
}
