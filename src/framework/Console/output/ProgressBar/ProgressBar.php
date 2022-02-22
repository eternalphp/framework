<?php

namespace framework\Console\output\ProgressBar;

use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Exception\InvalidArgumentException;

class ProgressBar
{
	
    /**
     * @var int
     */
    private $barWidth = 30;
	
    /**
     * @var int
     */
    private $complete = 0;
	
    /**
     * @var int
     */
    private $total = 100;
	
    /**
     * @var string 当前进度的显示字符
     */
    private $defaultChar = '-';

    /**
     * @var string 已完成的显示字符
     */
    private $completeChar = '=';

    /**
     * @var string 当前进度的显示字符
     */
    private $progressChar = '>';
	
    /**
     * 已完成百分比
     *
     * @var float
     */
    private $percent = 0.0;
	
    /**
     * @var int
     */
    private $startTime = 0;

    /**
     * @var int
     */
    private $endTime = 0;
	
    /**
     * 完成回调事件
     *
     * @var callable
     */
	private $finishCallback = null;
	
	private $currentBarText = '';
	
	public function __construct($total = 100){
		$this->output = new Output();
		$this->style = new Style();
		$this->total = $total;
	}
	
    /**
     * 启动进度
     *
     * return void
     */
	public function start(){
        $this->startTime = time();
        $this->percent   = 0.0;
        $this->started   = true;
	}
	
    /**
     * 设置进度条的长度
     * @param int $width
     * return $this
     */
	public function setBarWidth(int $width){
		$this->barWidth = $width;
		return $this;
	}
	
    /**
     * 获取进度条的长度
     * return int
     */
	public function getBarWidth(){
		return $this->barWidth;
	}
	
    /**
     * 设置当前进度
     * @param int $value
     * return $this
     */
	private function current(int $value){
		if($value > $this->complate){
			$this->complate = $value;
			$this->percent = ceil(($value/$this->total) * 100);
		}
		return $this;
	}
	
    /**
     * 更新当前进度
     * @param int $value
     * return $this
     */
	public function updateProcessBar(int $value){
		$this->current($value);
	}
	
    /**
     * 获取已完成的进度条长度
     * return int
     */
	public function getComplateBarWidth(){
		return ceil($this->complate / $this->total * $this->barWidth);
	}
	
    /**
     * 获取当前进度条数据
     * return string
     */
	public function getProcessBar(){
		$complate = str_pad($this->completeChar,$this->getComplateBarWidth(),$this->completeChar);
		$progressBar = str_pad($complate . $this->progressChar,$this->barWidth,$this->defaultChar);
		
		return sprintf("loading [%s] %s %s",$progressBar,$this->percent . "%",$this->currentBarText);
	}
	
    /**
     * 获取当前进度值
     * return int
     */
	public function getComplate(){
		return $this->complate;
	}
	
    /**
     * 获取当前进度百分比
     * return string
     */
	public function getPercent(){
		return $this->percent . '%';
	}
	
    /**
     * 设置当前进度提示文字
     * return string
     */
	public function setCurrentBarText(string $text){
		$this->currentBarText = $text;
		return $this;
	}
	
    /**
     * 设置完成事件
     * return $this;
     */
	public function finish(callable $callback){
		$this->finishCallback = $callback;
		return $this;
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
	
    /**
     * 显示进度条
	 * @param callable $callback
     * return $this;
     */
	public function display(callable $callback,$loop = false){
		
		if($loop){
			
			while($this->complate <= $this->total){
				
				$this->write("\x0D\x1B[2K");
				$this->write($this->getProcessBar());
				if($this->complate == $this->total) break;
				sleep(1);
				call_user_func($callback,$this,$this->complate);
			}
			
		}else{
			
			if($this->complate <= $this->total){
				$this->write("\x0D\x1B[2K"); //\033[100D
				$this->write($this->getProcessBar());
				
				call_user_func($callback,$this,$this->complate);
			}
			
		}
		
		if($this->complate >= $this->total){
			$this->endTime = time();
			$this->writeln('');
			if($this->finishCallback != null){
				$message = call_user_func($this->finishCallback,array(
					'starttime' => date("Y-m-d H:i:s",$this->startTime),
					'endtime' =>date("Y-m-d H:i:s",$this->endTime),
					'time' => ($this->endTime - $this->startTime)
				));
				if($message) $this->writeln($message);
			}
		}
	}
}
