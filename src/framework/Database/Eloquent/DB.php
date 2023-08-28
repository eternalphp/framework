<?php

namespace framework\Database\Eloquent;

final class DB{

    public static $model = null;

    public static function getInstance(){
        if(self::$model == null){
            self::$model = new Model();
        }
        return self::$model;
    }

	public static function __callStatic($method, $args = array()){
		return call_user_func_array(array(DB::getInstance(),$method),$args);
	}
}
?>