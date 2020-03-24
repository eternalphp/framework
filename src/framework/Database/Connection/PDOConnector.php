<?php

namespace framework\Database\Connection;

use framework\Database\Connection\ConnectorInterface;
use PDO;


class PDOConnector implements ConnectorInterface
{
	private $servername = null;
	private $username = null;
	private $password = null;
	private $port = 3306;
	private $database = null;
	private $charset = 'utf8';
	private $connection = null;
	private $result = null;
	public  $attrPersistent = false; //是否开启长连接
	
	public function __construct($config = array()){
		$this->servername = $config["servername"];
		$this->username = $config["username"];
		$this->password = $config["password"];
		if(isset($config["port"])) $this->port = $config["port"];
		if(isset($config["database"]))  $this->database = $config["database"];
		if(isset($config["charset"])) $this->charset = $config["charset"];
	}
	
    /**
     * Create new connection
     *
     * @return $this
     */
	public function connect(){
		try {
			$opt = array(
				PDO::ATTR_PERSISTENT => $this->attrPersistent
			);
			$dsn = sprintf("mysql:host=%s;dbname=%s",$this->servername,$this->database);
			$this->connection = new PDO($dsn,$this->username,$this->password,$opt);
			$this->charset($this->charset);
		}catch (PDOException $e){
			$this->error("Could not connect");
		}
	}
	
    /**
     * execute sql
     *
	 * param string $sql
     * @return $this
     */
	public function query($sql,$data = array()){
		if($data){
			$this->result = $this->connection->prepare($sql);
            $this->result->execute($data);
		}else{
			$this->result = $this->connection->query($sql);
		}
		return $this;
	}
	
    /**
     * execute sql
     *
	 * param string $sql
     * @return $this
     */
	public function execute($sql){
		return $this->connection->exec($sql);
	}

    /**
     * get data lists
     *
     * @return $this
     */
	public function select(){
		$list = array();
		while($row = $this->result->fetch(PDO::FETCH_ASSOC)){
			$list[] = $row;
		}
		return $list;
	}
	
    /**
     * get data first row
     *
     * @return $this
     */
	public function find(){
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}
	
    /**
     * get insert id
     *
     * @return int
     */
	public function insert(){
		return $this->connection->lastInsertId();
	}
	
    /**
     * Start transaction
     *
     * @return $this
     */
	public function startTrans(){
		$this->connection->beginTransaction();
	}
	
    /**
     * Transaction commit
     *
     * @return $this
     */
	public function commit(){
		$this->connection->commit();
	}
	
    /**
     * Transaction rollback
     *
     * @return $this
     */
	public function rollback(){
		$this->connection->rollBack();
	}
	
    /**
     * Transaction end
     *
     * @return $this
     */
	public function transEnd(){
		$this->connection->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
	}
	
    /**
     * charset
     *
     * @return $this
     */
	public function charset($charset){
		$this->connection->exec("SET names $charset");
	}
	
    /**
     * close connection
     *
     * @return $this
     */
	public function close(){
		$this->connection = null;
	}
	
    /**
     * mysql error
     *
	 * @param string $message 
     * @return void
     */
	public function error($message = null){
		$error = $this->connection->errorInfo();
		exit(sprintf("%s：%s",$message,$error));
	}
	
	public function __destruct(){
		$this->close();
	}
}

?>