<?php

namespace framework\Foundation;

use framework\Container\Container;
use framework\Router\Router;
use framework\Config\Repository;
use framework\Filesystem\Filesystem;
use framework\Session\Session;
use framework\Cookie\Cookie;

class Application
{
	
	const VERSION = '3.0.1';
	
	private $container;
	private $basePath;
	private $services = [];
	
	public function __construct(){
		$this->basePath = ROOT;
		$this->container = Container::getInstance();
		$this->container->bind('Filesystem',Filesystem::class);
		$this->load();
		$this->init();
	}

	public function load(){
		$this->configs = array();
		$this->container['Filesystem']->getFiles($this->configPath(),function($filename){
			$items = require($filename);
			$name = $this->container['Filesystem']->name($filename);
			$this->configs = array_merge($this->configs,[$name=>$items]);
		});
		
		$this->container->bind('config',new Repository($this->configs));
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
	
	public function register($service,$force = false){
		
        $registered = $this->getService($service);

        if ($registered && !$force) {
            return $registered;
        }

        if (is_string($service)) {
            $service = new $service($this);
        }

        if (method_exists($service, 'register')) {
            $service->register();
        }

        if (property_exists($service, 'bind')) {
            $this->bind($service->bind);
        }

        $this->services[] = $service;
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
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function storagePath($path = ''){
		$paths = array($this->basePath,'storage');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function resourcePath($path = ''){
		$paths = array($this->basePath,'resource');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function routePath($path = ''){
		$paths = array($this->basePath,'routers');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function appPath($path = ''){
		$paths = array($this->basePath,'app');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
	public function init(){
		
		//默认时区
		date_default_timezone_set($this->config('config.date_default_timezone','Asia/Shanghai')); 
		//默认编码
		ini_set('default_charset', $this->config('config.charset','utf-8'));
		//魔法反斜杠转义关闭
		ini_set('magic_quotes_runtime', $this->config('config.magic_quotes_runtime',1)); 
		
		$registers = array(
			'cookie'  => Cookie::class
		);
		foreach($registers as $key=>$class){
			$this->container->bind($key,$class);
		}
		
		$this->container['Filesystem']->getFiles($this->appPath('Services'),function($filename){
			$services = require($filename);
			if($services){
				foreach($services as $server){
					$this->register($server);
				}
			}
		});
	}
	
	public function getService($service){
        $name = is_string($service) ? $service : get_class($service);
        return array_values(array_filter($this->services, function ($value) use ($name) {
            return $value instanceof $name;
        }, ARRAY_FILTER_USE_BOTH))[0] ?? null;
	}
}
