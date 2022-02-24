<?php

namespace framework\Util\Html;

class HtmlControl {
	
	private $loader = array();
	
	public function __construct(){
		$this->options = array('Link','Checkbox','Input','Label','Radio','Select','Table','Textarea','File','Calendar');
	}
	
	public function __call($class,$args){
		if(in_array($class,$this->options)){
			return $this->loader($class,$args);
		}else{
			exit("this $class is not exists !");
		}
	}
	
	public function loader($class,$args = array()){
		$namespace_class = implode('\\',array('framework\Util\Html',$class));
		$instance = new \ReflectionClass($namespace_class);
		if(!isset($this->loader[$class])){
			$this->loader[$class] = $instance->newInstanceArgs($args);
		}else{
			call_user_func_array(array($this->loader[$class],'init'),$args);
		}
		return $this->loader[$class];
	}
}
?>