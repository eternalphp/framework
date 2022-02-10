<?php

namespace framework\Database\Eloquent;

use framework\Database\Connection\Connector;
use framework\Database\Relation\HasOne;
use framework\Database\Relation\HasMany;
use framework\Database\Relation\BelongsTo;
use framework\Database\Relation\BelongsToMany;
use Exception;
use stdClass;

class Model
{
	protected $table = null;
	protected $primaryKey = 'id';
	private $prefix = null;
	private $connector = null;
	private $config = null;
	private $sql;
	protected  $callback = null;
	private $listenQuery = false;
	private $buildQuery;
	protected $timestamps = false;
    const CREATED_AT = 'createtime';
    const UPDATED_AT = 'updatetime';
	protected $relations = [];
	
	public function __construct($config = array()){
		
		if($config){
			$this->config = $config;
		}else{
			$config = Config("database");
			$this->config = array(
				'driver' => $config['DB_DRIVER'],
				'servername' => $config['DB_HOST'],
				'username' => $config['DB_USER'],
				'password' => $config['DB_PWD'],
				'database' => $config['DB_NAME'],
				'port' => $config['DB_PORT'],
				'prefix' => $config['DB_PREFIX']
			);
		}
		
		if(isset($this->config["prefix"])){
			$this->prefix = $this->config["prefix"];
		}
		
		$this->buildQuery = new BuildQuery($this->prefix);
		
		if($this->table){
			$this->buildQuery->table($this->table);
		}
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
	
	final public function __call($method,$args){
		
		if(in_array($method, array('where', 'orwhere','whereIn','whereNotIn','between','whereLike','whereNotLike','table', 'join', 'joinquery','order', 'group', 'limit', 'having', 'count','sum','min','max','avg','field', 'lock','union'), true)){
			
			if($method == 'table'){
				$this->buildQuery = new BuildQuery($this->prefix);
			}
			
			call_user_func_array(array($this->buildQuery,$method),$args);
			
		}else{
			throw new Exception("{$method} is not exists !");
		}
		return $this;
	}
	
    /**
     * Query find
     *
     * @return $this
     */
	final public function find(){
		
		$this->sql = $this->buildQuery->getSql();
		
		$row = $this->query($this->sql)->find();
		
		if($this->relations){
			foreach($this->relations as $name=>$relation){
				if($relation instanceof HasOne){
					$field = $relation->getForeignKey();
					$row[$name] = $relation->getModel()->where($field,$row[$field])->find();
				}
				
				if($relation instanceof BelongsTo){
					$primaryKey = $relation->getLocalKey();
					$field = $relation->getForeignKey();
					$row[$name] = $relation->getModel()->where($primaryKey,$row[$field])->find();
				}
				
				if($relation instanceof HasMany){
					$field = $relation->getForeignKey();
					$row[$name] = $relation->getModel()->where($field,$row[$field])->select();
				}
				
				if($relation instanceof BelongsToMany){
					$field = $relation->getForeignKey();
					$row[$name] = $relation->belongsToManyQuery()->where($field,$row[$field])->select();
				}
			}
		}
		
		return $row;
	}
	
    /**
     * Query find
     *
     * @return $this
     */
	final public function first($id){
		$this->buildQuery->where($this->primaryKey,$id);
		$this->sql = $this->buildQuery->getSql();
		return $this->query($this->sql)->find();
	}

    /**
     * Query select
     *
     * @return $this
     */
	final public function select(){
		$this->sql = $this->buildQuery->getSql();
		$list = $this->query($this->sql)->select();
		if($this->pages){
			if(request('page',1) > $this->pages['total']){
				return false;
			}
		}
		
		if($this->relations){
			
			foreach($list as &$val){
				foreach($this->relations as $name=>$relation){
					
					if($relation instanceof HasOne){
						$field = $relation->getForeignKey();				
						$val[$name] = $relation->getModel()->where($field,$val[$field])->find();
					}
					
					if($relation instanceof BelongsTo){
						$primaryKey = $relation->getLocalKey();
						$field = $relation->getForeignKey();
						$val[$name] = $relation->getModel()->where($primaryKey,$val[$field])->find();
					}
				}
			}
			
		}
		
		if($this->callback != null && is_callable($this->callback)){
			if($list){
				foreach($list as $k=>&$row){
					$row = call_user_func($this->callback,$row);
				}
			}
		}
		
		return $list;
	}
	
	final public function query($sql){
		return $this->connect()->query($sql);
	}
	
	//设置分页
	final public function offset($pagesize = 30,$options = array()){
		
		$params = array();
		
		if(request('page')){
			$page = request('page');
			$params['page'] = intval($page);
		}
		
		if(request('offset')){
			$offset = request('offset');
			$params['offset'] = intval($offset);
		}
		
		if($options){
			$params = array_merge($params,$options);
		}
		
		
		$rows = $this->rows();
		$total = max(ceil($rows / $pagesize), 1); //总页数
		
		if(isset($params['offset'])){
			$offset = $params['offset'];
		}else{
			$page = $params['page'];
			$curr_page = max(min($total, $page), 1); //当前页
			$offset = ($curr_page - 1) * $pagesize;
		}
		
		$this->buildQuery->limit($pagesize,$offset);
		$this->pages = array('count'=>$rows,'total'=>$total);
		return $this;
	}
	
	final public function paginate($pageSize = 30){
		
		$rows = $this->rows();
		$pagination = new pagination($rows,$pageSize);
		
		if($pagination->hasPage()){
		
			$this->buildQuery->limit($pageSize,$pagination->offset());
			$list = $this->select();
			
		}else{
			$list = array();
		}
		
		if($this->callback != null && is_callable($this->callback)){
			if($list){
				foreach($list as $k=>&$row){
					$row = call_user_func($this->callback,$row);
				}
			}
		}
		
		$data = new stdClass();
		$data->paginate = $pagination;
		$data->data = $list;
		return $data;
	}
	
	//分批获取数据
	final public function chunk($pagesize,callable $callback){
		$page = 1;
		do{
			$_GET["page"] = $page;
			$this->offset($pagesize);
			$list = $this->select();
			call_user_func($callback,$list,$pagesize,$page);
			$page++;
			
		}while($page <= $this->model->pages["total"]);
	}
	
    /**
     * Query select
     *
     * @return $this
     */
	final public function queryList($sql){
		$this->sql = $sql;
		return $this->query($this->sql)->select();
	}
	
	final public function pipeline(callable $callback){
		$this->callback = $callback;
		return $this;
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
     * Query find get field value
     *
     * @return $this
     */
	final public function getVal($field){
		
		$fields = $this->buildQuery->getFields();
		if($fields[0] != $field){
			$this->buildQuery->field(sprintf("%s as %s",$fields[0],$field),0);
		}
		
		$this->sql = $this->buildQuery->getSql();

		$row = $this->query($this->sql)->find();
		
		if(isset($row[$field])){
			return $row[$field];
		}
		
		return false;
	}
	
    /**
     * Query insert
     *
	 * @param array $data
	 * @param bool $rows
     * @return int
     */
	final public function insert($data = array(),$rows = false){
		if($rows === true){
			foreach($data as $k=>$row){
				if($this->timestamps){
					$row[self::CREATED_AT] = date('Y-m-d H:i:s');
				}
				$data[$k] = $this->getData($row);
			}
		}else{
			if($this->timestamps){
				$data[self::CREATED_AT] = date('Y-m-d H:i:s');
			}
			$data = $this->getData($data);
		}
		$this->sql = $this->buildQuery->insert($data,$rows);
		return $this->query($this->sql)->insert();
		
	}
	
    /**
     * Query update
     *
	 * @param array $data
     * @return int
     */
	final public function update($data = array()){
		if(is_array($data)){
			if($this->timestamps){
				$data[self::UPDATED_AT] = date('Y-m-d H:i:s');
			}
			$data = $this->getData($data);
		}
		$this->sql = $this->buildQuery->update($data);
		return $this->query($this->sql);
	}


    /**
     * Query replace
     *
	 * @param array $field
	 * @param array $data
     * @return int
     */
	final public function replace($field = array(), $data = array()){
		$this->sql = $this->buildQuery->sql_replace($field,$data);
		return $this->query($this->sql);
	}

    /**
     * Query replaceInto
     *
	 * @param array $data
	 * @param bool $rows
     * @return int
     */
	final public function replaceInto($data = array(),$rows = false){
		if($rows === true){
			foreach($data as $k=>$row){
				$data[$k] = $this->getData($row);
			}
		}else{
			$data = $this->getData($data);
		}
		$this->sql = $this->buildQuery->replace($data,$rows);
		return $this->query($this->sql);
	}
	
    /**
     * Query delete
     *
     * @return bool
     */
	final public function delete(){
		$this->sql = $this->buildQuery->delete();
		return $this->query($this->sql);
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
	
	//事务封装
	public function transaction(callable $callback, callable $exception = null){
		$this->startTrans();
		try{
			
			call_user_func($callback,$this->model);
			
			$this->commit();
			
		}catch(Exception $ex){
			
			$this->rollback();
			
			if($exception != null){
				call_user_func($exception,$ex);
			}else{
				throw new Exception($ex->getMessage(),$ex->getCode());
			}
		}
		
		$this->transEnd();
	}

    /**
     * Lock table
     *
     * @return $this
     */
	final public function lock(){
		$this->connect()->query(sprintf("LOCK TABLES `%s` WRITE",$this->table));
	}

    /**
     * unLock table
     *
     * @return $this
     */
	final public function unlock(){
		$this->connect()->query("UNLOCK TABLES");
	}
	
	//懒加载
	public function with($name,callable $callback = null){
		$model = call_user_func_array(array($this,$name),array());
		
		if($callback != null){
			call_user_func($callback,$model->getModel());
		}
		
		$this->relations[$name] = $model;
		return $this;
	}
	
	//一对一关联
	public function hasOne($model,$foreignKey, $localKey = 'id'){
		return new HasOne($this, $model, $foreignKey, $localKey);
	}
	
	//一对一关联
	public function belongsTo($model,$foreignKey, $localKey = 'id'){
		return new BelongsTo($this, $model, $foreignKey, $localKey);
	}
	
	//一对多关联
	public function hasMany($model,$foreignKey, $localKey = 'id'){
		return new HasMany($this, $model, $foreignKey, $localKey);
	}
	
	//多对多关联
	public function belongsToMany($model,$middle,$foreignKey, $localKey = 'id'){
		return new BelongsToMany($this, $model, $middle, $foreignKey, $localKey);
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
				if($this->hasField($field)){
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
		}
		return $values;
	}
	
    /**
     * Query sql
     *
     * @return string
     */
	public function getSql(){
		$this->sql = $this->buildQuery->getSql();
		return $this->sql;
	}
	
    /**
     * Getting query sql
     *
     * @return strig
     */
	public function getLastSql(){
		return $this->sql;
	}
	
	//构造子查询
	final public function getSubQuery(callable $callback){
		$sql = $this->buildQuery->getSql();
		$table = sprintf("(%s)tmp",$sql);

		$this->buildQuery = new BuildQuery($this->prefix);
		$this->buildQuery->setTable($table);

		call_user_func($callback,$this->buildQuery);
		return $this;
	}
	
    /**
     * Get Fields
     *
	 * @param  string  $table
     * @return array
     */
	public function getFields(){
		static $fieldsList = array();
		
		$table = $this->buildQuery->getTable();
		
		$sql = "describe {$table}";
		
		if($fieldsList[md5($sql)]){
			return $fieldsList[md5($sql)];
		}
		
		$result = $this->query($sql);
		$fields = array();
		while($rs = $this->db->fetch_array($result)){
			$fields[] = $rs["Field"];
		}
		$fieldsList[md5($sql)] = $fields;
		
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
		$table = $this->tableName($table);
		if(in_array($table,$this->getTables())){
			return true;
		}else{
			return false;
		}
	}
	
	//获取当前定义的表名
	public function tableName($alias = ''){
		if($alias != ''){
			return sprintf("%s as %s",$this->table,$alias);
		}
		
		return $this->table;
	}
	
	//获取当前定义的表名
	public function fullTableName($alias = ''){
		if($alias != ''){
			return sprintf("%s as %s",$this->prefix . $this->table,$alias);
		}
		
		return $this->prefix . $this->table;
	}
	
	//获取主键
	public function primaryKey(){
		return $this->primaryKey;
	}
}
?>