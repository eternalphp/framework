<?php

namespace framework\Database\Schema;

class foreignkeyIndex
{
	private $name = null;
	private $field = null;
	private $type = 'foreignkey'; //外键索引
	private $references = [];
	private $onUpdate = null;
	private $onDelete = null;
	
	public function __construct($field){
		$this->field = $field;
		return $this;
	}
	
	public function name($name){
		if($name != null) $this->name = $name;
		return $this;
	}
	
	public function getType(){
		return $this->type;
	}
	
    /**
     * Define an foreign key
     *
     * @param  string  $table
     * @param  string  $field
     * @return $this
     */
	public function references($table,$field){
		$this->references['table'] = $table;
		$this->references['field'] = $field;
		$this->name = implode("_",['fk',$table,$field]);
		return $this;
	}
	
    /**
     * Define an foreign key on Update
     *
     * @param  string | null  $action
     * @return $this
     */
	public function onUpdate($action = 'CASCADE'){
		$this->onUpdate = $action;
		return $this;
	}
	
    /**
     * Define an foreign key on Delete
     *
     * @param  string | null  $action
     * @return $this
     */
	public function onDelete($action = 'RESTRICT'){
		$this->onDelete = $action;
		return $this;
	}
	
	public function getSection(){
		$line = array();
		$line[] = sprintf("CONSTRAINT %s FOREIGN KEY(%s) REFERENCES %s(%s)",$this->name,$this->field,$this->references['table'],$this->references['field']);
		if($this->onUpdate != null) $line[] = sprintf("ON UPDATE %s",$this->onUpdate);
		if($this->onDelete != null) $line[] = sprintf("ON DELETE %s",$this->onDelete);
		return implode(" ",$line);
	}
}

?>