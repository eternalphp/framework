<?php

namespace framework\Validate;

use framework\Validate\Validate;
use Exception;

class ValidateRule{
	
	private $field; //要验证的字段
	private $rules = array(); //要验证的规则列表
	private $message; //错误结果说明
	private $rule;
	private $validators = array(); //验证器对象
	
	public function __construct(){
		$this->rule = new Rule();
		$this->validators['Validator'] = Validator::class;
	}
	
	/**
	 * 设置要验证的字段
	 *
	 * @param string $field
	 * @return $this
	 */
	public function field($field){
		$this->field = $field;
		return $this;
	}
	
	/**
	 * 获取要验证的字段
	 *
	 * @return string
	 */
	public function getField(){
		return $this->field;
	}
	
	/**
	 * 设置验证规则
	 *
	 * @param string $name
	 * @return object
	 */
	public function rule($name){
		$rule = new Factory($name,$this->rule->getRule($name));
		$this->rules[] = $rule;
		return $rule;
	}
	
	/**
	 * 验证错误结果
	 *
	 * @return string
	 */
	public function getMessage(){
		return $this->message;
	}
	
	/**
	 * 验证规则列表
	 *
	 * @param string $value
	 * @return bool
	 */
	public function check($value){
		if($this->rules){
			foreach($this->rules as $rule){
				if($rule->getPattern() != null){
					if(!preg_match($rule->getPattern(),$value)){
						$this->message = $rule->getMessage();
						return false;
					}
				}else{
					$params = array($value);
					$ranges = $rule->getRange();
					if($ranges){
						$params = array_merge($params,$ranges);
					}
					
					if(!is_null($rule->getValue())){
						$params[] = $rule->getValue();
					}
					
					$func = $rule->getFunc();
					$funcs = explode('.',$func);
					if(count($funcs) > 1){
						
						if(isset($this->validators[$funcs[0]])){
							$class = $this->validators[$funcs[0]];
						}else{
							throw new Exception("Validator class does not exist.");
						}
						
						$method = $funcs[1];
						
						if(!method_exists($class,$method)){
							throw new Exception("Method [$method] does not exist.");
						}
						
						$result = call_user_func_array(array($class,$method),$params);
					}else{
						$result = call_user_func_array($func,$params);
					}
					if(!$result){
						$this->message = $rule->getMessage();
						return false;
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * 注册自定义验证器对象
	 *
	 * @param string $value
	 * @return bool
	 */
	public function register(){
		$args = func_get_args();
		if(is_array($args[0])){
			$this->validators = array_merge($this->validators,$args[0]);
		}else{
			$this->validators[$args[0]] = $args[1];
		}
		return $this;
	}
}
?>