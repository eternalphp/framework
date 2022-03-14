<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Console\PhpDevServe;

class RunServer extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('serve')
            ->setDescription('Serve the application on the PHP development server');
    }

    public function execute(Input $input, Output $output)
    {
		$serve = new PhpDevServe(public_path(''));
		$serve->listen();
    }
}
