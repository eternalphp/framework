<?php

namespace framework\Util\Html;


class Label
{
	
	private $text;
	private $options = array();
	
	public function __construct($text = ''){
		$this->text = $text;
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
		$attr = ($attrs)?" ".implode(' ',$attrs):"";
		return sprintf('<label%s>%s</label>',$attr,$this->text);
	}
}
?>