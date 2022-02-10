<?php

namespace framework\Middleware;

use framework\Http\Request;
use Closure;

class AuthMiddleware extends Middleware{
	
	function handle(Request $request, Closure $next){
		$response = call_user_func($next,$request);
		return $response;
	}
}
?>