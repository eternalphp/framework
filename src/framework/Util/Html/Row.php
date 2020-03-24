<?php

namespace framework\Util\Html;


class Row
{
	
	private $columns = array();
	private $options = array();
	
	public function __construct(){
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
	
	public function addColumn(callable $callback){
		$column = new Column();
		call_user_func($callback,$column);
		$this->columns[] = $column;
		return $this;
	}
	
	public function getColumn(){
		$column = new Column();
		$this->columns[] = $column;
		return $column;
	}
	
	public function getColumns(){
		return $this->columns;
	}
	
	public function create(){
		$attrs = array();
		foreach($this->options as $key=>$val){
			if($val !=''){
				$attrs[] = sprintf('%s="%s"',$key,$val);
			}
		}
		
		$columns = array();
		if($this->columns){
			foreach($this->columns as $column){
				$columns[] = $column->create();
			}
		}
		$attr = ($attrs)?" ".implode(' ',$attrs):"";
		return sprintf("\t<tr%s>\n%s\n\t</tr>",$attr,implode("\n",$columns));
	}
}
?>