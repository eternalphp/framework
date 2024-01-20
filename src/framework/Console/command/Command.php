<?php

namespace framework\Console\command;


use framework\Console\input\Command as BaseCommand;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Console\output\Confirm;

class Command extends BaseCommand
{
    public function configure()
    {
        // 指令配置
        $this->setName('make:command')
            ->setDescription('Create a new Artisan command');
			
		$this->addArgument('name',function($argument){
			$argument->setDescription('The name of the command');
			$argument->setRequired();
		});
		
		$this->addGroup();
    }

    public function execute(Input $input, Output $output)
    {
        $name = $input->getArgument('name');

        $content = file_get_contents($this->getPath());

        $content = str_replace(['{%command_name%}'], [
            $name,
        ], $content);

        $filePath = app_path($this->getStorePath($name));

        if(file_exists($filePath)){

            $confirm = new Confirm("The file already exists. Are you sure you want to re create it?");
            $confirm->run(function($answer) use($output){
                if($answer == 'no'){
                    $output->error("You have cancelled the operation");
                    exit;
                }
            });
        }

        if(!file_exists(dirname($filePath))){
            mkdir(dirname($filePath),0777,true);
        }

        file_put_contents($filePath,$content);

        $output->info(sprintf("%s create success",$this->getStorePath($name)));
    }
	
	public function getPath(){
		return  implode(DIRECTORY_SEPARATOR,[__DIR__,'templates','command.phpt']);
	}
	
	public function getStorePath($name){
		return implode(DIRECTORY_SEPARATOR,['Console','Commands',"{$name}.php"]);
	}
}
