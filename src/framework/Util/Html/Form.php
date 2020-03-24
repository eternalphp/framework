<?php

namespace framework\Util\Html;


class Form
{
	
	private $name;
	private $id;
	private $action;
	private $method;
	private $type;
	private $options = array();
	
	public function __construct($id,$action = ''){
		$this->id = $id;
		$this->name = $id;
		$this->value = $value;
		$this->type = 'form';
		$this->method = 'post';
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function name($name){
		$this->name = $name;
		return $this;
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function target($target){
		$this->options['target'] = $target;
		return $this;
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function method($method){
		$this->method = $method;
		return $this;
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function action($action){
		$this->action = $action;
		return $this;
	}
	
    /**
     * set attr class
     * @param string $class
     * @return $this
     */
	public function class($class){
		$this->options['class'] = $class;
		return $this;
	}
	
    /**
     * set attr class
     * @param string $class
     * @return $this
     */
	public function attr($name,$value){
		$this->options[$name] = $value;
		return $this;
	}
	
	public function create(){
		$attrs = array();
		foreach($this->options as $key=>$val){
			if($val !=''){
				$attrs[] = sprintf('%s="%s"',$key,$val);
			}
		}
		return sprintf('<form id="%s" name="%s" action="%s" method="%s"  %s />@yield("form")</form>',$this->id,$this->name,$this->action,$this->method,implode(' ',$attrs));
	}
}
?>