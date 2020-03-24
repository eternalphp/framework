<?php

namespace framework\Database\Schema;

use framework\Database\Schema\Index;
use framework\Database\Schema\primaryIndex;
use framework\Database\Schema\uniqueIndex;
use framework\Database\Schema\fulltextIndex;
use framework\Database\Schema\foreignkeyIndex;

class Field
{
	private $name = '';
	private $alias = null;
	private $type = '';
	private $value = null;
	private $length = null;
	private $places = null;
	private $comment = '';
	private $autoIncrement = '';
	private $charset = '';
	private $nullable = 'NOT NULL';
	private $unsigned = '';
	private $enumList = '';
	private $primaryKey = null;
	private $index = null;
	
	public function __construct(){
		
	}
	
    /**
     * Get the field name
     *
     * @return string
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * Setting field aliases
     *
     * @return $this
     */
	public function alias($name){
		$this->alias = $name;
		return $this;
	}
	
    /**
     * Get the field alias
     *
     * @return string
     */
	public function getAlias(){
		return $this->alias;
	}
	
    /**
     * Define fields of type increments
     *
     * @param  string | null  $name
     * @return $this
     */
	public function increments($name = null){
		if($name != null){
			$this->name = $name;
			$this->type = 'int';
			$this->length = '11';
		}
		$this->autoIncrement = 'AUTO_INCREMENT';
		$this->index = new primaryIndex($this->name);
		return $this;
	}
	
    /**
     * Define fields of type bigInt
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function bigInt($name,$length = 20){
		$this->name = $name;
		$this->type = 'bigint';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type smallInt
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function smallInt($name,$length = 6){
		$this->name = $name;
		$this->type = 'smallint';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type mediumInt
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function mediumInt($name,$length = 9){
		$this->name = $name;
		$this->type = 'mediumint';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type int
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function integer($name,$length = 11){
		$this->name = $name;
		$this->type = 'int';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type tinyint
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function tinyInt($name,$length = 4){
		$this->name = $name;
		$this->type = 'tinyint';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type string
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function string($name,$length = 255){
		$this->name = $name;
		$this->type = 'varchar';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type char
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function char($name,$length = 255){
		$this->name = $name;
		$this->type = 'char';
		$this->length = $length;
		return $this;
	}
	
    /**
     * Define fields of type text
     *
     * @param  string | null  $name
     * @return $this
     */
	public function text($name){
		$this->name = $name;
		$this->type = 'text';
		return $this;
	}
	
    /**
     * Define fields of type mediumtext
     *
     * @param  string | null  $name
     * @return $this
     */
	public function mediumText($name){
		$this->name = $name;
		$this->type = 'mediumtext';
		return $this;
	}
	
	
    /**
     * Define fields of type longtext
     *
     * @param  string | null  $name
     * @return $this
     */
	public function longText($name){
		$this->name = $name;
		$this->type = 'longtext';
		return $this;
	}
	
    /**
     * Define fields of type datetime
     *
     * @param  string | null  $name
     * @return $this
     */
	public function datetime($name){
		$this->name = $name;
		$this->type = 'datetime';
		return $this;
	}
	
    /**
     * Define fields of type date
     *
     * @param  string | null  $name
     * @return $this
     */
	public function date($name){
		$this->name = $name;
		$this->type = 'date';
		return $this;
	}
	
    /**
     * Define fields of type year
     *
     * @param  string | null  $name
     * @return $this
     */
	public function year($name){
		$this->name = $name;
		$this->type = 'year';
		return $this;
	}
	
    /**
     * Define fields of type time
     *
     * @param  string | null  $name
     * @return $this
     */
	public function time($name){
		$this->name = $name;
		$this->type = 'time';
		return $this;
	}
	
    /**
     * Define fields of type timestamp
     *
     * @param  string | null  $name
     * @return $this
     */
	public function timestamp($name){
		$this->name = $name;
		$this->type = 'timestamp';
		$this->value = 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
		return $this;
	}
	
    /**
     * Define fields of type float
     *
     * @param  string | null  $name
     * @param  int $length
     * @param  int $places
     * @return $this
     */
	public function float($name, $length = 8, $places = 2){
		$this->name = $name;
		$this->type = 'float';
		$this->length = $length;
		$this->places = $places;
		return $this;
	}
	
    /**
     * Define fields of type double
     *
     * @param  string | null  $name
     * @param  int $length
     * @param  int $places
     * @return $this
     */
	public function double($name, $length = 8, $places = 2){
		$this->name = $name;
		$this->type = 'double';
		$this->places = $places;
		return $this;
	}
	
