<?php

namespace framework\Util\Html;


class Link
{
	
	private $text;
	private $href;
	private $options = array();
	
	public function __construct($href = '',$text = ''){
		$this->text = $text;
	}
	
    /**
     * set attr class
     * @param string $class
     * @return $this
     */
	public function href($href){
		$this->href = $href;
		return $this;
	}
	
    /**
     * set attr class
     * @param string $class
     * @return $this
     */
	public function text($text){
		$this->text = $text;
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
		$attr = ($attrs)?" ".implode(' ',$attrs):"";
		return sprintf('<a href="%s" %s>%s</a>',$attr,$this->href,$this->text);
	}
}
?>