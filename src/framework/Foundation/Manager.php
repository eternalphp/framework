<?php

namespace framework\Foundation;

class Manager
{
	protected $app;
	
	public function __construct(Application $app){
		$this->app = $app;
	}
}
