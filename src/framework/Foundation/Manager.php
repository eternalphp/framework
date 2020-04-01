<?php

namespace framework\Foundation;

class Manager
{
	protected $app;
	
	public function __construct(){
		$this->app = Application::getInstance();
	}
}
