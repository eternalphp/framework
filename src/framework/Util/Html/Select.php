<?php

namespace framework\Util\Html;


class Select
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
		$this->type = 'select';
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
		
		$items = array();
		if($this->items){
			foreach($this->items as $key=>$val){
				if(is_array($val)){
					$key = $val[$this->keys[0]];
					$value = $val[$this->keys[1]];
					$selectd = ($this->value == $value)?"selected":"";
					if(is_string($key)){
						$items[] = sprintf('<option value="%s" %s>%s</option>',$value,$selectd,$key);
					}else{
						$items[] = sprintf('<option value="%s" %s>%s</option>',$value,$selectd,$value);
					}
				}else{
					$selectd = ($this->value == $val)?"selected":"";
					if(is_string($key)){
						$items[] = sprintf('<option value="%s" %s>%s</option>',$val,$selectd,$key);
					}else{
						$items[] = sprintf('<option value="%s" %s>%s</option>',$val,$selectd,$val);
					}
				}
			}
		}
		
		return sprintf("<select id=\"%s\" name=\"%s\" %s>%s</select>",$this->id,$this->name,implode(' ',$attrs),implode("",$items));
	}
}
?>