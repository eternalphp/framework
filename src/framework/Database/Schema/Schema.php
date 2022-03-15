<?php

namespace framework\Database\Schema;

class Schema{
	
	public static $instance = null;
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new Control();
		}
		return self::$instance;
	}
	
	public static function __callStatic($method, $args = array()){
		return call_user_func_array(array(self::getInstance(),$method),$args);
	}
}
?>