<?php

namespace framework\Session;

use framework\Session\SessionInterface;
use SessionHandlerInterface;
use framework\Support\Arr;

class Session implements SessionInterface
{
	private $sessionId;
	private $name;
	private $handle;
	private $sessionData;
	
	public function __construct($name, SessionHandlerInterface $handler,$sessionId){
		$this->name = $name;
		$this->handle = $handle;
		$this->sessionId = $sessionId;
		$this->sessionData = $this->getSessionHandle();
		$this->Arr = new Arr($this->sessionData);
	}
	
    /**
     * get session
     *
     * @param string $key
	 * @param string $default
     * @return string
     */
	public function get($key,$default = null){
		return $this->Arr->get($key,$default);
	}
	
    /**
     * put session
     *
     * @param string $key
	 * @param string $value
     * @return bool
     */
	public function put($key,$value){
		$this->Arr->put($key,$value);
		$this->handle->write($this->getId(),serialize($this->sessionData));
	}
	
	public function getSessionHandle(){
		if($data = $this->handle->read($this->getId())){
			$data = unserialize($data);
			if($data != false && !is_null($data) && is_array($data)){
				return $data;
			}
		}
		return array();
	}
	
	public function getId(){
		return $this->sessionId;
	}
	
    /**
     * set sessionId
     *
     * @param string $sessionId
     * @return void
     */
	public function setId($sessionId){
		$this->sessionId = $sessionId;
	}
	
	public function getName(){
		return $this->name;
	}
	
    /**
     * set session name
     *
     * @param string $name
     * @return void
     */
	public function setName($name){
		$this->name = $name;
	}
	
	public function generateSessionId(){
		return session_id();
	}
}
?>