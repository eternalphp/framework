<?php

namespace framework\Database\Eloquent;

class BuildQuery{
	
	private $tables = []; //表名列表
	private $alias;  //为sql设置别名
	private $prefix; //表前缀
	private $fields = []; //字段列表
	private $conditions = []; //条件列表
	private $joins = []; //连接语句
	private $orders = []; //排序语句
	private $group; //分组语句
	private $limit; //分页
	private $having; //聚合查询
	private $unions = []; //联合查询
	private $buildCondition; //构建条件语句对象

	public function __construct($prefix = ''){
		$this->prefix = $prefix;
		$this->buildCondition = new BuildCondition();
	}


    /**
     * Set the table name of the query statement
     * @param string $table
	 * @param string ...
     * @return $this
     */
	public function table($table){
		$nums = func_num_args();
		if($nums > 1){
			$tables = func_get_args();
			foreach($tables as $table){
				$this->tables[] = $this->getTableName($table);
			}
		}else{
			$this->tables[] = $this->getTableName($table);
			$this->alias = $table;
		}
		
		return $this;
	}
	
	public function setTable($table){
		$this->tables[] = $table;
		return $this;
	}
	
    /**
     * Set the field name of the query statement
     * @param string|array|callable $fields
     * @return $this
     */
	public function field($fields = '*',$index = null){
		
		if($index === null) {
			if(is_callable($fields)){
				$query = new BuildQuery($this->prefix);
				call_user_func($fields, $query);
				$this->fields[] = sprintf("(%s) as %s", $query->getSql(), $query->getAlias());
			}
			
			if(is_array($fields)){
				$this->fields = array_merge($this->fields, $fields);
			}
			
			if(is_string($fields)){
				$this->fields = array_merge($this->fields, explode(", ", $fields));
			}
		}else{
			$this->fields[$index] = $fields;
		}
		
		return $this;
	}
	
    /**
     * Set the condition of the query statement
     * @param callable $callback
     * @return $this
     */
	public function orWhere(callable $callback){
		
		$conditions = $this->buildCondition->getConditions();
		if($conditions){
			$this->conditions = array_merge($this->conditions, $conditions);
			$this->buildCondition = new BuildCondition();
		}
		
		$query = new BuildCondition();
		call_user_func($callback, $query);
		$this->conditions[] = sprintf("(%s)", implode(" OR ", $query->getConditions()));
		return $this;
	}
	
    /**
     * Set the condition of the query statement
	 * @param string $condition
     * @param callable $callback
     * @return $this
     */
	public function queryWhere($condition, callable $callback){
		$query = new BuildQuery($this->prefix);
		call_user_func($callback, $query);
		$this->conditions[] = sprintf("%s (%s)", $condition, $query->getSql());
		return $this;
	}
	
    /**
     * Set the limit of the query statement
	 * @param int $num
     * @param int $offset
     * @return $this
     */
	public function limit($num = 30, $offset = 0){
		$this->limit = sprintf("limit %d, %d", $offset, $num);
		return $this;
	}
	
    /**
     * Set the group of the query statement
	 * @param string $fields
     * @return $this
     */
	public function group($fields){
		$this->group = sprintf("group by %s", $fields);
		return $this;
	}
	
    /**
     * Set the join of the query statement
	 * @param string $table
	 * @param string $condition
	 * @param string $type
     * @return $this
     */
	public function join($table, $condition, $type = 'LEFT'){
		$table = $this->getTableName($table);
		$this->joins[] = sprintf("%s JOIN %s on %s", $type, $table, $condition);
		return $this;
	}
	
    /**
     * Set the join of the query statement
	 * @param string $table
	 * @param string $condition
     * @return $this
     */
	public function rightJoin($table, $condition){
		$this->join($table, $condition, 'RIGHT');
		return $this;
	}
	
    /**
     * Set the join of the query statement
	 * @param string $table
	 * @param string $condition
     * @return $this
     */
	public function innerJoin($table, $condition){
		$this->join($table, $condition, 'INNER');
		return $this;
	}
	
    /**
     * Set the join of the query statement
	 * @param callable $callback
	 * @param string $condition
	 * @param string $type
     * @return $this
     */
	public function joinQuery(callable $callback, $condition, $type = 'LEFT'){
		$query = new BuildQuery($this->prefix);
		call_user_func($callback, $query);
		$this->joins[] = sprintf("%s JOIN (%s)%s on %s", $type, $query->getSql(), $query->getAlias(), $condition);
		return $this;
	}
	
    /**
     * Set the union of the query statement
	 * @param callable $callback
     * @return $this
     */
	public function union(callable $callback){
		$query = new BuildQuery($this->prefix);
		call_user_func($callback, $query);
		$this->unions[] = sprintf("union %s", $query->getSql());
		return $this;
	}
	
    /**
     * Set the union of the query statement
	 * @param callable $callback
     * @return $this
     */
	public function unionAll(callable $callback){
		$query = new BuildQuery($this->prefix);
		call_user_func($callback, $query);
		$this->unions[] = sprintf("union all %s", $query->getSql());
		return $this;
	}
	
    /**
     * Set the alias name of the query statement
	 * @param string $name
     * @return $this
     */
	public function alias($name){
		$this->alias = $name;
		return $this;
	}
	
    /**
     * Get the alias name
     * @return string
     */
	public function getAlias(){
		return $this->alias;
	}
	
    /**
     * Set the order of the query statement
	 * @param string $order
     * @return $this
     */
	public function order($order){
		$this->orders[] = $order;
		return $this;
	}
	