    /**
     * Define fields of type decimal
     *
     * @param  string | null  $name
     * @param  int $length
     * @param  int $places
     * @return $this
     */
	public function decimal($name, $length = 8, $places = 2){
		$this->name = $name;
		$this->type = 'decimal';
		$this->places = $places;
		return $this;
	}
	
    /**
     * Define fields of type enum
     *
     * @param  string | null  $name
     * @param  array $list
     * @return $this
     */
	public function enum($name,$list = array()){
		$this->name = $name;
		$this->type = 'enum';
		$this->enumList = implode(",",$list);
		return $this;
	}
	
	public function primary(){
		$this->index = new primaryIndex($this->name);
		return $this;
	}
	
    /**
     * Define an index
     *
     * @param  string | null  $name
     * @return $this
     */
	public function index($name = null){
		$this->index = (new Index($this->name))->name($name);
		return $this;
	}
	
    /**
     * Define an unique index
     *
     * @param  string | null  $name
     * @return $this
     */
	public function unique($name = null){
		$this->index = (new uniqueIndex($this->name))->name($name);
		return $this;
	}
	
    /**
     * Define an fulltext
     *
     * @param  string | null  $name
     * @return $this
     */
	public function fulltext($name = null){
		$this->index = (new fullIndex($this->name))->name($name);
		return $this;
	}
	
    /**
     * Define an foreign key
     *
     * @param  string | null  $name
     * @return $this
     */
	public function foreignkey($name = null){
		$this->index = (new foreignkeyIndex($this->name))->name($name);
		return $this;
	}
	
    /**
     * Define an foreign key
     *
     * @param  string  $table
     * @param  string  $field
     * @return $this
     */
	public function references($table,$field){
		$this->index->references($table,$field);
		return $this;
	}
	
    /**
     * Define an foreign key on Update
     *
     * @param  string | null  $action
     * @return $this
     */
	public function onUpdate($action = 'CASCADE'){
		$this->index->onUpdate($action);
		return $this;
	}
	
    /**
     * Define an foreign key on Delete
     *
     * @param  string | null  $action
     * @return $this
     */
	public function onDelete($action = 'RESTRICT'){
		$this->index->onDelete($action);
		return $this;
	}
	
    /**
     * Setting field nullable
     *
     * @return $this
     */
	public function nullable(){
		$this->nullable = 'NULL';
		return $this;
	}
	
    /**
     * Setting field unsigned
     *
     * @return $this
     */
	public function unsigned(){
		$this->unsigned = 'unsigned';
		return $this;
	}
	
    /**
     * Set default values for fields
     *
     * @param  string | int  $value
     * @return $this
     */
	public function defaultVal($value){
		$this->value = $value;
		return $this;
	}
	
    /**
     * Setup field description
     *
     * @param  string  $text
     * @return $this
     */
	public function comment($text){
		$this->comment = $text;
		return $this;
	}
	
    /**
     * Getting field Remarks
     *
     * @return string
     */
	public function getComment(){
		return $this->comment;
	}
	
    /**
     * Getting field type
     *
     * @return string
     */
	public function getType(){
		return $this->type;
	}
	
    /**
     * Setting field encoding
     *
     * @param  string  $charset
     * @return $this
     */
	public function charset($charset){
		$this->charset = $charset;
		return $this;
	}
	
	public function getIndex(){
		return $this->index;
	}
	
	public function getSection(){
		$sections = array();
		$sections[] = sprintf("`%s`",($this->alias != null) ? $this->alias : $this->name);
		if($this->length != null && $this->places != null){
			$sections[] = sprintf("%s(%d,%d)",$this->type,$this->length,$this->places);
		}elseif($this->length != null){
			$sections[] = sprintf("%s(%d)",$this->type,$this->length);
		}else{
			$sections[] = sprintf("%s",$this->type);
		}
		if($this->charset != '') $sections[] = sprintf("CHARACTER SET %s",$this->charset);
		if($this->unsigned != '') $sections[] = $this->unsigned;
		if($this->value !== null){
			if($this->type == 'timestamp'){
				$sections[] = sprintf("NOT NULL DEFAULT %s",$this->value);
			}else{
				$sections[] = sprintf("DEFAULT '%s'",$this->value);
			}
		}else{
			if($this->nullable == 'NULL'){
				if($this->type != 'text'){
					$sections[] = sprintf("DEFAULT %s",$this->nullable);
				}
			}else{
				$sections[] = $this->nullable;
			}
		}
		if($this->autoIncrement != '') $sections[] = $this->autoIncrement;
		if($this->comment != '') $sections[] = sprintf("COMMENT '%s'",$this->comment);
		return implode(" ",$sections);
	}
}

?>