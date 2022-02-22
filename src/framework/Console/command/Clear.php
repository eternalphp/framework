<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Filesystem\Filesystem;

class Clear extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('clear')
            ->setDescription('Clear runtime file');
    }

    public function execute(Input $input, Output $output)
    {
		$this->Filesystem = new Filesystem();
		
		$dirs = ['access','cache','debug','logs'];
		
		foreach($dirs as $dir){
			
			$this->Filesystem->getFiles(application()->storagePath($dir),function($file) use($output){
				$this->Filesystem->delete($file);
				
				$output->error(sprintf("delete file: %s success",$file));
				
			});
		}
		
        
    }
}
