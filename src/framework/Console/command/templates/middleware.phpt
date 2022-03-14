<?php

/**
 * -------------------------------------------------------------------
 * 项目说明
 * -------------------------------------------------------------------
 * Author: yuanzhongyi <564165682@qq.com>
 * -------------------------------------------------------------------
 * Date: 2022-03-11
 * -------------------------------------------------------------------
 * Copyright (c) 2022~2025 http://www.homepage.com All rights reserved.
 * -------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * -------------------------------------------------------------------
 */


use framework\Database\Schema\Control;
use framework\Database\Eloquent\Model;
use framework\Database\Schema\Table;

class {%middleware_name%} extends Middleware {
	
 	function handle(Request $request, Closure $next){
		
		//code this

		$response = call_user_func($next,$request);
		return $response;
	}
}
?>