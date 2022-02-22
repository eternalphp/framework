<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;

class VendorPublish extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('vendor:publish')
            ->setDescription('Publish any publishable assets from vendor packages');
    }

    public function execute(Input $input, Output $output)
    {
        $output->error('vendor:publish-----------------------');
    }
}
