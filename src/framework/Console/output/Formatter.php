<?php

namespace framework\Console\output;


use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Exception\InvalidArgumentException;
use Exception;

class Formatter
{
	static $instance = null;
	private $styles    = [];
	
	public function __construct(){
		$this->addStyle('error',$this->style('red','white'));
		$this->addStyle('info',$this->style('white','green'));
		$this->addStyle('comment',$this->style('yellow','white'));
		$this->addStyle('question',$this->style('cyan','black'));
		$this->addStyle('highlight',$this->style('red'));
		$this->addStyle('warning',$this->style('yellow','black'));
	}
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/*
	 * 添加一个样式
	 * @param string $name
	 * @param Style $style
	 * @return $this;
	 **/
	public function addStyle(string $name, Style $style){
		$this->styles[strtolower($name)] = $style;
		return $this;
	}
	
	/*
	 * 获取一个样式
	 * @param string $name
	 * @return $this;
	 **/
	public function getStyle(string $name){
		
        if(!isset($this->styles[strtolower($name)])){
            throw new InvalidArgumentException(sprintf('Undefined style: %s', $name));
        }
		
		return $this->styles[strtolower($name)];
	}
	
	/*
	 * 设置一个样式对象
	 * @param string $color
	 * @param string $bgColor
	 * @return $this;
	 **/
	public function style(string $color,string $bgColor = 'default'){
		$style = new Style();
		$style->setForeground($color)
		->setBackground($bgColor);
		return $style;
	}
	
	/*
	 * 格式化文本
	 * @param string $message
	 * @return $this;
	 **/
	public function format(string $message){
        $tagRegex = '[a-z][a-z0-9_=;-]*';
        preg_match_all("/<([a-z]+)>(.*?)<\/[a-z]+>/is", $message, $matches);
		foreach ($matches[0] as $k => $match) {
			$text = $matches[2][$k];
			$style = $matches[1][$k];
			$text = $this->getStyle($style)->text($text);
			$message = str_replace($match,$text,$message);
		}
		return $message;
	}
}
