<?php

namespace framework\Container;

use Closure;
use ReflectionClass;
use Exception;

abstract class AbstractContainer implements ContainerInterface {
	
	protected $definitions = [];
	private $instances = []; //实例化的对象
	private $aliases = [];
	private $extenders = [];
 
	public function __construct($definitions = [])
	{
		foreach ($definitions as $id => $definition) {
			$this->bind($id, $definition);
		}
	}
 
	public function get($id)
	{
		if (!$this->has($id)) {
			throw new Exception("No entry or class found for {$id}");
		}
 
		$instance = $this->make($id);
 
		return $instance;
	}
 
	public function has($id)
	{
		return isset($this->definitions[$id]);
	}

	/**
	 * create object
	 * @param $name
	 * @return object
	 */
	public function make($name,$params = [])
	{
		if (isset($this->instances[$name])) {
			return $this->instances[$name];
		}
 
		$definition = $this->definitions[$name];
		if (is_array($definition) && isset($definition['class'])) {
			$params = $definition;
			$definition = $definition['class'];
			unset($params['class']);
		}
		$class = $this->build($definition, $params);
 
		return $this->instances[$name] = $class;
	}
 
	public function build($className, array $params = [])
	{
		if ($className instanceof Closure) {
			
			return $className($this);
			
		} elseif (is_string($className)) {
			
			$class = new ReflectionClass($className);
			
			if(!$class->isInstantiable()){
				throw new Exception("Can't instantiate this.");
			}
			
			$constructor = $class->getConstructor();
			if(is_null($constructor)){
				return new $className;
			}
			
			$dependencies = $this->getDependencies($class);
			foreach ($params as $index => $value) {
				$dependencies[$index] = $value;
			}
			
			return $class->newInstanceArgs($dependencies);
			
		} elseif (is_object($className)) {
			return $className;
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
	 * @param \ReflectionClass $reflection
	 * @return array
	 */
	public function getMethodParams($class,$method){
		$dependencies = [];
		$class = new ReflectionClass($class);
		if($class->hasMethod($method)){
			$constructor = $class->getMethod($method); //获取函数对象
			if ($constructor !== null) {
				$parameters = $constructor->getParameters(); //获取构造函数的参数列表
				$dependencies = $this->getParametersByDependencies($parameters);
			}
		}
		return $dependencies;
	}
 
	/**
	 *
	 * 获取构造类相关参数的依赖
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
	
	public function alias($name,$className){
		$this->aliases[$name] = $className;
	}
	
	public function getAlias($name){
		if(isset($this->aliases[$name])){
			return $this->aliases[$name];
		}
	}
	
	/**
	 * @param string $id
	 * @param string | array | callable $concrete
	 * @throws ContainerException
	 */
	public function bind($id, $className)
	{
		if (!is_string($id)) {
			throw new Exception(sprintf(
				'The id parameter must be of type string, %s given',
				is_object($id) ? get_class($id) : gettype($id)
			));
		}
 
		if (is_array($className) && !isset($className['class'])) {
			throw new Exception('数组必须包含类定义');
		}
 
		$this->definitions[$id] = $className;
	}
	
	/**
	 * @param string $id
	 * @param string | array | callable $concrete
	 * @throws ContainerException
	 */
	public function singleton($id,$className){
		
		if(!isset($this->definitions[$id])){
			$this->bind($id, $className);
		}
	}
	
	/**
	 * @param string $id
	 * @param string | array | callable $concrete
	 * @throws ContainerException
	 */
	public function instance($id,$class){
		
		if (!is_string($id)) {
			throw new Exception(sprintf(
				'The id parameter must be of type string, %s given',
				is_object($id) ? get_class($id) : gettype($id)
			));
		}
 
		if (!is_object($class)) {
			throw new Exception('必须是实例对象');
		}
 
		$this->definitions[$id] = $class;
		$this->instances[$id] = $class;
		
	}

}
?>