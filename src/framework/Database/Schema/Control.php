<?php

namespace framework\Database\Schema;

use framework\Database\Schema\Field;
use framework\Database\Schema\Table;
use framework\Database\Connection\Connector;

class Control
{
	private $config = null; //配置参数 
	private $connector = null; //数据库链接对象
	private $tableList = array(); //创建的表对象列表
	private $displayMessage = false; //是否显示消息
	private $displayDebug = false; //是否启用调试

	public function __construct($config = array()){
		$this->config = $config;
	}
	
    /**
     * Create Connector
     *
     * @return connector
     */
	public function connect(){
		if($this->connector == null){
			$this->connector = new Connector($this->config);
			$this->connector->connect();
		}
		return $this->connector;
	}
	
    /**
     * Display message
     *
     * @return $this;
     */
	public function displayMessage(){
		$this->displayMessage = true;
		return $this;
	}
	
    /**
     * Display debug
     *
     * @return $this;
     */
	public function displayDebug(){
		$this->displayDebug = true;
		return $this;
	}
	
    /**
     * Create Table
     *
     * @param  string  $table
     * @param  callable  $callback
     * @return void
     */
	public function create($table,callable $callback){
		$table = $this->getTableName($table);
		$this->table = new Table($table);
		$this->tableList[$table] = $this->table;
		call_user_func($callback,$this->table);
		
		if($this->hasTable($table) == false){
			$result = $this->connect()->execute($this->table->getSql());
			if($result){
				$this->message(sprintf("create table : %s \n",$table));
			}
		}else{
			
			$sections = $this->getSections($table);
			$oldFields = $sections["fields"];
			
			$tableFields = $this->getFields($table);
			
			$fields = $this->table->getFields();
			$fieldNames = array();
			
			if($fields){
				$lastField = null;
				foreach($fields as $name=>$field){
					
					$fieldNames[] = $name;
					
					//表示字段要更换
					if($field->getAlias() != null){
						
						$fieldNames[] = $field->getAlias();
						$tableFields[] = $name;
						
						if(!in_array($field->getAlias(),$tableFields)){
							
							$sql = $this->table->changeColumn($name);
							$result = $this->connect()->execute($sql);
							if($result){
								$this->message(sprintf("alter table %s change column : %s \n",$table,$name));
								
								if($this->displayDebug == true){
									$this->message(sprintf("change column: %s \n",$sql));
								}
							}
							
						}
					}
					
					
					//表示要添加字段
					if(!in_array($name,$tableFields)){
						
						$sql = $this->table->addColumn($name,$lastField);
						$result = $this->connect()->execute($sql);
						if($result){
							$this->message(sprintf("alter table %s add column : %s \n",$table,$name));
							
							if($this->displayDebug == true){
								$this->message(sprintf("add column: %s \n",$sql));
							}
						}
						
					}else{
						
						//判断字段是否修改
						if($oldFields){
							
							$name = ($field->getAlias() != null) ? $field->getAlias() : $name;
							
							if(isset($oldFields[$name]) && $oldFields[$name] != $field->getSection()){
								$sql = $this->table->updateColumn($name);
								$result = $this->connect()->execute($sql);
								if($result){
									$this->message(sprintf("alter table %s modify column : %s \n",$table,$name));
									
									if($this->displayDebug == true){
										$this->message(sprintf("modify column: %s \n",$sql));
									}
									
								}
							}
						}
						
					}

					$lastField = $name;
				}
			}
			
			//检查是否有字段删除
			if($tableFields){
				foreach($tableFields as $field){
					if(!in_array($field,$fieldNames)){
						$sql = $this->table->dropColumn($field);
						$result = $this->connect()->execute($sql);
						if($result){
							$this->message(sprintf("alter table %s drop column : %s \n",$table,$field));
							
							if($this->displayDebug == true){
								$this->message(sprintf("drop column: %s \n",$sql));
							}
							
						}
					}
				}
			}
			
		}

	}
	
    /**
     * Get Tables
     *
     * @return array
     */
	public function getTables(){
		$tables = array();
		$key = implode("_",["Tables_in",$this->config["database"]]);
		$list = $this->connect()->query("show tables")->select();
		if($list){
			foreach($list as $k=>$val){
				$tables[] = $val[$key];
			}
		}
		unset($list);
		return $tables;
	}
	
    /**
     * Judging whether the table exists
     *
	 * @param string $table
     * @return bool
     */
	public function hasTable($table){
		$tables = $this->getTables();
		if(in_array($table,$tables)){
			return true;
		}else{
			return false;
		}
	}
	
    /**
     * drop table if exists
     *
	 * @param string $table
     * @return bool
     */
	public function dropIfExists($table){
		if($this->hasTable($table)){
			$table = new Table($table);
			$sql = $table->drop();
			return $this->connect()->execute($sql);
		}
	}
	
    /**
     * Get Fields
     *
	 * @param  string  $table
     * @return array
     */
	public function getFields($table){
		$fields = array();
		$list = $this->connect()->query("desc $table")->select();
		if($list){
			foreach($list as $k=>$val){
				$fields[] = $val["Field"];
			}
		}
		unset($list);
		return $fields;
	}
	
    /**
     * Get Table Sections
     *
	 * @param  string  $table
     * @return array
     */
	public function getSections($table){
		$fields = array();
		$indexs = array();
		$row = $this->connect()->query("show create table $table")->find();
		if($row){
			$sections = explode("\n",$row["Create Table"]);
			if($sections){
				foreach($sections as $k=>$section){
					if($k > 0){
						if(strstr($section,"PRIMARY KEY") || strstr($section,"INDEX") || strstr($section,"KEY") || strstr($section,"UNIQUE") || strstr($section,"FULLTEXT") || strstr($section,"FOREIGN KEY")){
							$indexs[] = $section;
						}else{
							preg_match("/`(.*?)`/",$section,$matchs);
							if($matchs){
								$fields[$matchs[1]] = trim(trim($section),",");
							}
						}
					}
				}
			}
		}
		return array(
			'fields'=>$fields,
			'indexs'=>$indexs
		);
	}
	
    /**
     * Get Table List
     *
     * @return array
     */
	public function getTableList(){
		return $this->tableList;
	}
	
    /**
     * print message
     *
	 * @param string $message
     * @return array
     */
	public function message($message){
		if($this->displayMessage == true){
			echo $message;
		}
	}
	
    /**
     * Get TableName
     *
	 * @param  string  $table
     * @return string
     */
	public function getTableName($table){
		if(isset($this->config["prefix"])){
			$table = sprintf("%s%s",$this->config["prefix"],$table);
		}
		return $table;
	}

}

?>