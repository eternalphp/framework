<?php

namespace framework\Console\output;


use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Console\output\Reader\StreamReader;

class Ask
{
	
	protected $output;
	protected $style;
	protected $question;
	protected $answer;
	protected $stream;
	
	public function __construct(string $question){
		
		$this->output = new Output();
		$this->style = new Style();
		$this->question = $question;
		$this->stream = new StreamReader();
	}
	
	/**
	 * 执行操作
	 * @param callable $callback
	 * @return output
	 */
	public function run(callable $callback = null){
		$this->writePrompt();
		
		$answer = $this->stream->line();
		if(!empty($answer)){
			
			if($callback != null){
				call_user_func($callback,$answer);
			}else{
				$this->writeln($answer);
			}
		}
	}
	
	/**
	 * 输出提示语句
	 * @return output
	 */
	public function writePrompt(){
		$this->writeln(sprintf("%s :",$this->question));
	}
	
    /**
     * 输出数据
	 * @param string $message
     * return output
     */
	public function write($message){
		$message = $this->style->text($message);
		$this->output->sameLine()->write($message);
	}
	
    /**
     * 输出数据
	 * @param string $message
     * return output
     */
	public function writeln($message){
		$message = $this->style->text($message);
		$this->output->write($message);
	}
	
    /**
     * 设置样式
	 * @param callable $callback
     * return void
     */
	public function setStyle(callable $callback){
		call_user_func($callback,$this->style);
		return $this;
	}
	
    /**
     * 获取样式对象
     * return Style
     */
	public function getStyle(){
		return $this->style;
	}
}
