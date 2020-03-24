<?php

namespace framework\Database\Schema;

class Index
{
	private $name = null;
	private $fields = [];
	private $type = 'index'; //普通索引
	
	public function __construct($field){
		if(is_array($field)){
			$this->fields = $field;
		}else{
			$this->fields[] = $field;
		}
		$this->name = sprintf("%s_%s",$this->type,implode("_",$this->fields));
	}
	
	public function name($name){
		if($name != null) $this->name = $name;
		return $this;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getSection(){
		return sprintf("INDEX %s(%s)",$this->name,implode(",",$this->fields));
	}
}

?>