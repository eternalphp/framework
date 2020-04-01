<?php

namespace framework\Validate;

class Rule{
	
	private $rules = array(); //验证规则表
	
	public function __construct(){
		
		$this->rules = array(
			"required" => array('func'=>'Validator.isRequired','message'=>'不能为空'),
			"number" => array('func'=>'is_numeric','message'=>'必须为数字'),
			"minlength" => array('func'=>'Validator.minlength','message'=>'字符串长度不能少于%d'),
			"maxlength" => array('func'=>'Validator.maxlength','message'=>'字符串长度不能大于%d'),
			"decimal" => array('pattern'=>'/^[-]{0,1}(\d+)[\.]+(\d+)$/','message'=>'必须为DECIMAL格式'),
			"english" => array('pattern'=>'/^[A-Za-z]+$/','message'=>'必须为英文字母'),
			"upper_english" => array('pattern'=>'/^[A-Z]+$/','message'=>'必须为大写英文字母'),
			"lower_english" => array('pattern'=>'/^[a-z]+$/','message'=>'必须为小写英文字母'),
			"email" => array('pattern'=>"/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/",'message'=>'Email格式不正确'),
			"chinese" => array('pattern'=>'/[\x7f-\xff]/','message'=>'必须含有中文'),
			"url" => array('pattern'=>'/^[a-zA-z]+:\/\/[^s]*/','message'=>'URL格式不正确'),
			"phone" => array('pattern'=>'/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{0,6}[\-\s]?\d{4,12}$/','message'=>'电话号码格式不正确'),
			"mobile" => array('pattern'=>'/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{4,12}$/','message'=>'手机号码格式不正确'),
			"ip" => array('pattern'=>'/(?:(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)/','message'=>'IP地址格式不正确'),
			"money" => array('pattern'=>'/^[0-9]+[\.][0-9]{0,3}$/','message'=>'金额格式不正确'),
			"number_letter" => array('pattern'=>'/^[0-9a-zA-Z\_]+$/','message'=>'只允许输入英文字母、数字、_'),
			"zip_code" => array('pattern'=>'/^\d{4,8}$/','message'=>'邮政编码格式不正确'),
			"account" => array('pattern'=>'/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/','message'=>'账号名不合法，允许5-16字符，字母下划线和数字'),
			"password" => array('pattern'=>'/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/','message'=>'密码不合法，允许5-16字符，字母下划线和数字'),
			"compare" => array('func'=>'Validator.compare','message'=>'确认密码失败'),
			"qq" => array('pattern'=>'/^[1-9]\d{4,14}$/','message'=>'QQ账号不正确'),
			"card" => array('pattern'=>'/^(?:\d{17}[\d|X]|\d{15})$/','message'=>'身份证号码不正确')
		);
	}
	
	/**
	 * 获取验证规则
	 *
	 * @param $name
	 * @return array
	 */
	public function getRule($name){
		return isset($this->rules[$name]) ? $this->rules[$name] : array();
	}
}
?>