<?php

namespace framework\Http;

class Request{
	
	private static $data = [];
	private $get;
	private $post;
	private $cookies;
	private $env;
	
	public function __construct(){
        $this->get = $this->filter($_GET);
        $this->post = $this->filter($_POST, INPUT_POST);
        $this->cookies = $this->filter($_COOKIE, INPUT_COOKIE);
        $this->env = $this->filter($_ENV, INPUT_ENV);
	}
	
	public function getServer($name = null,$defaultVal = null){
		if($name == null){
			return $_SERVER;
		}else{
			if(isset($_SERVER[$name])){
				return $_SERVER[$name];
			}
			
			$name = strtoupper($name);
			if(isset($_SERVER[$name])){
				return $_SERVER[$name];
			}
			
			return $defaultVal;
		}
	}
	
	public function setServer($name,$value = null){
		if($name != ''){
			$name = strtoupper($name);
			$_SERVER[$name] = $value;
		}
	}
	
	public function host(){
		if($this->getServer('HTTP_X_REAL_HOST') != null){
			return $this->getServer('HTTP_X_REAL_HOST');
		}else{
			return $this->getServer("HTTP_HOST");
		}
	}
	
	public function port(){
		return $this->getServer("SERVER_PORT");
	}
	
	public function getIP(){
		if ($this->getServer('HTTP_X_FORWARDED_FOR') != null){
			$arr = explode(',',$this->getServer('HTTP_X_FORWARDED_FOR'));
			$pos = array_search('unknown', $arr);
			if (false !== $pos) {
				unset($arr[$pos]);
			}
			$ip = trim(current($arr));
		}else if ($this->getServer('HTTP_CLIENT_IP') != null){
			$ip = $this->getServer('HTTP_CLIENT_IP');
		}else if ($this->getServer('REMOTE_ADDR') != null){
			$ip = $this->getServer('REMOTE_ADDR');
		}else{
			$ip = null;
		}
		return $ip;
	}
	
	public function url(){
		return $this->getServer('REQUEST_URI');
	}
	
	public function params(){
		return $this->getServer('QUERY_STRING');
	}
	
	public function fullUrl(){
		$prfix = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		return $prfix . $this->host() . $this->url();
	}
	
    /**
     * @param $params
     * @param int $type
     * @return array
     */
    private function filter($params, $type = INPUT_GET)
    {
        $ret = [];
        foreach ($params as $key => $param) {
            $ret[$key] = filter_input($type, $key, FILTER_DEFAULT);
        }
        return $ret;
    }
	
	public function scheme(){
		return $this->isSSL() ? 'https' : 'http';
	}
	
	public function isSSL(){
		if($this->getServer('HTTPS') == 'on' || $this->getServer('HTTPS') == 1){
			return true;
		}elseif($this->getServer('REQUEST_SCHEME') == 'https'){
			return true;
		}elseif($this->port() == '443'){
			return true;
		}elseif($this->getServer('HTTP_X_FORWARDED_PROTO') == 'https'){
			return true;
		}
		return false;
	}
	
	public function isAjax(){
		
		if(!strtolower($this->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest'){
			return false;
		}
		
		return true;
	}
	
	public function method(){
		return $this->getServer('REQUEST_METHOD');
	}
	
	public function isGet(){
		return $this->method() == 'GET';
	}
	
	public function isPost(){
		return $this->method() == 'POST';
	}
	
	public function isPut(){
		return $this->method() == 'PUT';
	}

	public function isDelete(){
		return $this->method() == 'DELETE';
	}
	
	public function getContentType(){
		$s = $this->getServer();
		$contentType = $this->getServer('CONTENT_TYPE');
		$arr = explode(";",$contentType);
		return $arr[0];
	}
	
	public function isCLI(){
		return PHP_SAPI == 'cli';
	}
	
	public function isCGI(){
		return strpos(PHP_SAPI, 'cgi') === 0;
	}
	
	public function protocol(){
		return $this->getServer('SERVER_PROTOCOL');
	}
	
	public function pathinfo(){
		return $this->getServer('PATH_INFO');
	}
	
	public function get($name,$defaultVal = null){
		if(isset($this->get[$name])){
			return $this->get[$name];
		}
		return $defaultVal;
	}
	
	public function form($name,$defaultVal = null){
		if(isset($this->post[$name])){
			return $this->post[$name];
		}
		return $defaultVal;
	}
	
	public function input($name,$defaultVal = null){
		$data = $this->getData();
		if(isset($data[$name])){
			return $data[$name];
		}
		return $defaultVal;
	}
	
	public function getAjaxData(){
		$data = array();
		if($this->isAjax() && $this->getContentType() != 'application/x-www-form-urlencoded'){
			$json = file_get_contents('php://input');
			if($json != ''){
				$data = json_decode($json,true);
			}
		}
		return $data;
	}
	
	public function getData(){
		$data = $this->getAjaxData();
		$data = array_merge($this->get,$this->post,Request::$data,$data);
		return $data;
	}
	
	public function merge(array $input){
		Request::$data = array_merge(Request::$data,$input);
		return $this;
	}
	
	public function all(){
		$data = $this->getData();
		return $data;
	}
	
	public function toJson($data = array()){
		return json_encode($data);
	}
	
	public function toArray($json){
		return json_decode($json,true);
	}
	
	public function agent(){
		return $this->getServer('HTTP_USER_AGENT');
	}
	
	public function language(){
		return $this->getServer('HTTP_ACCEPT_LANGUAGE');
	}
	
	public function encoding(){
		return explode(',',$this->getServer('HTTP_ACCEPT_ENCODING'));
	}
	
	public function accept(){
		return explode(',',$this->getServer('HTTP_ACCEPT'));
	}
	
	public function redirect($url){
		header("location:$url");
		exit;
	}
}
?>