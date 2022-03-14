<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Console\output\Confirm;

class Migration extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('make:migration')
            ->setDescription('Create a new resource migration class');
			
		$this->addArgument('name',function($argument){
			$argument->setDescription('The name of the migration');
			$argument->setRequired();
		});
		
		$this->addOption('create','',function($option){
			$option->setDescription('The name of the table');
		});
		
		$this->addOption('table','',function($option){
			$option->setDescription('The name of the table');
		});
		
		$this->addGroup();
    }

    public function execute(Input $input, Output $output)
    {
        $name = $input->getArgument('name');
		
		$table = $input->getOption('create');
		
		$content = file_get_contents($this->getPath());
		
        $content = str_replace(['{%migration_name%}','{%table_name%}'], [
            $name,
			$table
        ], $content);
		
		$fileName = date("YmdHis_") . $name;
		
		$filePath = database_path($this->getStorePath($fileName));

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
		
		$output->info(sprintf("%s create success",$this->getStorePath($fileName)));
    }
	
	public function getPath(){
		return  implode(DIRECTORY_SEPARATOR,[__DIR__,'templates','migration.phpt']);
	}
	
	public function getStorePath($name){
		return implode(DIRECTORY_SEPARATOR,['migrations',"{$name}.php"]);
	}
}