    /**
     * Set the having of the query statement
	 * @param string $condition
     * @return $this
     */
	public function having($condition){
		$this->having = sprintf("having %s", $condition);
		return $this;
	}
	
    /**
     * Set the count of the query statement
	 * @param string $field
     * @return $this
     */
	public function count($field){
		$this->fields = [sprintf("count(%s)", $field)];
		return $this;
	}
	
    /**
     * Set the max of the query statement
	 * @param string $field
     * @return $this
     */
	public function max($field){
		$this->fields = [sprintf("max(%s)", $field)];
		return $this;
	}
	
    /**
     * Set the min of the query statement
	 * @param string $field
     * @return $this
     */
	public function min($field){
		$this->fields = [sprintf("min(%s)", $field)];
		return $this;
	}
	
    /**
     * Set the sum of the query statement
	 * @param string $field
     * @return $this
     */
	public function sum($field){
		$this->fields = [sprintf("sum(%s)", $field)];
		return $this;
	}
	
    /**
     * Set the avg of the query statement
	 * @param string $field
     * @return $this
     */
	public function avg($field){
		$this->fields = [sprintf("avg(%s)", $field)];
		return $this;
	}
	
	private function getDataFields($data = array()){
		$fields = array();
		if($data){
			foreach(array_keys($data) as $field){
				$fields[] = sprintf('`%s`',$field);
			}
		}
		return implode(",",$fields);
	}
	
	public function insert($data,$rows = false){
		$table = $this->tables[0];
		if($rows === true){
			$values = array();
			foreach($data as $k=>$row){
				$values[] = "(".implode(",",$row).")";
			}
			$fields = $this->getDataFields($data[0]);
			$sql = "INSERT INTO $table ({$fields}) values ".implode(",",$values);
			unset($values,$data);
		}else{
			$fields = $this->getDataFields($data);
			$sql = "INSERT INTO $table ({$fields}) values (".implode(",",$data).")";
			unset($data);
		}
		return $sql;
	}
	
	public function update($data = array()){
		$table = $this->tables[0];
		$where = implode(" and ",$this->getConditions());

		if(is_array($data)){

			$values = array();
			foreach($data as $field => $value){
				$values[] = implode("=",array("`$field`",$value));
			}
			$sql = "UPDATE {$table} SET ".implode(',',$values)." where $where";
		}else{
			$sql = "UPDATE {$table} SET $data where $where";
		}
		return $sql;
	}
	
	final public function sql_replace($fields = array(),$data = array()){
		$table = $this->tables[0];
		$where = $this->getConditions() ? implode(" and ",$this->getConditions()) : '1=1';
		$values = array();
		foreach($fields as $field){
			foreach($data as $key=>$word){
				$values[] = "$field=replace($field,'$key','$word')";
			}
		}
		$sql = "UPDATE {$table} SET ".implode(',',$values)." where $where";
		return $sql;
	}
	
	public function replace($data = array(),$rows = false){
		$table = $this->tables[0];
		if($rows === true){
			$values = array();
			foreach($data as $k=>$row){
				$values[] = "(".implode(",",$row).")";
			}
			$fields = $this->getDataFields($data[0]);
			$sql = "REPLACE INTO $table ({$fields}) values ".implode(",",$values);
			unset($values,$data);

		}else{
			$fields = $this->getDataFields($data[0]);
			$sql = "REPLACE INTO $table ({$fields}) values (".implode(",",$data).")";
			unset($data);
		}
		return $sql;
	}
	
	public function delete(){
		$table = $this->tables[0];
		$where = $this->getConditions() ? implode(" and ",$this->getConditions()) : '1=1';
		$sql = "DELETE from {$table} where $where";

		return $sql;
	}
	
    /**
     * Get name of table
	 * @param string $table
     * @return string
     */
	private function getTableName($table){
		$tables = explode(" as ", $table);
		if(count($tables) > 1){
			$tables[0] = $this->prefix . $tables[0];
			$table = implode(" as ", $tables);
		}else{
			$table = $this->prefix . $table;
		}
		
		return $table;
	}
	
	public function getConditions(){
		
		$conditions = $this->buildCondition->getConditions();
		if($conditions){
			$this->conditions = array_merge($this->conditions, $conditions);
		}
		$this->buildCondition = new BuildCondition();
		return $this->conditions;
	}
	
	public function getFields(){
		return $this->fields ? $this->fields : ['*'];
	}
	
	public function getTable(){
		return implode(",", $this->tables);
	}
	
    /**
     * Get sql of the query statement
     * @return string
     */
	public function getSql(){
		$sql = array();

		$table = implode(",", $this->tables);
		$field = $this->fields ? implode(",", $this->fields) : '*';

		if($this->joins){
			$sql[] = implode(" ", $this->joins);
		}
		
		$this->getConditions();
		
		if($this->conditions){
			$where = implode(" AND ", $this->conditions);
			$sql[] = sprintf("where %s", $where);
		}
		
		if(!empty($this->group)){
			$sql[] = $this->group;
		}
		
		if(!empty($this->having)){
			$sql[] = $this->having;
		}
		
		if($this->orders){
			$sql[] = sprintf("order by %s", implode(", ", $this->orders));
		}
		
		if($this->unions){
			$sql = array_merge($sql, $this->unions);
		}
		
		if(!empty($this->limit)){
			$sql[] = $this->limit;
		}

		return "select $field from $table ".implode(" ", $sql);
	}
	
    /**
     * Set the condition of the query statement
	 * @param string $method
	 * @param array $args
     * @return $this
     */
	public function __call($method, $args){
		call_user_func_array(array($this->buildCondition, $method), $args);
		return $this;
	}

	function __destruct(){
	}
}
?>