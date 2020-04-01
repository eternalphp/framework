<?php

namespace framework\Controller;
use framework\Container\Container;

class Controller
{
	
	protected $app;
	protected $view;
	
	public function __construct(){
		$this->app = Container::getInstance();
		$this->view = $this->app['view'];
	}
	
	public function view(){
		$num = func_num_args();
		$args = func_get_args();
		$data = array();
		$namespace = $this->app["route"]->getNamespace();
		$directory = $this->app["route"]->getController();
		$directory = str_replace("Action",'',$directory);
		$path = $this->app["route"]->getAction();
		
		if($num == 1){
			if(is_array($args[0])){
				$data = $args[0];
			}else{
				$path = $args[0];
			}
		}else{
			list($path,$data) = $args;
		}
		
		if($path != '' && strstr($path,'/') == false){
			$path = implode('/',array($namespace,ucfirst($directory),$path));
		}
		
		$this->view->assign($data);
		$this->view->display($path);
	}
}
?>