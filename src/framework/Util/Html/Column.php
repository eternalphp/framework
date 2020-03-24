<?php

namespace framework\Util\Html;


class Column
{
	
	private $text;
	private $sections = array();
	private $options = array();
	
	public function __construct($text = ''){
		$this->text = $text;
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
	
	public function input($id,callable $callback){
		$input = new Input($id);
		call_user_func($callback,$input);
		$this->sections[] = $input;
		return $this;
	}
	
	public function file($id,callable $callback){
		$file = new File($id);
		call_user_func($callback,$file);
		$this->sections[] = $file;
		return $this;
	}
	
	public function label($text,callable $callback){
		$label = new Label($text);
		call_user_func($callback,$label);
		$this->sections[] = $label;
		return $this;
	}
	
	public function textarea($id,callable $callback){
		$textarea = new Textarea($id);
		call_user_func($callback,$textarea);
		$this->sections[] = $textarea;
		return $this;
	}
	
	public function checkbox($id,$items = array(),callable $callback){
		$checkbox = new Checkbox($id,$items);
		call_user_func($callback,$checkbox);
		$this->sections[] = $checkbox;
		return $this;
	}
	
	public function radio($id,$items = array(),callable $callback){
		$radio = new Radio($id,$items);
		call_user_func($callback,$radio);
		$this->sections[] = $radio;
		return $this;
	}
	
	public function select($id,$items = array(),callable $callback){
		$select = new Select($id,$items);
		call_user_func($callback,$select);
		$this->sections[] = $select;
		return $this;
	}
	
	public function button($id,callable $callback){
		$button = new Button($id);
		call_user_func($callback,$button);
		$this->sections[] = $button;
		return $this;
	}
	
	public function submit($id,callable $callback){
		$submit = new Submit($id);
		call_user_func($callback,$submit);
		$this->sections[] = $submit;
		return $this;
	}
	
	public function create(){
		$attrs = array();
		foreach($this->options as $key=>$val){
			if($val !=''){
				$attrs[] = sprintf('%s="%s"',$key,$val);
			}
		}
		
		$sections = array();
		if($this->sections){
			foreach($this->sections as $input){
				$sections[] = $input->create();
			}
		}
		$attr = ($attrs)?" ".implode(' ',$attrs):"";
		return sprintf("\t\t<td%s>%s%s</td>",$attr,$this->text,implode("",$sections));
	}
}
?>