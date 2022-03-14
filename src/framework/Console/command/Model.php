<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Console\output\Confirm;

class Model extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('make:model')
            ->setDescription('Create a new resource model class');
			
		$this->addArgument('name',function($argument){
			$argument->setDescription('The name of the model');
			$argument->setRequired();
		});
		
		$this->addGroup();
    }

    public function execute(Input $input, Output $output)
    {
        $name = $input->getArgument('name');
		$path = 'Home';
		
		$names = explode("/",$name);
		if(count($names) > 1){
			$path = ucfirst($names[0]);
			$name = $names[1];
		}
		
		$content = file_get_contents($this->getPath());
		
        $content = str_replace(['{%model_name%}', '{%path_name%}'], [
            $name,
			$path
        ], $content);
		
		$filePath = app_path($this->getStorePath($path,$name));

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
		
		$output->info(sprintf("%s create success",$this->getStorePath($path,$name)));
    }
	
	public function getPath(){
		return  implode(DIRECTORY_SEPARATOR,[__DIR__,'templates','model.phpt']);
	}
	
	public function getStorePath($path,$name){
		return implode(DIRECTORY_SEPARATOR,[$path,'Models',"{$name}.php"]);
	}
}
