<?php

namespace framework\Router;

class Route{
	
	private $uri;
	private $pattern;
	private $callback;
	private $name;
	private $prefix;
	private $params = array();
	private $namespace = 'Home';
	private $methods = ['GET','POST','PUT','DELETE','PATCH','COMMAND'];
	private $paramPatterns = array();
	
	public function __construct($pattern, $callback = null, $methods = []){
		$this->pattern = $pattern;
		$this->uri = $pattern;
		$this->callback = $callback;
		if(is_array($methods) && count($methods) > 0){
			$this->methods = array_map('strtoupper',$methods);
		}
		return $this;
	}
	
	/**
	 * 给路由命名
	 * @param string $name 路由命名
	 * @return $this;
	 */
	public function alias($name){
		$this->name = $name;
		return $this;
	}
	
	/**
	 * 设置路由命名空间
	 * @param string $namespace
	 * @return $this;
	 */
	public function namespace($namespace){
		$this->namespace = $namespace;
		return $this;
	}
	
	/**
	 * 获取路由名称
	 * @return string;
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * 获取路由支持的方法
	 * @return array;
	 */
	public function getMethods(){
		return $this->methods;
	}
	
	/**
	 * 获取路由uri
	 * @return string;
	 */
	public function getUri(){
		return $this->uri;
	}
	
	/**
	 * 获取路由prefix
	 * @return string;
	 */
	public function getPrefix(){
		return $this->prefix;
	}
	
	/**
	 * 获取路由参数
	 * @return array;
	 */
	public function getParams(){
		return $this->params;
	}
	
	/**
	 * 获取路由命名空间
	 * @return string;
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * 获取路由命名空间地址
	 * @return string;
	 */
	public function getNamespacePath(){
		return implode("\\",["App",$this->namespace,'Controllers',$this->getController()]);
	}
	
	/**
	 * 判断是否回调方法
	 * @return bool
	 */
	public function isCallback(){
		return is_callable($this->callback);
	}
	
	/**
	 * 执行回调方法
	 * @return bool
	 */
	public function callback(){
		if(is_callable($this->callback)){
			echo call_user_func($this->callback);
		}else{
			return false;
		}
	}
	
	/**
	 * 获取控制器名称
	 * @return string | bool;
	 */
	public function getController(){
		if(is_string($this->callback)){
			$arr = explode("@",$this->callback);
			return $arr[0];
		}else{
			return false;
		}
	}
	
	/**
	 * 获取控制器的方法
	 * @return string | bool;
	 */
	public function getAction(){
		if(is_string($this->callback)){
			$arr = explode("@",$this->callback);
			return $arr[1];
		}else{
			return false;
		}
	}
	
	/**
	 * 解析路由地址
	 */
	private function parseUrl(){
		$patterns = array();
		if($this->prefix != ''){
			$patterns[] = $this->prefix;
		}
		$params = explode('/',trim($this->pattern,'/'));
		if($params){
			$patterns = array_merge($patterns,$params);
		}
		$this->pattern = implode('/',$patterns);
		foreach($patterns as $k=>$param){
			preg_match('/\{(.*?)\}/',$param,$matchs);
			if($matchs){
				$this->paramPatterns[] = array(
					'name'=>rtrim($matchs[1],'?'),
					'pattern'=>null,
					'required'=>(strstr($matchs[1],'?') != false)?false:true
				);
				unset($patterns[$k]);
			}
		}
		$this->uri = implode('/',$patterns);
		
	}
	
	/**
	 * 设置路由参数规则
	 * @param string $name 参数名
	 * @param string $pattern 规则
	 * @return $this;
	 */
	public function where($name,$pattern){
		if($this->paramPatterns){
			foreach($this->paramPatterns as $k=>$val){
				if($val['name'] == $name){
					$this->paramPatterns[$k]["pattern"] = $pattern;
					break;
				}
			}
		}
		return $this;
	}
	
	/**
	 * 设置路由前缀
	 * @param string $prefix
	 * @return $this;
	 */
	public function prefix($prefix){
		$this->prefix = $prefix;
		return $this;
	}
	
	/**
	 * 验证路由
	 * @param string $url
	 * @param string $method
	 * @return bool;
	 */
	public function match($url,$method){
			
		if(!$this->isAllowMethod($method)){
			return false;
		}
		
		$this->parseUrl();
		
		$patterns = $this->urlToArray($this->pattern);
		$params = $this->urlToArray($this->uri);
		$urlParams = $this->urlToArray($url);
		
		if(count($urlParams) > count($patterns)){
			return false;
		}
		
		
		if($urlParams){
			foreach($urlParams as $k=>$param){
				if(isset($params[$k])){
					if($param != $params[$k]){
						return false;
					}else{
						unset($urlParams[$k]);
					}
				}
			}
		}
		
		$params = array_values($urlParams);
		
		if($this->paramPatterns){
			foreach($this->paramPatterns as $k=>$val){
				if($val['required'] == true){
					if(isset($params[$k])){
						if($val["pattern"] != null){
							if(!preg_match(sprintf("/%s/",$val["pattern"]),$params[$k])){
								return false;
							}
						}
						$this->params[$val['name']] = $params[$k];
					}else{
						return false;
					}
				}else{
					if(isset($params[$k]) && $params[$k] != ''){
						if($val["pattern"] != null){
							if(!preg_match(sprintf("/%s/",$val["pattern"]),$params[$k])){
								return false;
							}
						}
						
						$this->params[$val['name']] = $params[$k];
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * 判断参数方法是否是本路由可接受的
	 * @param $methods string/array HTTP方法
	 * @return bool
	 */
	private function isAllowMethod($methods){
		$methods = array_map('strtoupper',(array)$methods);
		foreach((array)$methods as $method){
			if($method == 'HEAD'){
				$method = 'GET';
			}
			if(in_array($method, $this->methods)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 将URL转换成可用的数组形式
	 */
	private function urlToArray($url){
		$uris = explode("?",$url);
		$uri = $uris[0];
		return preg_split('|(?mi-Us)/+|', trim($uri, '/'));
	}
}
?>