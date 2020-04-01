<?php

namespace framework\Validate;

class Factory{
	
	private $name;
	private $pattern;
	private $func;
	private $message;
	private $ranges = array();
	private $value = null;
	
	public function __construct($name,$rule = array()){
		
		$this->name = $name;
		
		if(isset($rule['pattern'])){
			$this->pattern = $rule['pattern'];
		}
		
		if(isset($rule['func'])){
			$this->func = $rule['func'];
		}
		
		if(isset($rule['message'])){
			$this->message = $rule['message'];
		}
	}
	
	/**
	 * 获取规则名称
	 *
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * 设置验证表达式
	 *
	 * @param $pattern
	 * @return $this
	 */
	public function pattern($pattern){
		$this->pattern = $pattern;
		return $this;
	}
	
	/**
	 * 设置验证函数
	 *
	 * @param $func
	 * @return $this
	 */
	public function func($func){
		$this->func = $func;
		return $this;
	}
	
	/**
	 * 设置验证提示语
	 *
	 * @param $message
	 * @return $this
	 */
	public function message($message){
		$this->message = $message;
		return $this;
	}
	
	/**
	 * 设置验证取值范围
	 *
	 * @param int $min
	 * @param int $max
	 * @return $this
	 */
	public function ranges($min,$max){
		$this->ranges = [$min,$max];
		return $this;
	}
	
	/**
	 * 设置验证数据
	 *
	 * @param string | int $value
	 * @return $this
	 */
	public function value($value){
		$this->value = $value;
		return $this;
	}
	
	/**
	 * 获取验证规则表达式
	 *
	 * @return string
	 */
	public function getPattern(){
		return $this->pattern;
	}
	
	/**
	 * 获取验证规则函数
	 *
	 * @return string
	 */
	public function getFunc(){
		return $this->func;
	}
	
	/**
	 * 获取验证提示语
	 *
	 * @return string
	 */
	public function getMessage(){
		return $this->message;
	}
	
	/**
	 * 获取验证规则取值范围
	 *
	 * @return array
	 */
	public function getRange(){
		return $this->ranges;
	}
	
	/**
	 * 获取验证值
	 *
	 * @return string
	 */
	public function getValue(){
		return $this->value;
	}
}
?>