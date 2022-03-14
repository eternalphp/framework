<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Filesystem\Filesystem;

class View extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('view:clear')
            ->setDescription('Clear all compiled view files');
    }

    public function execute(Input $input, Output $output)
    {
		$this->Filesystem = new Filesystem();
		
		$this->Filesystem->getFiles(app_path('views/cache'),function($file) use($output){
			$this->Filesystem->delete($file);
			
			$output->error(sprintf("delete file: %s success",$file));
			
		});
		
        
    }
}
