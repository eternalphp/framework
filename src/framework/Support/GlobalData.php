<?php 

namespace framework\Support;

use stdClass;

class GlobalData{
	
	static $globalData = [];
	
	public static function set($name,$value = null){
		self::$globalData[$name] = $value;
	}
	
	public static function get($name){
		return isset(self::$globalData[$name]) ? self::$globalData[$name] : null;
	}
	
	public static function remove($name){
		unset(self::$globalData[$name]);
	}
	
	public static function all(){
		return self::$globalData;
	}
}
?>