<?php

namespace framework\Database\Eloquent;

final class DB{

	public static function __callStatic($method, $args = array()){
		return call_user_func_array(array(new Model(),$method),$args);
	}
}
?>