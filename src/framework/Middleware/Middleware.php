<?php

namespace framework\Middleware;

use framework\Http\Request;
use Closure;

abstract class Middleware{
	
	abstract function handle(Request $request, Closure $next);
}
?>