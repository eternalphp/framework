<?php

namespace framework\Util\Html;


class Textarea
{
	
	private $name;
	private $id;
	private $value;
	private $type;
	private $options = array();
	
	public function __construct($id,$value = ''){
		$this->id = $id;
		$this->name = $id;
		$this->value = $value;
		$this->type = 'textarea';
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function value($value){
		$this->value = $value;
		return $this;
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
	public function cols($cols){
		$this->options['cols'] = $cols;
		return $this;
	}
	
    /**
     * set attr class
     * @param string $class
     * @return $this
     */
	public function rows($rows){
		$this->options['rows'] = $rows;
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
		return sprintf('<textarea id="%s" name="%s" %s >%s</textarea>',$this->id,$this->name,implode(' ',$attrs),$this->value);
	}
}
?>