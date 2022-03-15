<?php

namespace framework\Database\Schema;

use framework\Database\Eloquent\DB;

class Control
{
	private $tableList = array(); //创建的表对象列表
	private $displayDebug = false; //是否启用调试
	private $callback = null;

	public function __construct(){

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
		$shortName = $table;

		$table = DB::getTableName($table);
		$this->table = new Table($table);
		$this->tableList[$table] = $this->table;
		call_user_func($callback,$this->table);
		
		if(DB::hasTable($shortName) == false){
			$result = DB::query($this->table->getSql());
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
							$result = DB::query($sql);
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
						$result = DB::query($sql);
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
								$result = DB::query($sql);
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
						$result = $this->table->dropColumn($field);
						$sql = $this->table->getLastSql();
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
     * modify Table
     *
     * @param  string  $table
     * @param  callable  $callback
     * @return void
     */
	public function table($table,callable $callback){
		$shortName = $table;

		$table = DB::getTableName($table);
		
		$oldTableFields = $this->getFields($table);
		
		$this->table = new Table($table);
		$this->tableList[$table] = $this->table;
		call_user_func($callback,$this->table);
		
		$sections = $this->getSections($table);
		$oldFields = $sections["fields"];

		//获取表中的字段
		$tableFields = $this->getFields($table);
		
		$fields = $this->table->getFields();
		
		if($fields){
			$lastField = end($tableFields);
			foreach($fields as $name=>$field){
				
				//表示要添加字段
				if(!in_array($name,$tableFields)){
					
					$sql = $this->table->addColumn($name,$lastField);
					$result = DB::query($sql);
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
							$result = DB::query($sql);
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
		if($oldTableFields){
			foreach($oldTableFields as $field){
				if(!in_array($field,$tableFields)){
					$this->message(sprintf("alter table %s drop column : %s \n",$table,$field));
				}
			}
		}
		
	}
	
    /**
     * drop table if exists
     *
	 * @param string $table
     * @return bool
     */
	public function dropIfExists($table){
		if(DB::hasTable($table)){
			$table = new Table(DB::getTableName($table));
			$sql = $table->drop();
			return DB::query($sql);
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
		$list = DB::query("desc $table")->select();
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
		$row = DB::query("show create table $table")->find();
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
		if($this->callback != null){
			call_user_func($this->callback,$message);
		}
	}
	
    /**
     * set callback of message
     *
	 * @param callable $callback
     * @return $this
     */
	public function onMessage(callable $callback){
		$this->callback = $callback;
		return $this;
	}

}

?>