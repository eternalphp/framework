<?php

namespace framework\Router;

class Router{
	
	public static $instance = null;
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new Routes();
		}
		return self::$instance;
	}
	
	public static function __callStatic($method, $args = array()){
		return call_user_func_array(array(self::getInstance(),$method),$args);
	}
}
?>