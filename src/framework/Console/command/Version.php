<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;

class Version extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('version')
            ->setDescription('show thinkphp framework version');
    }

    public function execute(Input $input, Output $output)
    {
        $output->error('v' . applition()->version());
    }
}
