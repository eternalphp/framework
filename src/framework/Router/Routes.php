<?php

namespace framework\Router;

class Routes{
	
	private $routes = []; //路由对象集合
	private $namespace = 'Home';
	private $prefix = '';
	private $currRoute = null;
	private $middlewares = [];
	
	public function get($url,$callback = null){
		$this->addRoute($url,$callback,['GET']);
		return $this;
	}
	
	public function post($url,$callback = null){
		$this->addRoute($url,$callback,['POST']);
		return $this;
	}
	
	public function any($url,$callback = null){
		$this->addRoute($url,$callback);
		return $this;
	}
	
	public function put($url,$callback = null){
		$this->addRoute($url,$callback,['PUT']);
		return $this;
	}
	
	public function delete($url,$callback = null){
		$this->addRoute($url,$callback,['DELETE']);
		return $this;
	}
	
	public function patch($url,$callback = null){
		$this->addRoute($url,$callback,['PATCH']);
		return $this;
	}
	
	public function command($url,$callback = null){
		$this->addRoute($url,$callback,['COMMAND']);
		return $this;
	}
	
	public function match(array $methods,$url,$callback = null){
		$this->addRoute($url,$callback,$methods);
		return $this;
	}
	
	public function addRoute($url,$callback = null,$methods = []){
		$this->currRoute = new Route($url,$callback,$methods);
		$this->currRoute->namespaces($this->namespace);
		$this->routes[] = $this->currRoute;
		if($this->prefix != ''){
			$this->currRoute->prefix($this->prefix);
		}
		
		if($this->middlewares){
			$this->currRoute->middleware($this->middlewares);
		}
	}
	
	public function query($uri, $method){
		foreach($this->routes as $route){
			if($route->match($uri, $method)){
				return $route;
			}
		}
		return false;
	}
	
	public function namespaces($namespace){
		$this->namespace = $namespace;
		$this->middlewares = [];
		return $this;
	}
	
	public function where($name,$pattern = null){
		$args = func_get_args();
		if(is_array($args[0]) && count($args[0]) > 0){
			foreach($args[0] as $name=>$pattern){
				$this->currRoute->where($name,$pattern);
			}
		}else{
			$this->currRoute->where($name,$pattern);
		}
		return $this;
	}
	
	public function prefix($prefix){
		$this->prefix = $prefix;
		return $this;
	}
	
	public function name($name){
		$this->currRoute->alias($name);
		return $this;
	}
	
	public function getRoutes(){
		return $this->routes;
	}
	
	public function middleware($middleware){
		if(is_array($middleware)){
			$this->middlewares = array_merge($this->middlewares,$middleware);
		}else{
			$this->middlewares = array_merge($this->middlewares,explode(',',$middleware));
		}
		return $this;
	}
	
	public function group(){
		$args = func_get_args();
		if(is_array($args[0])){
			if(isset($args[0]['namespace'])){
				$this->namespaces($args[0]['namespace']);
			}
			
			if(isset($args[0]['prefix'])){
				$this->prefix($args[0]['prefix']);
			}
			
			if(is_callable($args[1])){
				$callback = $args[1];
			}else{
				$callback = function(){};
			}
		}else{
			$callback = $args[0];
		}
		call_user_func($callback);
	}
}
?>