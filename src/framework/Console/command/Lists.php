<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Console\input\Argument;
use framework\Console\input\Option;
use framework\Console\Console;
use framework\Exception\InvalidArgumentException;

class Lists extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('list')
            ->setDescription('Lists commands')->setHelp(
            <<<EOF
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
        )->addUsage('[arguments ...] [options ...]');
		
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
		$lines = $this->describe();
		
		$commands = $this->getConsole()->getDefaultCommands();
		
		$lines = array_merge($lines,$commands);
		
		foreach($lines as $line){
			$output->write($line);
		}
    }
}
