<?php

namespace framework\Util\Html;


class Table
{
	
	private $rows = array();
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
	
	public function addRow(callable $callback){
		$row = new Row();
		call_user_func($callback,$row);
		$this->rows[] = $row;
		return $this;
	}
	
	public function getRows(){
		return $this->rows;
	}
	
	public function section($title){
		$row = new Row();
		$this->rows[] = $row;
		$row->getColumn()->text($title)->class("tableleft");
		return $row->getColumn();
	}
	
	public function create(){
		$attrs = array();
		foreach($this->options as $key=>$val){
			if($val !=''){
				$attrs[] = sprintf('%s="%s"',$key,$val);
			}
		}
		
		$rows = array();
		if($this->rows){
			foreach($this->rows as $row){
				$rows[] = $row->create();
			}
		}
		$attr = ($attrs)?" ".implode(' ',$attrs):"";
		return sprintf("<table%s>\n%s\n</table>",$attr,implode("\n",$rows));
	}
}
?>