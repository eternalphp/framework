<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Event\Dispatcher;

class Listen extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('listen')
            ->setDescription('listen the events');
    }

    public function execute(Input $input, Output $output)
    {
        Dispatcher::getInstance()->listen(function($event) use($output){
			
			$output->info(sprintf("[%s] run event %s",date('Y-m-d H:i:s'),$event->getName()));
			
		});
    }
}
