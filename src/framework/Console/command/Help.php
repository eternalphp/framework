<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Console\output\table\Table;
use framework\Exception\InvalidArgumentException;


class Help extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('help')
            ->setDescription('Displays help for a command');
			
		$this->addArgument('command_name',function($argument){
			$argument->setDescription('The name of the command');
			$argument->setRequired();
		});

    }

    public function execute(Input $input, Output $output)
    {
		$commandName = $input->getArgument('command_name');
		$command = $this->getConsole()->getCommand($commandName);
		
		$lines = $command->describe();

		if($lines){
			foreach($lines as $line){
				$output->write($line);
			}
		}

    }
}
