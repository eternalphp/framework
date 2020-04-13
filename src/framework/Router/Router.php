<?php

namespace framework\Router;

class Router{
	
	private static $routes = []; //路由对象集合
	private static $namespace = 'Home';
	private static $prefix = '';
	private static $instance = null;
	private static $currRoute = null;
	
	public static function getInstance(){
		if(!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 注册get路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function get($url,$callback = null){
		self::addRoute($url,$callback,['GET']);
		return self::getInstance();
	}
	
	/**
	 * 注册post路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function post($url,$callback = null){
		self::addRoute($url,$callback,['POST']);
		return self::getInstance();
	}
	
	/**
	 * 注册任意路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function any($url,$callback = null){
		self::addRoute($url,$callback);
		return self::getInstance();
	}
	
	/**
	 * 注册put路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function put($url,$callback = null){
		self::addRoute($url,$callback,['PUT']);
		return self::getInstance();
	}
	
	/**
	 * 注册delete路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function delete($url,$callback = null){
		self::addRoute($url,$callback,['DELETE']);
		return self::getInstance();
	}
	
	/**
	 * 注册patch路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function patch($url,$callback = null){
		self::addRoute($url,$callback,['PATCH']);
		return self::getInstance();
	}
	
	/**
	 * 注册命令行路由
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function command($url,$callback = null){
		self::addRoute($url,$callback,['COMMAND']);
		return self::getInstance();
	}
	
	/**
	 * 注册多方法路由
	 * @param array $methods
	 * @param string $url
	 * @param callable $callback
	 * @return self;
	 */
	public static function match(array $methods,$url,$callback = null){
		self::addRoute($url,$callback,$methods);
		return self::getInstance();
	}
	
	/**
	 * 实例化一个路由对象
	 * @param string $url
	 * @param callable $callback
	 * @param array $methods
	 * @return void;
	 */
	public static function addRoute($url,$callback = null,$methods = []){
		self::$currRoute = new Route($url,$callback,$methods);
		self::$currRoute->namespace(self::$namespace);
		self::$routes[] = self::$currRoute;
		if(self::$prefix != ''){
			self::$currRoute->prefix(self::$prefix);
		}
	}
	
	/**
	 * 检测路由规则
	 * @param string $uri
	 * @param string $method
	 * @return bool;
	 */
	public static function query($uri, $method){
		foreach(self::$routes as $route){
			if($route->match($uri, $method)){
				return $route;
			}
		}
		return false;
	}
	
	/**
	 * 设置命名空间
	 * @param string $namespace
	 * @return self;
	 */
	public static function namespace($namespace){
		self::$namespace = $namespace;
		return self::getInstance();
	}
	
	/**
	 * 设置路由规则
	 * @param string $name
	 * @param string $pattern
	 * @return self;
	 */
	public static function where($name,$pattern = null){
		$args = func_get_args();
		if(is_array($args[0]) && count($args[0]) > 0){
			foreach($args[0] as $name=>$pattern){
				self::$currRoute->where($name,$pattern);
			}
		}else{
			self::$currRoute->where($name,$pattern);
		}
		return self::getInstance();
	}
	
	/**
	 * 设置路由前缀
	 * @param string $prefix
	 * @return self;
	 */
	public static function prefix($prefix){
		if(self::$prefix != ''){
			self::$prefix = implode("/",array(self::$prefix,$prefix));
		}else{
			self::$prefix = $prefix;
		}
		return self::getInstance();
	}
	
	/**
	 * 设置路由别名
	 * @param string $name
	 * @return self;
	 */
	public static function name($name){
		self::$currRoute->alias($name);
		return self::getInstance();
	}	
	/**
	 * 返回路由对象集合
	 * @return array;
	 */
	public static function getRoutes(){
		return self::$routes;
	}
	
	/**
	 * 设置路由分组
	 * @return void;
	 */
	public static function group(){
		$args = func_get_args();
		if(is_array($args[0])){
			if(isset($args[0]['namespace'])){
				self::namespace($args[0]['namespace']);
			}
			
			if(isset($args[0]['prefix'])){
				self::prefix($args[0]['prefix']);
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