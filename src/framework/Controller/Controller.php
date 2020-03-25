<?php

namespace framework\Controller;
use framework\Container\Container;
use framework\View\View;

class Controller
{
	
	protected $app;
	protected $view;
	
	public function __construct(){
		$this->app = Container::getInstance();
		$this->app->bind('view',new View());
		$this->view = $this->app['view'];
	}
	
	
}
?>