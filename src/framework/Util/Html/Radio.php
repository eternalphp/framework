<?php

namespace framework\Util\Html;


class Radio
{
	
	private $name;
	private $id;
	private $value;
	private $type;
	private $options = array();
	private $items;
	private $keys = array('name','value');
	
	public function __construct($id,$items = array(),$value = ''){
		$this->id = $id;
		$this->name = $id;
		$this->value = $value;
		$this->type = 'radio';
		$this->items = $items;
	}
	
    /**
     * set input value
     * @param string $value
     * @return $this
     */
	public function items($items,$keys = array()){
		$this->items = $items;
		$this->keys = array_merge($this->keys,$keys);
		return $this;
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
		
		$items = array();
		if($this->items){
			foreach($this->items as $key=>$val){
				if(is_array($val)){
					$key = $val[$this->keys[0]];
					$value = $val[$this->keys[1]];
					$selectd = ($this->value == $value)?"selected":"";
					if(is_string($key)){
						$items[] = sprintf('<input type="%s" id="%s" name="%s" value="%s" %s %s /> %s',$this->type,$this->id,$this->name,$value,implode(' ',$attrs),$checked,$key);
					}else{
						$items[] = sprintf('<input type="%s" id="%s" name="%s" value="%s" %s %s /> %s',$this->type,$this->id,$this->name,$value,implode(' ',$attrs),$checked,$value);
					}
				}else{
					$checked = ($this->value == $val)?"checked":"";
					if(is_string($key)){
						$items[] = sprintf('<input type="%s" id="%s" name="%s" value="%s" %s %s /> %s',$this->type,$this->id,$this->name,$val,implode(' ',$attrs),$checked,$key);
					}else{
						$items[] = sprintf('<input type="%s" id="%s" name="%s" value="%s" %s %s /> %s',$this->type,$this->id,$this->name,$val,implode(' ',$attrs),$checked,$val);
					}
				}
			}
		}
		
		return implode(" ",$items);
	}
}
?>