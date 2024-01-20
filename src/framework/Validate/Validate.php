<?php

namespace framework\Validate;

class Validate{
	
	private $rules = array();
	private $errors = array();
	private $rule;
	
	public function __construct(){

	}
	
	/**
	 * 绑定验证器
	 *
	 * @param callable $callback
	 * @return $this;
	 */
	public function make(callable $callback){
		$this->rule = new ValidateRule();
		call_user_func($callback,$this->rule);
		$this->rules[$this->rule->getField()] = $this->rule;
		return $this;
	}
	
	/**
	 * 验证数据规则
	 *
	 * @param array $data
	 * @param callable $callback
	 * @return bool
	 */
	public function validate($data = array(),callable $callback = null){
		if($this->rules){
			foreach($this->rules as $rule){
				if(!$rule->check($data[$rule->getField()])){
					$this->errors[$rule->getField()] = $rule->getMessage();
				}
			}
		}
		
		if($this->errors){
			if(is_callable($callback)){
				call_user_func($callback,$this->errors);
			}
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * 验证是否通过
	 *
	 * @return bool
	 */
	public function fails(){
		return ($this->errors) ? true : false;
	}
	
	/**
	 * 获取错误结果
	 *
	 * @return array
	 */
	public function getError(){
		return $this->errors;
	}
}
?>