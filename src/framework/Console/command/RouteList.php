<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Console\input\Argument;
use framework\Console\input\Option;
use framework\Exception\InvalidArgumentException;

class RouteList extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('route:list')
            ->setDescription('show route list.');
			
		$this->addArgument('name',function($argument){
			$argument->setDescription('The name of the class');
		});
		
		$this->addArgument('commandName',function($argument){
			$argument->setDescription('The name of the command');
		});
		
		$this->addOption('help','h',function($option){
			$option->setDescription('Display this help message');
		});
		
		$this->addOption('version','V',function($option){
			$option->setDescription('Display this console version');
		});
    }

    public function execute(Input $input, Output $output)
    {
        $output->error('route list-----------------------');
    }
}
