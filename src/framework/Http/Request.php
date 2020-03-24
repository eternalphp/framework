<?php

namespace framework\Http;

class Request{
	
	public function __construct(){

	}
	
    /**
     * get value from Server
     *
     * @param  string  $name
     * @param  string  $defaultVal
     * @return string | array
     */
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
	
    /**
     * set value to Server
     *
     * @param  string  $name
     * @param  string  $value
     * @return void
     */
	public function setServer($name,$value = null){
		if($name != ''){
			$name = strtoupper($name);
			$_SERVER[$name] = $value;
		}
	}
	
    /**
     * get host
     *
     * @return string
     */
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
		if($this->scheme() == 'https'){
			return 'https://'.$this->host().$this->url();
		}else{
			return 'http://'.$this->host().$this->url();
		}
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
		if(strtolower($this->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest'){
			return true;
		}
		return false;
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
		if(isset($_GET[$name])){
			return $_GET[$name];
		}
		return $defaultVal;
	}
	
	public function input($name,$defaultVal = null){
		if(isset($_POST[$name])){
			return $_POST[$name];
		}
		return $defaultVal;
	}
	
	public function toJson($data = array()){
		return json_encode($data);
	}
	
	public function toArray($json){
		return json_decode($json,true);
	}
	
	public function all($name = null,$defaultVal = null){
		if($name == null){
			return $_REQUEST;
		}else{
			if(isset($_REQUEST[$name])){
				return $_REQUEST[$name];
			}
			return $defaultVal;
		}
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
	}
}
?>