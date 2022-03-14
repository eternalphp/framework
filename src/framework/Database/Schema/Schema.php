<?php

use framework\Database\Schema;

class Schema{
	
	public static $instance = null;
	
	public static function getInstance(){
		if(self::$instance == null){
			
			$config = Config("database");
			
			$cfg = array(
				'driver'=>'MySqli',
				'servername'=>$config['DB_HOST'],
				'username'=>$config['DB_USER'],
				'password'=>$config['DB_PWD'],
				'database'=>$config['DB_NAME'],
				'port'=>$config['DB_PORT'],
				'prefix'=>$config['DB_PREFIX']
			);
			
			self::$instance = new Control($cfg);
		}
		return self::$instance;
	}
	
	public static function __callStatic($method, $args = array()){
		return call_user_func_array(array(self::getInstance(),$method),$args);
	}
}
?>