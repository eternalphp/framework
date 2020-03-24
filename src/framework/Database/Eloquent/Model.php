<?php

namespace framework\Database\Eloquent;

use framework\Database\Connection\Connector;


class Model
{
	protected $table = null;
	protected $primaryKey = 'id';
	private $options = array();
	private $condition = null;
	private $prefix = null;
	private $connector = null;
	private $config = null;
	private $sql;
	
	public function __construct($config){
		$this->config = $config;
		if(isset($config["prefix"])){
			$this->prefix = $config["prefix"];
		}
		$this->connect();
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
     * Query table
     *
     * @return $this
     */
	public function table($table){
		$this->options = array();
		$this->condition = null;
		$this->options["table"] = $this->getTableName($table);
		return $this;
	}
	
    /**
     * Query where
     *
     * @return $this
     */
	public function where(){
		if($this->condition == null){
			$this->condition = new Condition();
		}
		$args = func_get_args();
		call_user_func_array(array($this->condition,'where'),$args);
		
		$this->options["where"] = $this->condition->getCondition();
		
		return $this;
	}
	
    /**
     * Query join
     *
     * @return $this
     */
	public function join($table, $condition, $handle = 'LEFT'){
		$this->options["join"][] = sprintf("%s JOIN %s ON %s",$handle,$this->getTableName($table),$condition);
		return $this;
	}
	
    /**
     * Query field
     *
     * @return $this
     */
	public function field($field = '*'){
		$this->options["field"] = $field;
		return $this;
	}
	
    /**
     * Query count
     *
     * @return $this
     */
	public function count($field = '*'){
		$this->options["field"] = sprintf("count(%s)",$field);
		return $this;
	}
	
    /**
     * Query avg
     *
     * @return $this
     */
	public function avg($field){
		$this->options["field"] = sprintf("avg(%s)",$field);
		return $this;
	}
	
    /**
     * Query sum
     *
     * @return $this
     */
	public function sum($field){
		$this->options["field"] = sprintf("sum(%s)",$field);
		return $this;
	}
	
    /**
     * Query min
     *
     * @return $this
     */
	public function min($field){
		$this->options["field"] = sprintf("min(%s)",$field);
		return $this;
	}
	
    /**
     * Query max
     *
     * @return $this
     */
	public function max($field){
		$this->options["field"] = sprintf("max(%s)",$field);
		return $this;
	}
	
    /**
     * Query limit
     *
     * @return $this
     */
	public function limit($offset = 0, $pageSize = 30){
		$this->options["limit"] = sprintf("limit %d,%d",$offset,$pageSize);
		return $this;
	}
	
    /**
     * Query list count
     *
     * @return int
     */
	final public function rows(){
		$sql = $this->getSql();
		if(strstr($sql,"group by")){
			$sql = sprintf("select count(*) as count from(%s) as s",$sql);
		}else{
			$sql = preg_replace("/select\s+(.*?)\s+from/","select count(*) as count from",$sql);
		}
		$res = $this->connector->find($sql);
		
		if($res){
			return $res['count'];
		}else{
			return 0;
		}
	}
	
    /**
     * Query list pages
     *
     * @return $this
     */
	public function offset($pageSize = 30,$name = 'offset'){
		$rows = $this->rows();
		$total = max(ceil($rows / $pageSize), 1); //总页数
		$params = array();
		$offset = isset($_GET[$name]) ? intval($_GET[$name]) : 0;
		$this->options['limit'] = sprintf("%d,%d",$offset,$pageSize);
		$this->pages = array('count'=>$rows,'total'=>$total);
		if($offset > $rows){
			return false;
		}
		return $this;
	}
	
    /**
     * Query list pages
     *
     * @return $this
     */
	public function pagination($pageSize = 30,$name = 'page'){
		$rows = $this->rows();
		$total = max(ceil($rows / $pageSize), 1); //总页数
		$params = array();
		$page = isset($_GET[$name]) ? intval($_GET[$name]) : 1;
		$offset = ($page - 1) * $pageSize;
		$this->options['limit'] = sprintf("%d,%d",$offset,$pageSize);
		$this->pages = array('count'=>$rows,'total'=>$total);
		if($page > $total || $page < 1){
			return false;
		}
		return $this;
	}
	
    /**
     * Query order
     *
     * @return $this
     */
	public function order($order){
		$this->options["order"] = sprintf("order by %s",$order);
		return $this;
	}
	
    /**
     * Query group by
     *
     * @return $this
     */
	public function group($group){
		$this->options["group"] = sprintf("group by %s",$group);
		return $this;
	}
	
    /**
     * Query select
     *
     * @return $this
     */
	final public function select(){
		$this->sql = $this->getSql();
		return $this->connector->query($this->sql)->select();
	}
	
    /**
     * Query toList
     *
	 * @param string $key
	 * @param string $value
	 * @param array $data
     * @return array
     */
	final public function toList($key,$value = null,$data = null){
		
		if($data == null){
			$data = $this->select();
		}
		
		$newData = array();
		if($data){
			if($value!=null){
				foreach($data as $k=>$val){
					if(isset($val[$key]) && isset($val[$value])){
						$newData[$val[$key]][] = $val[$value];
					}
				}
				
				if($newData){
					foreach($newData as $field=>$vals){
						if(count($vals) == 1){
							$newData[$field] = $vals[0];
						}
					}
				}
				
			}else{
				foreach($data as $k=>$val){
					if(isset($val[$key])){
						$newData[] = $val[$key];
					}
				}
			}
			unset($data);
		}
		return $newData;
	}
	
    /**
     * Query find
     *
     * @return $this
     */
	final public function find(){
		$this->sql = $this->getSql();
		return $this->connect()->query($this->sql)->find();
	}
	
    /**
     * Query find get field value
     *
     * @return $this
     */
	final public function getVal($field){
		$row = $this->find();
		if(isset($row[$field])){
			return $row[$field];
		}else{
			return false;
		}
	}
	
    /**
     * Query insert
     *
	 * @param array $data
	 * @param bool $rows
     * @return int
     */
	final public function insert($data = array(),$rows = false){
		$fields = array();
		$values = array();
		$table = $this->options["table"];
		if($rows == true){
			$values = array();
			foreach($data as $k=>$row){
				if(count($fields) == 0){
					foreach($row as $field=>$val){
						$fields[] = sprintf("`%s`",$field);
					}
				}
				$values[] = sprintf("(%s)",implode(",",$this->getData($row)));
			}
		}else{
			foreach($data as $field=>$val){
				$fields[] = sprintf("`%s`",$field);
			}
			$values[] = sprintf("(%s)",implode(",",$this->getData($data)));
		}
		$this->sql = sprintf("INSERT INTO %s (%s) values %s;",$table,implode(",",$fields),implode(",",$values));
		return $this->connect()->query($this->sql)->insert();
		
	}
	
    /**
     * Query update
     *
	 * @param array $data
     * @return int
     */
	final public function update($data = array()){
		$table = $this->options['table'];
		$where = isset($this->options['where']) ? $this->options['where'] : '1=1';

		if(is_array($data)){
			$sqls = array();
			foreach($data as $key=>$val){
				$val = sprintf("'%s'",$this->connector->escape(trim($val)));
				$sqls[] = sprintf("`%s`=%s",$key,$val);
			}
			$this->sql = sprintf("UPDATE %s SET %s where %s",$table,implode(',',$sqls),$where);
		}else{
			$this->sql = sprintf("UPDATE %s SET %s where %s",$table,$data,$where);
		}
		return $this->connect()->query($this->sql);
	}


    /**
     * Query replace
     *
	 * @param array $field
	 * @param array $data
     * @return int
     */
	final public function replace($field = array(), $data = array()){
		$table = $this->options['table'];
		$where = isset($this->options['where']) ? $this->options['where'] : '1=1';
		$sqls = array();
		foreach($field as $val){
			foreach($data as $key=>$word){
				$sqls[] = sprintf("%s=replace(%s,'%s','%s')",$val,$val,$key,$word);
			}
		}
		$this->sql = sprintf("UPDATE %s SET %s where %s",$table,implode(',',$sqls),$where);

		return $this->connect()->execute($this->sql);
	}

    /**
     * Query replaceInto
     *
	 * @param array $data
	 * @param bool $rows
     * @return int
     */
	final public function replaceInto($data = array(),$rows = false){
		$fields = array();
		$values = array();
		$table = $this->options["table"];
		if($rows == true){
			$values = array();
			foreach($data as $k=>$row){
				if(count($fields) == 0){
					foreach($row as $field=>$val){
						$fields[] = sprintf("`%s`",$field);
					}
				}
				$values[] = sprintf("(%s)",implode(",",$this->getData($row)));
			}
		}else{
			foreach($data as $field=>$val){
				$fields[] = sprintf("`%s`",$field);
			}
			$values[] = sprintf("(%s)",implode(",",$this->getData($data)));
		}
		$this->sql = sprintf("REPLACE INTO %s (%s) values %s;",$table,implode(",",$fields),implode(",",$values));
		return $this->connect()->execute($this->sql);
	}
	
    /**
     * Query delete
     *
     * @return bool
     */
	final public function delete(){
		$table = $this->options['table'];
		$where = isset($this->options['where']) ? $this->options['where'] : '1=1';
		if(isset($this->options["join"])){
			$joins = $this->options["join"];
			$alias = array();
			$arr = explode(" ",$table);
			$alias[] = $arr[2];
			foreach($joins as $join){
				$arr = explode(" ",$join);
				$alias[] = $arr[4];
			}		  
			$joinSql = implode(" ",$this->options["join"]);
			$alias = implode(",",$alias);
			$this->sql = sprintf("DELETE %s from %s %s where %s",$alias,$table,$joinSql,$where);
		}else{
			$this->sql = sprintf("DELETE from %s where %s",$table,$where);
		}

		return $this->connect()->execute($this->sql);
	}

    /**
     * Start transaction
     *
     * @return $this
     */
	final public function startTrans(){
		$this->connect()->startTrans();
	}

    /**
     * Transaction commit
     *
     * @return $this
     */
	final public function commit(){
		$this->connect()->commit();
	}

    /**
     * Transaction rollback
     *
     * @return $this
     */
	final public function rollback(){
		$this->connect()->rollback();
	}

    /**
     * Transaction end
     *
     * @return $this
     */
	final public function transEnd(){
		$this->connect()->transEnd();
	}

    /**
     * Lock table
     *
     * @return $this
     */
	final public function lock(){
		$table = $this->options['table'];
		$this->connect()->query(sprintf("LOCK TABLES `%s` WRITE",$table));
	}

    /**
     * unLock table
     *
     * @return $this
     */
	final public function unlock(){
		$this->connect()->query("UNLOCK TABLES");
	}
	
	
    /**
     * Format data
     *
	 * @param array $row
     * @return array
     */
	public function getData($row = array()){
		$values = array();
		if($row){
			foreach($row as $field=>$val){
				if(is_array($val)){
					$values[] = sprintf("'%s'",json_encode($val));
				}else{
					if(is_null($val)){
						$values[] = 'NULL';
					}else{
						$values[] = sprintf("'%s'",$this->connector->escape(trim($val)));
					}
				}
			}
		}
		return $values;
	}
	
    /**
     * Query sql
     *
     * @return string
     */
	public function getSql(){
		
		if(!isset($this->options["field"])){
			$this->options["field"] = "*";
		}
		
		$sql[] = sprintf("select %s from %s",$this->options["field"],$this->options["table"]);
		
		if(isset($this->options["join"]) && count($this->options["join"]) > 0){
			$sql[] = implode(" ",$this->options["join"]);
		}
		
		if(isset($this->options["where"]) && $this->options["where"] != ''){
			$sql[] = sprintf("where %s",$this->options["where"]);
		}
		
		if(isset($this->options["group"]) && $this->options["group"] != ''){
			$sql[] = $this->options["group"];
		}
		
		if(isset($this->options["order"]) && $this->options["order"] != ''){
			$sql[] = $this->options["order"];
		}
		
		if(isset($this->options["limit"]) && $this->options["limit"] != ''){
			$sql[] = $this->options["limit"];
		}
		
		return implode(" ",$sql);
	}
	
    /**
     * Getting query sql
     *
     * @return strig
     */
	public function getLastSql(){
		return $this->sql;
	}
	
    /**
     * Get Fields
     *
	 * @param  string  $table
     * @return array
     */
	public function getFields(){
		$fields = array();
		$table = $this->options["table"];
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
     * Determine whether the field name exists
     *
	 * @param  string  $field
     * @return bool
     */
	final public function hasField($field){
		if(in_array($field,$this->getFields())){
			return true;
		}else{
			return false;
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
     * Determine whether the table name exists
     *
	 * @param  string  $table
     * @return bool
     */
	final public function hasTable($table){
		$table = $this->getTableName($table);
		if(in_array($table,$this->getTables())){
			return true;
		}else{
			return false;
		}
	}
	
    /**
     * Getting table name
     *
     * @return strig
     */
	public function getTableName($table){
		if(strstr($table,' as ')){
			$tables = explode(" as ",$table);
			$tables[0] = sprintf("%s%s",$this->prefix,$tables[0]);
			return implode(" as ",$tables);
		}else{
			return sprintf("%s%s",$this->prefix,$table);
		}
	}
}
?>