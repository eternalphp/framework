<?php

namespace framework\Cookie;

use framework\Support\Arr;

class Cookie
{
	private $path;
	private $domain;
	private $secure = false;
	private $minutes = 120;
	private $httponly = false;
	
	public function __construct(){
		$this->path = '/';
	}
	
    /**
     * set path
     *
     * @param string $path
     * @return object $this
     */
	public function path($path){
		$this->path = $path;
		return $this;
	}
	
    /**
     * set domain
     *
     * @param string $domain
     * @return object $this
     */
	public function domain($domain){
		$this->domain = $domain;
		return $this;
	}
	
    /**
     * set secure
     *
     * @return object $this
     */
	public function secure(){
		$this->secure = true;
		return $this;
	}
	
    /**
     * set expire
     *
     * @param int $minutes
     * @return object $this
     */
	public function expire($minutes){
		$this->minutes = $minutes;
		return $this;
	}
	
    /**
     * set httponly
     *
     * @return object $this
     */
	public function httponly(){
		$this->httponly = true;
		return $this;
	}
	
    /**
     * save cookie
     *
     * @param string $name
     * @param string $value
     * @return object $this
     */
	public function save($name,$value){
		setcookie($name,$value,time() + $this->minutes*60,$this->path,$this->domain,$this->secure,$this->httponly);
	}
	
	public function get($name,$default = null){
		return isset($_COOKIE[$name])?$_COOKIE[$name]:$default;
	}
	
	public function destroy($name){
		setcookie($name, "", time()-3600);
	}
}
?>