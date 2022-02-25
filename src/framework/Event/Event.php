<?php

namespace framework\Event;

class Event {
	
	/*
	 事件名称
	 **/
	protected $name = 'Event';
	
	/*
	 事件处理器
	 **/
	private $listeners = array();
	
	/*
	 事件处理数据
	 **/
	private $payload = array();
	
	public function __construct($payload = array()){
		
		$this->payload = $payload;
		
	}
	
	/**
	 * 绑定事件处理器
	 * @param string $name
	 * @param HandlerInterface | callable $handler
	 * @return $this;
	 */
	public function bind($name,$handler){
		
		if($handler instanceof HandlerInterface){
			$this->listeners[$name] = $handler;
		}elseif(is_callable($handler)){
			$this->listeners[$name] = $handler;
		}else{
			$this->listeners[$name] = app($handler);
		}
		
		return $this;
	}
	
	/**
	 * 获取事件名
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * 获取事件处理器
	 * @param string $name;
	 * @return object;
	 */
	public function getListeners(){
		return $this->listeners;
	}
	
	/**
	 * 获取数据
	 * @return array
	 */
	public function getPayload(){
		return $this->payload;
	}
	
	/**
	 * 获取数据
	 * @return array
	 */
	public function setPayload($payload){
		$this->payload = $payload;
		return $this;
	}
}
?>