<?php

namespace framework\Event;

class Event {
	
	private $name = 'Event';
	private $handlers = array();
	private $calendar = null;
	private $data = array();
	
	public function __construct($data = array()){
		$this->data = $data;
	}
	
	/**
	 * 设置事件名称
	 * @param string $name
	 * @return $this;
	 */
	public function name($name){
		$this->name = $name;
		return $this;
	}
	
	/**
	 * 绑定事件处理器
	 * @param string $name
	 * @param HandlerInterface | callable $handler
	 * @return $this;
	 */
	public function handler($name,$handler){
		if($handler instanceof HandlerInterface){
			$this->handlers[$name] = $handler;
		}elseif(is_callable($handler)){
			$this->handlers[$name] = $handler;
		}else{
			$this->handlers[$name] = app($handler);
		}
		
		return $this;
	}
	
	/**
	 * 设置数据
	 * @param array $data
	 * @return $this;
	 */
	public function data($data = array()){
		$this->data = $data;
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
	public function getHandler(){
		return $this->handlers;
	}
	
	/**
	 * 获取数据
	 * @return array
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * 获取日历对象
	 * @return object
	 */
	public function getCalendar(){
		return $this->calendar;
	}
	
	/**
	 * 设置日历对象
	 * @return object
	 */
	public function calendar($calendar){
		$this->calendar = $calendar;
		return $this;
	}
}
?>