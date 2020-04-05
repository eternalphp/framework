<?php

namespace framework\Event;

class Events {
	
	
	static $events = array();
	
	/**
	 * 触发事件
	 * @param string $name
	 * @param callable $callback
	 * @return void
	 */
	public static function trigger($name,callable $callback = null){
		$event = app()->get($name);
		call_user_func($callback,$event);
		self::$events[$name] = $event;
	}
	
	/**
	 * 监听事件,调用事件处理器
	 * @param string $name
	 * @param callable | string $callback
	 * @return void
	 */
	public static function listen($name,$handler = null){
		
		$event = self::$events[$name];
		if(is_callable($handler)){
			
			call_user_func($handler,$event);
			
		}elseif(is_string($handler)){
			
			$event->handler($name,$handler);
			$handlers = $event->getHandler();
			if($handlers){
				foreach($handlers as $name=>$handler){
					$handler->handle($event->getData());
				}
			}
			
		}else{
			
			$handlers = $event->getHandler();
			if($handlers){
				foreach($handlers as $name=>$handler){
					$handler->handle($event->getData());
				}
			}
			
		}
	}
}
?>