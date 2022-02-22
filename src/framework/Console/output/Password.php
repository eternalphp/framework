<?php

namespace framework\Console\output;


use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Console\output\Reader\StreamReader;
use framework\Exception\RuntimeException;
use Exception;

class Password extends Ask
{
	/**
	 * 执行操作
	 * @param callable $callback
	 * @return output
	 */
	public function run(callable $callback = null){
		
		$this->writePrompt();
		
		$answer = $this->stream->hidden($this->stream);
		
		if($callback != null){
			call_user_func($callback,$answer);
		}else{
			$this->writeln($answer);
		}
		
	}
	
	/**
	 * 输出提示语句
	 * @return output
	 */
	public function writePrompt(){
		$this->writeln(sprintf("%s :",$this->question));
	}
	
 
}
