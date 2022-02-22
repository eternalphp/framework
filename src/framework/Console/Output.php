<?php

namespace framework\Console;

use framework\Console\output\Output as StreamOutput;
use framework\Console\output\table\Table;
use framework\Console\output\ProgressBar\ProgressBar;

class Output extends StreamOutput
{
    /**
     * 设置字体颜色
     * @param string $color
	 * return $this;
     */
	public function setForeground($color){
		$this->style->setForeground($color);
		return $this;
	}
	
    /**
     * 设置背景颜色
     * @param string $color
	 * return $this;
     */
	public function setBackground($color){
		$this->style->setBackground($color);
		return $this;
	}
	
    /**
     * 设置字体样式
	 * return $this;
     */
	public function bold(){
		$this->style->bold();
		return $this;
	}
	
    /**
     * 设置字体样式
	 * return $this;
     */
	public function underscore(){
		$this->style->underscore();
		return $this;
	}
	
    /**
     * 设置字体样式
	 * return $this;
     */
	public function blink(){
		$this->style->blink();
		return $this;
	}
	
    /**
     * 设置字体样式
	 * return $this;
     */
	public function reverse(){
		$this->style->reverse();
		return $this;
	}
	
    /**
     * 设置字体样式
	 * return $this;
     */
	public function conceal(){
		$this->style->conceal();
		return $this;
	}
	
    /**
     * 输出信息
     * @param string $message
     */
    public function info(string $message)
    {
		$message = $this->style
		->setForeground('yellow')
		->setBackground('default')
		->bold()
		->blink()
		->text($message);
        $this->write($message);
    }
	
    /**
     * 输出信息
     * @param string $message
     */
    public function error(string $message)
    {
		$message = $this->style
		->setForeground('red')
		->setBackground('default')
		->bold()
		->blink()
		->text($message);
        $this->write($message);
    }
	
    /**
     * 输出信息
     * @param string $message
     */
    public function success(string $message)
    {
		$message = $this->style
		->setForeground('green')
		->setBackground('default')
		->bold()
		->blink()
		->text($message);
        $this->write($message);
    }
	
    /**
     * 输出表格
     * @param string $message
     */
    public function table(array $data,callable $callback = null)
    {
		$this->style
		->setForeground('default')
		->setBackground('default');
		
		if($callback != null){
			call_user_func($callback,$this->style);
		}
		
		$table = new Table($this->style);
		$headers = [];

		if($data){
			
			foreach($data[0] as $key=>$value){
				if(is_string($key)){
					$headers[] = $key;
				}
			}
			
			if($headers){
				$table->setHeader($headers);
			}
			
			foreach($data as $k=>$rows){
				$table->addRow(array_values($rows));
			}
			
			$table->display();
		}
    }
	
    /**
     * 输出进度条
     * @param string $message
     */
	public function ProgressBar(int $value,callable $callback){
		$progressBar = new ProgressBar($value);
		call_user_func($callback,$progressBar);
		return $progressBar;
	}
}
