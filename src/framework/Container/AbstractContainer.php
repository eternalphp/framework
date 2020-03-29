<?php

namespace framework\Container;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionException;
use Exception;

abstract class AbstractContainer implements ContainerInterface {
	
	protected $definitions = []; //容器绑定标识
	private $instances = []; //实例化的对象
	private $aliases = [];
	private $extenders = [];
 
	public function __construct($definitions = [])
	{
		foreach ($definitions as $name => $definition) {
			$this->bind($name, $definition);
		}
	}
 
	public function get($name)
	{
		if (!$this->has($name)) {
			throw new Exception("No entry or class found for {$name}");
		}
 
		return $this->make($name);
	}
 
    /**
     * 判断容器中是否存在类及标识
     * @param string $name 类名或者标识
     * @return bool
     */
	public function has($name)
	{
		return isset($this->definitions[$name]);
	}
	
    /**
     * 判断容器中是否存在对象实例
     * @param string $abstract 类名或者标识
     * @return bool
     */
    public function exists(string $abstract)
    {
        $abstract = $this->getAlias($abstract);

        return isset($this->instances[$abstract]);
    }

	/**
	 * create object
	 * @param string $name
	 * @param array $params
	 * @param bool $newInstance
	 * @return object
	 */
	public function make($abstract,$params = array(),$newInstance = false)
	{	
		$abstract = $this->getAlias($abstract);
		
		if (isset($this->instances[$abstract]) && !$newInstance) {
			return $this->instances[$abstract];
		}
		
		if(isset($this->definitions[$abstract])){
			$class = $this->build($this->definitions[$abstract], $params);
		}else{
			$class = $this->build($abstract, $params);
		}

		if(!$newInstance){
			$this->instances[$abstract] = $class;
		}
 
		return $class;
	}
 
	/**
	 * 构造对象
	 * @param string $className
	 * @param array $params
	 * @return object
	 */
	public function build($abstract, $params = array())
	{
		if ($abstract instanceof Closure) {
			
			try {
				$reflect = new ReflectionFunction($abstract);
			} catch (ReflectionException $e) {
				throw new Exception("function not exists: {$abstract}()", $abstract, $e);
			}
			
			if($reflect->getNumberOfParameters() > 0){
				$parameters = $reflect->getParameters(); //获取构造函数的参数列表
				$dependencies = $this->getParametersByDependencies($parameters);
				foreach ($params as $index => $value) {
					$dependencies[$index] = $value;
				}
			}else{
				$params = [];
			}
			
			return call_user_func_array($abstract,$params);
			
		} elseif (is_string($abstract)) {
			
			$class = new ReflectionClass($abstract);
			
			if(!$class->isInstantiable()){
				throw new Exception("Can't instantiate this.");
			}
			
			$constructor = $class->getConstructor();
			if(is_null($constructor)){
				return new $abstract;
			}
			
			$dependencies = $this->getDependencies($class);
			foreach ($params as $index => $value) {
				$dependencies[$index] = $value;
			}
			
			return $class->newInstanceArgs($dependencies);
			
		} elseif (is_object($abstract)) {
			return $abstract;
		}
	}
 
	/**
	 * @param \ReflectionClass $reflection
	 * @return array
	 */
	private function getDependencies($class)
	{
		$dependencies = [];
		$constructor = $class->getConstructor(); //获取构造函数
		if ($constructor !== null) {
			$parameters = $constructor->getParameters(); //获取构造函数的参数列表
			$dependencies = $this->getParametersByDependencies($parameters);
		}
 
		return $dependencies;
	}
	
	/**
	 * 获取方法中相关参数的依赖
	 * @param \ReflectionClass $class
	 * @param string $method
	 * @return array
	 */
	public function getMethodParams($class,$method){
		$dependencies = [];
		$class = new ReflectionClass($class);
		if($class->hasMethod($method)){
			$function = $class->getMethod($method); //获取函数对象
			if ($function !== null) {
				$parameters = $function->getParameters(); //获取函数的参数对象
				$dependencies = $this->getParametersByDependencies($parameters);
			}
		}
		return $dependencies;
	}
 
	/**
	 *
	 * 解析参数对象并自动注入依赖项
	 * @param array $dependencies
	 * @return array $parameters
	 * */
	private function getParametersByDependencies(array $dependencies)
	{
		$parameters = [];
		foreach ($dependencies as $param) {
			$paramName = $param->name;
			if ($param->getClass()) {
				$className = $param->getClass()->name;
				$class = $this->build($className);
				$parameters[$paramName] = $class;
			} elseif ($param->isArray()) {
				if ($param->isDefaultValueAvailable()) {
					$parameters[$paramName] = $param->getDefaultValue();
				} else {
					$parameters[$paramName] = [];
				}
			} elseif ($param->isCallable()) {
				if ($param->isDefaultValueAvailable()) {
					$parameters[$paramName] = $param->getDefaultValue();
				} else {
					$parameters[$paramName] = function ($arg) {
					};
				}
			} else {
				if ($param->isDefaultValueAvailable()) {
					$parameters[$paramName] = $param->getDefaultValue();
				} else {
					if ($param->allowsNull()) {
						$parameters[$paramName] = null;
					} else {
						$parameters[$paramName] = false;
					}
				}
			}
		}
		return $parameters;
	}
	
    /**
     * 设置别名绑定
     * @param  string $name
     * @param  string $abstract
     * @return $this
     */
	public function alias($name,$abstract){
		$this->aliases[$name] = $abstract;
		return $this;
	}
	
    /**
     * 根据别名获取真实类名
     * @param  string $abstract
     * @return string
     */
	public function getAlias($abstract){
		if(isset($this->aliases[$abstract])){
			return $this->aliases[$abstract];
		}
		return $abstract;
	}
	
	/**
	 * @param string $abstract
	 * @param string | array | callable $concrete
	 * @return $this
	 */
	public function bind($abstract, $concrete = null)
	{
		if(is_array($abstract)){
            foreach ($abstract as $key => $val) {
                $this->bind($key, $val);
            }
		}elseif($concrete instanceof Closure){
			$this->bind[$abstract] = $concrete;
		}elseif(is_object($concrete)){
			$this->instance($abstract, $concrete);
		}else{
            $abstract = $this->getAlias($abstract);
            $this->definitions[$abstract] = $concrete;
		}
		return $this;
	}
	
	/**
	 * @param string $abstract
	 * @param array $params
	 * @return void
	 */
	public function singleton($abstract,$params = []){
		return $this->make($abstract,$params,false);
	}
	
	/**
	 * @param string $abstract
	 * @param object $concrete
	 * @return $this
	 */
	public function instance(string $abstract, $instance){
		
		$abstract = $this->getAlias($abstract);
		$this->instances[$abstract] = $instance;
		$this->definitions[$abstract] = $instance;
		return $this;
		
	}

}
?>