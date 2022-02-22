<?php

namespace framework\Console;

use framework\Console\input\Argument;
use framework\Console\input\Command;
use framework\Console\input\Option;
use framework\Exception\InvalidArgumentException;
use framework\Exception\RuntimeException;

class Input
{
	
	private $argvs;
	private $command;
	private $commandName;
	private $consoleName;
	private $options = [];
	private $shortOptions = [];
	private $arguments = [];
	
	public function __construct($argv = null){
		
        if (null === $argv) {
            $argv = $_SERVER['argv'];
            // 去除命令名
            $this->consoleName = array_shift($argv);
			
			$name = '';
			if(strpos($argv[0], '-') === false){
				$name = array_shift($argv);
			}
			
			$this->commandName = $name;
        }

        $this->argvs = $argv;
	}
	
    /**
     * 解析参数
     */
	private function parse(){
		if($this->argvs){
			foreach($this->argvs as $k=>$argv){
				
				if(strpos($argv, '--') === 0){
					$name = ltrim($argv,'--');
					$this->options[] = $name;
					continue;
				}
				
				if(strpos($argv, '-') === 0){
					$name = ltrim($argv,'-');
					$this->shortOptions[] = $name;
					continue;
				}
				
				$this->arguments[] = end(explode('=',$argv));
			}
		}
		
		try{
			
			$this->validateArguments();
			
		}catch(RuntimeException $ex){
			
			//缺少参数，显示帮助
			$lines = $this->command->describe();
			if($lines){
				foreach($lines as $line){
					$this->command->getConsole()->getOutput()->error($line);
				}
			}
			
			exit;
		}
		
		$this->parseArgument();
	}
	
    /**
     * 绑定命令对象
     */
	public function bind(Command $command){
		
        $this->arguments  = [];
        $this->options    = [];
		$this->command = $command;
		$this->parse();
	}
	
    /**
     * 获取选项
	 * @return array
     */
	public function getOptions(){
		return $this->options;
	}
	
    /**
     * 获取短选项
	 * @return array
     */
	public function getShortOptions(){
		return $this->shortOptions;
	}
	
    /**
     * 获取参数
	 * @return array
     */
	public function getArguments(){
		return $this->arguments;
	}
	
    /**
     * 获取参数
	 * @return string
     */
	public function getArgument(string $name){
		return $this->arguments[$name];
	}
	
    /**
     * 获取命令对象
	 * @return Command
     */
	public function getCommand(){
		return $this->command;
	}
	
    /**
     * 获取命令名称
	 * @return string
     */
	public function getCommandName(){
		return $this->commandName;
	}
	
    /**
     * 获取控制台命令名称
	 * @return string
     */
	public function getConsoleName(){
		return $this->consoleName;
	}
	
    /**
     * 获取完整命令名称
	 * @return string
     */
	public function getFullName(){
		return implode(" ",[$this->consoleName,$this->commandName]);
	}
	
    /**
     * 解析参数
     */
	public function parseArgument(){

		$arguments = $this->command->getArguments();
		if($arguments){
			
			$inputArguments = $this->arguments;
			$this->arguments = array();
			
			if(array_keys($arguments)){
				foreach(array_keys($arguments) as $k=>$name){
					$this->arguments[$name] = isset($inputArguments[$k]) ? $inputArguments[$k] : '';
				}
			}
			
			foreach(array_values($arguments) as $k=>$argument){
				if(isset($this->arguments[$k])){
					$argument->setDefault($this->arguments[$k]);
				}
			}
		}
	}
	
    /**
     * 验证输入参数
     * @throws \RuntimeException
     */
    public function validateArguments()
    {	
        if (count($this->arguments) < $this->command->getArgumentRequiredCount()) {
            throw new RuntimeException('Not enough arguments.');
        }
    }
	
    /**
     * 选项判断
	 * @return bool
     */
	public function hasParameterOption($options = []){
		foreach($options as $option){
			
			$options = explode('-',$option);
			$option = end($options);
			
			if(in_array($option,$this->getOptions())){
				return true;
			}
			
			if(in_array($option,$this->getShortOptions())){
				return true;
			}
		}
		
		return false;
	}
}
