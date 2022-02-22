<?php

namespace framework\Console\output;


use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Console\output\Reader\StreamReader;

class Confirm extends Ask
{
	private $default = 'yes';
	private $options = ['yes','no'];
	
	/**
	 * 执行操作
	 * @param callable $callback
	 * @return output
	 */
	public function run(callable $callback = null){
		
		$this->writePrompt();
		
		$answer = $this->stream->line();
		
		$answer = empty($answer) ? $this->default : $answer;
		
		if(!in_array($answer,$this->options)){
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
		$this->write(sprintf("Please confirm (%s) [default:%s] : ",implode("|",$this->options),$this->default));
	}
}
