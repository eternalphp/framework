<?php

namespace framework\Database\Schema;

class primaryIndex
{
	private $field = null;
	private $type = 'primary'; //主键索引
	
	public function __construct($field){
		$this->field = $field;
		return $this;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getSection(){
		return sprintf("PRIMARY KEY (%s)",$this->field);
	}
}

?>