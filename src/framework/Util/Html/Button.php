<?php

namespace framework\Util\Html;


class Button
{
	
	private $name;
	private $id;
	private $text;
	private $type;
	private $options = array();
	
	public function __construct($id,$text = ''){
		$this->id = $id;
		$this->name = $id;
		$this->text = $text;
		$this->type = 'button';
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function text($text){
		$this->text = $text;
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
	public function size($size){
		$this->options['size'] = $size;
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
		$attr = ($attrs)?" ".implode(' ',$attrs):"";
		return sprintf('<button type="%s" id="%s" name="%s" %s>%s</button>',$this->type,$this->id,$this->name,$attr,$this->text);
	}
}
?>