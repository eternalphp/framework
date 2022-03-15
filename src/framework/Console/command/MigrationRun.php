<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Console\output\Confirm;
use framework\Filesystem\Filesystem;
use Exception;
use framework\Database\Schema\Schema;



class MigrationRun extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('migration')
            ->setDescription('Create a new resource migration class');
    }

    public function execute(Input $input, Output $output)
    {
		$this->Filesystem = new Filesystem();
		$this->Filesystem->getFiles(database_path('migrations'),function($file) use($output){

			require $file;

			$arr = explode("_",basename($file,'.php'));

			$class = new $arr[1];
			
			Schema::onMessage(function($message) use($output){
				$output->success($message);
			});
			
			$class->up();
			

		});
    }
}
