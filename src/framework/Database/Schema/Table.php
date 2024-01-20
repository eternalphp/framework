<?php

namespace framework\Database\Schema;

use framework\Database\Schema\Field;
use framework\Database\Schema\Index;
use framework\Database\Schema\uniqueIndex;
use framework\Database\Schema\fulltextIndex;

class Table
{
	private $name = null;
	private $engine = 'InnoDB';
	private $autoIncrement = 1;
	private $charset = 'utf8';
	private $comment = '';
	private $fields = [];
	private $indexs = []; //索引

	public function __construct($name){
		$this->name = $name;
	}
	
    /**
     * Define fields of type increments
     *
     * @param  string | null  $name
     * @return $this
     */
	public function increments($name){
		$field = new Field();
		$field->increments($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type bigInt
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function bigInt($name,$length = 20){
		$field = new Field();
		$field->bigInt($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type smallInt
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function smallInt($name,$length = 6){
		$field = new Field();
		$field->smallInt($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type mediumInt
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function mediumInt($name,$length = 9){
		$field = new Field();
		$field->mediumInt($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type int
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function integer($name,$length = 11){
		$field = new Field();
		$field->integer($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type tinyint
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function tinyInt($name,$length = 4){
		$field = new Field();
		$field->tinyInt($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type string
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function string($name,$length = 255){
		$field = new Field();
		$field->string($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type char
     *
     * @param  string | null  $name
	 * @param  int $length
     * @return $this
     */
	public function char($name,$length = 255){
		$field = new Field();
		$field->char($name,$length);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type text
     *
     * @param  string | null  $name
     * @return $this
     */
	public function text($name){
		$field = new Field();
		$field->text($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type mediumtext
     *
     * @param  string | null  $name
     * @return $this
     */
	public function mediumText($name){
		$field = new Field();
		$field->mediumText($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type longtext
     *
     * @param  string | null  $name
     * @return $this
     */
	public function longText($name){
		$field = new Field();
		$field->longText($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type datetime
     *
     * @param  string | null  $name
     * @return $this
     */
	public function datetime($name){
		$field = new Field();
		$field->datetime($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type date
     *
     * @param  string | null  $name
     * @return $this
     */
	public function date($name){
		$field = new Field();
		$field->date($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type year
     *
     * @param  string | null  $name
     * @return $this
     */
	public function year($name){
		$field = new Field();
		$field->year($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type time
     *
     * @param  string | null  $name
     * @return $this
     */
	public function time($name){
		$field = new Field();
		$field->time($name);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type timestamp
     *
     * @param  string | null  $name
     * @return $this
     */
	public function timestamp($name){
		$field = new Field();
		$field->timestamp($name);
		$this->fields[$name] = $field;
		return $field;
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
		$field = new Field();
		$field->float($name,$length,$places);
		$this->fields[$name] = $field;
		return $field;
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
		$field = new Field();
		$field->double($name,$length,$places);
		$this->fields[$name] = $field;
		return $field;
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
		$field = new Field();
		$field->decimal($name,$length,$places);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Define fields of type enum
     *
     * @param  string | null  $name
     * @param  array $list
     * @return $this
     */
	public function enum($name,$list = array()){
		$field = new Field();
		$field->enum($name,$list);
		$this->fields[$name] = $field;
		return $field;
	}
	
    /**
     * Setting table encoding
     *
     * @param  string  $charset
     * @return $this
     */
	public function charset($charset = 'utf8'){
		$this->charset = $charset;
		return $this;
	}
	
    /**
     * Setting table engine
     *
     * @param  string  $engine
     * @return $this
     */
	public function engine($engine = 'InnoDB'){
		$this->engine = $engine;
		return $this;
	}
	
    /**
     * Setting table autoIncrement
     *
     * @param  string  $value
     * @return $this
     */
	public function autoIncrement($value = 1){
		$this->autoIncrement = $value;
		return $this;
	}
	
    /**
     * Define an index
     *
     * @param  string | null  $name
     * @return $this
     */
	public function index($fields = array(),$name = null){
		$this->indexs[] = (new Index($fields))->name($name);
		return $this;
	}
	
    /**
     * Define an unique index
     *
     * @param  string | null  $name
     * @return $this
     */
	public function unique($fields = array(),$name = null){
		$this->indexs[] = (new uniqueIndex($fields))->name($name);
		return $this;
	}
	
    /**
     * Define an unique index
     *
     * @param  string | null  $name
     * @return $this
     */
	public function fulltext($fields = array(),$name = null){
		$this->indexs[] = (new fulltextIndex($fields))->name($name);
		return $this;
	}
	
    /**
     * Setting Table Remarks
     *
     * @param  string $text
     * @return $this
     */
	public function comment($text){
		$this->comment = $text;
		return $this;
	}

    /**
     * Getting Table Remarks
     *
     * @return string
     */
	public function getComment(){
		return $this->comment;
	}
	
    /**
     * Get a list of table fields
     *
     * @return array
     */
	public function getFields(){
		return $this->fields;
	}
	
    /**
     * Get table name
     *
     * @return array
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * Get table engine
     *
     * @return array
     */
	public function getEngine(){
		return $this->engine;
	}
	
    /**
     * Get table charset
     *
     * @return array
     */
	public function getCharset(){
		return $this->charset;
	}
	
    /**
     * Get table index list
     *
     * @return array
     */
	public function getIndexs(){
		if($this->fields){
			$primaryKey = null;
			foreach($this->fields as $field){
				if($field->getIndex() != null){
					if($field->getIndex()->getType() == 'primary'){
						$primaryKey = $field->getIndex();
					}else{
						array_unshift($this->indexs,$field->getIndex());
					}
				}
			}
			if($primaryKey != null){
				array_unshift($this->indexs,$primaryKey);
			}
		}
		return $this->indexs;
	}
	
    /**
     * Get primaryKey
     *
     * @return string
     */
	public function getPrimaryKey(){
		if($this->fields){
			foreach($this->fields as $field){
				if($field->getIndex() != null){
					if($field->getIndex()->getType() == 'primary'){
						return $field->getName();
					}
				}
			}
		}
		return null;
	}
	
    /**
     * Get table structure content
     *
     * @return array
     */
	public function getSection(){
		$section = array();
		if($this->fields){
			foreach($this->fields as $field){
				$section[] = "\t" . $field->getSection();
			}
		}
		
		$this->getIndexs();
		
		if($this->indexs){
			foreach($this->indexs as $index){
				$section[] = "\t" . $index->getSection();
			}
		}
		return $section;
	}
	
    /**
     * Add table fields
     *
	 * @param string $field
     * @return string
     */
	public function addColumn($field,$afterField = null){
		if(isset($this->fields[$field])){
			$fieldObj = $this->fields[$field];
			$section = $fieldObj->getSection();
			if($afterField != null){
				$section = sprintf("%s AFTER `%s`;",rtrim($section,";"),$afterField);
			}else{
				$section = sprintf("%s primary key FIRST;",rtrim($section,";"));
			}
			return sprintf("ALTER TABLE %s ADD COLUMN %s",$this->name,$section);
		}
	}
	
    /**
     * modify table fields
     *
	 * @param string $field
     * @return string
     */
	public function updateColumn($field){
		if(isset($this->fields[$field])){
			$fieldObj = $this->fields[$field];
			$section = $fieldObj->getSection();
			return sprintf("ALTER TABLE %s  MODIFY COLUMN %s",$this->name,$section);
		}
	}
	
    /**
     * Change table fields name
     *
	 * @param string $field
     * @return string
     */
	public function changeColumn($field){
		if(isset($this->fields[$field])){
			$fieldObj = $this->fields[$field];
			$section = $fieldObj->getSection();
			return sprintf("ALTER TABLE %s CHANGE COLUMN %s %s",$this->name,$field,$section);
		}
	}
	
    /**
     * Modify the table name
     *
	 * @param string $tableName
     * @return string
     */
	public function rename($tableName){
		return sprintf("ALTER TABLE %s RENAME TO %s;",$this->name,$tableName);
	}
	
    /**
     * drop table fields
     *
	 * @param string $field
     * @return string
     */
	public function dropColumn($field){
		return sprintf("ALTER TABLE %s DROP %s;",$this->name,$field);
	}
	
    /**
     * drop table
     *
     * @return string
     */
	public function drop(){
		return sprintf("DROP TABLE IF EXISTS `%s`;",$this->name);
	}
	
    /**
     * Get SQL for creating tables
     *
     * @return string
     */
	public function getSql(){
		$lines = array();
		$lines[] = sprintf("CREATE TABLE `%s` (",$this->name);
		$lines[] = implode(",\n",$this->getSection());
		$lines[] = sprintf(") ENGINE=%s AUTO_INCREMENT=%d DEFAULT CHARSET=%s COMMENT='%s';",$this->engine,$this->autoIncrement,$this->charset,$this->comment);
		return implode("\n",$lines);
	}
}

?>