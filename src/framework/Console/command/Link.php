<?php

namespace framework\Console\command;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;
use framework\Filesystem\Filesystem;

class Link extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('storage:link')
            ->setDescription('Create a symbolic link from "public/upload" to "storage/upload');

        $this->addArgument('name',function($argument){
            $argument->setDescription('The name of the symbolic link');
        });
        
    }

    public function execute(Input $input, Output $output)
    {
        $directory = $input->getArgument('name');
        if($directory == ''){
            $directory = 'upload';
        }

        if(!file_exists(storage_path($directory))){
            mkdir(storage_path($directory),0777,true);
        }
        
        if(file_exists(public_path($directory))){
            $output->error(sprintf("File exists: %s",public_path($directory)));
            return false;
        }

        if(symlink(storage_path($directory), public_path($directory))){
            $output->success(sprintf("Create a symbolic link: %s success",public_path($directory)));
        }else{
            $output->error(sprintf("Create a symbolic link: %s fail",public_path($directory)));
        }
    }
}
