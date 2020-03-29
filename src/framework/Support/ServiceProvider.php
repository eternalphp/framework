<?php

namespace framework\Support;

use framework\Container\Container;

abstract class ServiceProvider
{
	
	protected $app;
	
	public function __construct(){
		$this->app = Container::getInstance();
	}
	
	abstract public function register();
	
	abstract public function boot();
}
?>