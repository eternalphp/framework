<?php

namespace framework\Event;

use framework\Exception\InvalidArgumentException;
use framework\Filesystem\Filesystem;

class Dispatcher {
	
	
	static $instance = null;
	
	private $events = array();
	
	private $running = true;
	
	public function __construct(){
		
	}
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 触发事件
	 * @param string $name
	 * @param callable $callback
	 * @return void
	 */
	public function trigger(Event $event){

		$path = storage_path(implode("/",['events',$event->getName()]));
		
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		
		file_put_contents($path . '/' . uniqid(time()) ,serialize($event));
		
		return $this;
	}
	
	/**
	 * 判断事件是否已存在
	 * @param string $name
	 * @return bool
	 */
	public function hasEvent($name){
		return isset($this->events[$name]) ? true : false ;
	}
	
	/**
	 * 监听事件,调用事件处理器
	 * @return void
	 */
	public function listen(callable $callback = null){

        $Filesystem = new Filesystem();
		while($this->running){

			$Filesystem->getFiles(storage_path("events"),function($file,$type){
				if($type == 'file'){
					$event = unserialize(file_get_contents($file));
					$this->events[] = $event;
					unlink($file);
				}
			});
			
			if($this->events){
				
				$event = array_shift($this->events);
				
				if($callback != null){
					call_user_func($callback,$event);
				}
				
				$this->dispatch($event);
			}
			
			sleep(1);
		}
	}
	
	/*
	 * 获取事件处理器，调用事件处理器
	 * @param HandlerInterface $event
	 * @return void
	 **/
	public function fire(Event $event){
		$this->dispatch($event);
	}
	
	/*
	 * 事件调度
	 * @param $event
	 * @param array $payload
	 * @return void
	 **/
	private function dispatch(Event $event){
		
		$listeners = $event->getListeners();
		
		if($listeners){
			foreach($listeners as $name => $listener){
				$listener->handle($event);
			}
		}
	}
}
?>