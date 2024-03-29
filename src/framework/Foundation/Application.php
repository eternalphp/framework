<?php

namespace framework\Foundation;

use framework\Container\Container;
use framework\Router\Router;
use framework\Config\Repository;
use framework\Filesystem\Filesystem;
use framework\Session\Session;
use framework\Cookie\Cookie;
use framework\Logger\Logger;
use framework\Language\Language;
use framework\Http\Request;
use framework\Http\Response;
use framework\Pipeline\Pipeline;
use framework\Middleware\Middleware;
use Exception;

class Application
{
	
	const VERSION = '3.0.1';
	
	private $container;
	private $basePath;
	private $services = [];
	static $instance = null;
	private $configs;
	private $items;
	
	public function __construct($basePath = ''){
		$this->basePath = $basePath;
		$this->container = Container::getInstance();
	}
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
			defined("ROOT") && self::$instance->setBasePath(ROOT);
		}
		return self::$instance;
	}

    /**
     * load config file
     *
     * @return void
     */
	public function load(){
		
		//加载配置文件
		$this->configs = array();
		$this->container['Filesystem']->getFiles($this->configPath(),function($filename){
			$items = require($filename);
			$name = $this->container['Filesystem']->name($filename);
			$this->configs = array_merge($this->configs,[$name=>$items]);
		});
		
		$this->container->bind('config',new Repository($this->configs));

	}
	
    /**
     * application start
     *
     * @return void
     */
	public function Start($environment = 1){
		
		$this->container->bind('Filesystem',Filesystem::class);
		$this->load();
		$this->loadService();
		$this->loadLanguage();
		$this->loadRoute();
		$this->init();
		
		if($environment == 1){
			$this->dispatch();
		}
		
	}
	
    /**
     * register service to services
     *
	 * @param string $service
	 * @param bool $force
     * @return void
     */
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
            $this->container->bind($service->bind);
        }

        $this->services[] = $service;
	}
	
    /**
     * get value of config
     *
	 * @param string $key
	 * @param string $default
     * @return string
     */
	public function config($key,$default = null){
		return $this->container['config']->get($key,$default);
	}
	
    /**
     * set root path
     *
     * @return string
     */
	public function setBasePath($path){
		$this->basePath = $path;
		return $this;
	}
	
    /**
     * get root path
     *
     * @return string
     */
	public function basePath(){
		return $this->basePath;
	}
	
    /**
     * get config path
     *
	 * @param string $path
     * @return string
     */
	public function configPath($path = ''){
		$paths = array($this->basePath,'configs');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get public path
     *
	 * @param string $path
     * @return string
     */
	public function publicPath($path = ''){
		$paths = array($this->basePath,'public');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get storage path
     *
	 * @param string $path
     * @return string
     */
	public function storagePath($path = ''){
		$paths = array($this->basePath,'storage');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get resource path
     *
	 * @param string $path
     * @return string
     */
	public function resourcePath($path = ''){
		$paths = array($this->basePath,'resource');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get route path
     *
	 * @param string $path
     * @return string
     */
	public function routePath($path = ''){
		$paths = array($this->basePath,'routers');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get app path
     *
	 * @param string $path
     * @return string
     */
	public function appPath($path = ''){
		$paths = array($this->basePath,'app');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get language path
     *
	 * @param string $path
     * @return string
     */
	public function languagePath($path = 'zh'){
		$paths = array($this->basePath,'resource','lang');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * get database path
     *
	 * @param string $path
     * @return string
     */
	public function databasePath($path = ''){
		$paths = array($this->basePath,'database');
		if($path != '') $paths[] = $path;
		return implode(DIRECTORY_SEPARATOR,$paths);
	}
	
    /**
     * application init
     *
     * @return void
     */
	public function init(){
		
		//默认时区
		date_default_timezone_set($this->config('config.date_default_timezone','Asia/Shanghai')); 
		//默认编码
		ini_set('default_charset', $this->config('config.charset','utf-8'));
		//魔法反斜杠转义关闭
		ini_set('magic_quotes_runtime', $this->config('config.magic_quotes_runtime',0));

		$this->container->get('logger')->init();
	}
	
    /**
     * load route config
     *
     * @return void
     */
	public function loadRoute(){
		$this->container['Filesystem']->getFiles($this->routePath(),function($filename){
			require($filename);
		});
	}
	
    /**
     * 路由调度
     *
     * @return void
     */
	public function dispatch(){
		
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
				
				$this->middleware(new Request());
				
				$class = $route->getNamespacePath();
				$this->container->bind('controller',$class);
				$app = $this->container->get('controller');
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
					//header('HTTP/1.1 404 Not Found');
					abort();
				}else{
					exit("No find command");
				}
			}
			
		}catch(Exception $ex){
			throw new Exception($ex->getMessage());
		}
	}
	
	//中间件处理程序
	public function middleware(Request $request){
		$route = $this->container->get('route');
		$Middlewares = $route->getMiddlewares();
		
		if($Middlewares){
			
			try{
			
				$pipeline = new Pipeline();
				$pipeline->send($request);
				
				foreach($Middlewares as $name){
					
					$class = implode("\\",['App','Middleware',$name.'Middleware']);
					if(!class_exists($class)){
						$class = implode("\\",['framwork','Middleware',$name.'Middleware']);
					}
					
					$middleware = new $class;
					
					if(!($middleware instanceof Middleware)){
						throw new Exception("$class is not instanceof Middleware");
					}
					
					//要处理的中间件
					$pipeline->through(function($request,$next) use ($middleware){
						return $middleware->handle($request, $next) ;
					});
					
				}
				
				//默认处理的方法
				$response = $pipeline->then(function() use ($request){
					return new Response();
				});
				
				return $response;
				
			}catch(Exception $ex){
				throw new Exception($ex->getMessage());
			}
		}
	}
	
    /**
     * load language config
     *
     * @return void
     */
	public function loadLanguage(){
 		$this->items = array();
		$this->container['Filesystem']->getFiles($this->languagePath(),function($filename){
			$items = require($filename);
			$name = $this->container['Filesystem']->name($filename);
			$this->items = array_merge($this->items,[$name=>$items]);
		});
		
		$this->container->bind('language',new Language($this->items));
	}
	
    /**
     * load service
     *
     * @return void
     */
	public function loadService(){
		//绑定基本类
		$registers = (array)$this->config("app.aliases");
		if($registers){
			foreach($registers as $key=>$class){
				$this->container->bind($key,$class);
			}
		}
		
		//注册服务
		$services = (array)$this->config("app.providers");
		if($services){
			foreach($services as $service){
				$this->register($service);
			}
		}
	}
	
    /**
     * get service from services
     *
	 * @param string $service
     * @return string
     */
	public function getService($service){
        $name = is_string($service) ? $service : get_class($service);
        return array_values(array_filter($this->services, function ($value) use ($name) {
            return $value instanceof $name;
        }, ARRAY_FILTER_USE_BOTH))[0] ?? null;
	}
	
    /**
     * get version of application
     *
     * @return string
     */
	public function version(){
		return self::VERSION;
	}
}
