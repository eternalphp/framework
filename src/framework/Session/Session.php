<?php

namespace framework\Session;

use framework\Session\SessionManager;
use framework\Support\Arr;

class Session
{
	private $sessionId;
	private $name;
	private $handle;
	private $sessionData;
	private $prefix = 'sess';
	
	public function __construct(SessionManager $handler){
		$this->name = 'PHPSESSID';
		$this->handle = $handler->getDefaultDriver();
		$this->sessionId = session_id();
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
		$this->Arr->set($key,$value);
		$this->sessionData = $this->Arr->get();
		$this->handle->write($this->getId(),serialize($this->sessionData));
	}
	
    /**
     * put session
     *
     * @param string $key
	 * @param string $value
     * @return bool
     */
	public function remove($key){
		$this->Arr->remove($key);
		$this->sessionData = $this->Arr->get();
		$this->handle->write($this->getId(),serialize($this->sessionData));
		return true;
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
		return implode("_",[$this->prefix,$this->sessionId]);
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