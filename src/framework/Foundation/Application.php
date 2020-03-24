<?php

namespace framework\Foundation;

use framework\Container\Container;
use framework\Router\Router;
use framework\Config\Config;
use framework\Filesystem\Filesystem;


class Application
{
	private $container;
	private $basePath;
	
	public function __construct($basePath = null){
		
		$this->basePath = $basePath;
		$this->container = Container::getInstance();
		$this->register();
		$this->init();
		$this->load();
	}

	public function load(){
		$this->configs = array();
		$this->container['Filesystem']->getFiles($this->configPath(),function($filename){
			$items = require($filename);
			$this->configs = array_merge($this->configs,$items);
		});
		
		$this->container->bind('config',new Config($this->configs));
	}
	
	public function Start(){
		
		$this->container['Filesystem']->getFiles($this->routePath(),function($filename){
			require($filename);
		});
		
		try{
			
			if(php_sapi_name() == 'cli'){
				$argv = $_SERVER["argv"];
				array_shift($argv);
				$route = Router::query($argv[0],'COMMAND');
			}else{
				$route = Router::query($_SERVER["REQUEST_URI"],$_SERVER["REQUEST_METHOD"]);
			}
			
			if($route){
				if($route->isCallback()){
					return $route->callback();
				}else{
					$controller = $route->getController();
					$method = $route->getAction();
					$this->container->instance('route',$route);
				}
				
				$class = $route->getNamespacePath();
				$this->container->bind($class,new $class());
				$app = $this->container->get($class);
				if(method_exists($app,$method)){
					
					//解析方法中的参数
					$methodParams = $this->container->getMethodParams($route->getNamespacePath(),$method);
					$params = $route->getParams();
					if($params){
						$methodParams = array_merge($methodParams,$params);
					}
					
					$before_method = sprintf("before_%s",$method);
					$after_method = sprintf("after_%s",$method);
					if(method_exists($app,$before_method)){
						call_user_func(array($app,$before_method),[]);
					}
					
					call_user_func_array(array($app,$method),$methodParams);//支持自动传参
					
					if(method_exists($app,$after_method)){
						call_user_func(array($app,$after_method),[]);
					}
				}else{
					throw new Exception("$controller can not found $method");
				}
				
			}else{
				if(php_sapi_name() != 'cli'){
					header('HTTP/1.1 404 Not Found');
				}else{
					exit("No find command");
				}
			}
			
		}catch(Exception $e){
			$e->showError();
		}
	}
	
	public function register(){
		$registers = array('Filesystem');
		foreach($registers as $class){
			$this->container->bind($class,new $class());
		}
	}
	
	public function config($key,$default = null){
		return $this->container['config']->get($key,$default);
	}
	
	public function basePath(){
		return $this->basePath;
	}
	
	public function configPath($path = ''){
		$paths = array($this->basePath,'configs');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function publicPath($path = ''){
		$paths = array($this->basePath,'public');
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function storagePath(){
		$paths = array($this->basePath,'storage');
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function resourcePath($path = ''){
		$paths = array($this->basePath,'resource');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function routePath($path = ''){
		$paths = array($this->basePath,'routes');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function init(){
		date_default_timezone_set($this->config('date_default_timezone','Asia/Shanghai')); //默认时区
		ini_set('default_charset', $this->config('charset','utf-8')); //默认编码
		ini_set('magic_quotes_runtime', $this->config('magic_quotes_runtime',1)); //魔法反斜杠转义关闭
	}
}
