<?php

namespace App\Console\Commands;


use framework\Console\input\Command;
use framework\Console\Output;
use framework\Console\Input;
use framework\Exception\InvalidArgumentException;

class {%command_name%} extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('{%command_name%}')
            ->setDescription('user command');

        //设置参数
        /*
		$this->addArgument('name',function($argument){
			$argument->setDescription('The name of the command');
			$argument->setRequired();
		});
		*/

		//设置选项
		/*
		$this->addOption('name','',function($option){
			$option->setDescription('The name of the command');
		});
		*/

		//添加示例说明
		//$this->addUsage('[arguments ...]');

        //添加帮助
		//$this->setHelp('The help of command');

		//添加分组
		//$this->addGroup();
    }

    public function execute(Input $input, Output $output)
    {

        //获取参数
        //$name = $input->getArgument('name');

        //获取选项
        //$name = $input->getOption('name');

        //$output->success('message');

        //$output->error('message');


		//命令行逻辑
    }
}