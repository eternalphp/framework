<?php

namespace framework\Console\output;


use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Console\output\Reader\StreamReader;
use Exception;

class Select extends Ask
{
	private $default = 'a';
	private $options = [];
	
	/**
	 * 执行操作
	 * @param callable $callback
	 * @return output
	 */
	public function run(callable $callback = null){
		
		try{
			$this->writePrompt();
		}catch(Exception $ex){
			$this->writeln($ex->getMessage());
			exit;
		}
		
		$answer = $this->stream->line();
		
		$answer = empty($answer) ? $this->default : trim($answer);
		
		if($answer == 'q'){
			exit;
		}
		
		if(!in_array($answer,array_keys($this->options))){
			return $this->run();
		}
		
		if($callback != null){
			call_user_func($callback,$answer);
		}else{
			$this->writeln($answer);
		}
		
	}
	
	/**
	 * 设置选项
	 * @param array $options
	 * @return $this;
	 */
	public function setOptions(array $options){
		$this->options = $options;
		
		$this->default = (array_keys($options))[0];
		return $this;
	}
	
	/**
	 * 设置默认选项
	 * @param string $default
	 * @return $this;
	 */
	public function setDefault(string $default){
		$this->default = $default;
		return $this;
	}
	
	/**
	 * 输出提示语句
	 * @return output
	 */
	public function writePrompt(){
		$this->writeln(sprintf("%s :",$this->question));
		
		if($this->options){
			foreach($this->options as $key=>$val){
				$this->writeln(sprintf("%s) %s",$key,$val));
			}
			$this->writeln(sprintf("q) Quit",$key,$val));
		}else{
			throw new Exception("请设置选项！");
		}
		
		$this->write(sprintf("You choice[default:%s] : ",$this->default));
	}
}
