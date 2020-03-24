<?php

namespace framework\Database\Schema;

class fulltextIndex
{
	private $name = null;
	private $fields = [];
	private $type = 'fulltext'; //全文索引
	
	public function __construct($field){
		if(is_array($field)){
			$this->fields = $field;
		}else{
			$this->fields[] = $field;
		}
		$this->name = sprintf("%s_%s",$this->type,implode("_",$this->fields));
		return $this;
	}
	
	public function name($name){
		if($name != null) $this->name = $name;
		return $this;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getSection(){
		return sprintf("FULLTEXT %s(%s)",$this->name,implode(",",$this->fields));
	}
}

?>