<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;

class RunServer extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('run')
            ->setDescription('PHP Built-in Server for ThinkPHP');
    }

    public function execute(Input $input, Output $output)
    {
        $output->error('run server-----------------------');
    }
}
