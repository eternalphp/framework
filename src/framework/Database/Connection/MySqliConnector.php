<?php

namespace framework\Database\Connection;

use framework\Database\Connection\ConnectorInterface;
use framework\Exception\DatabaseException;

class MySqliConnector implements ConnectorInterface
{
	private $servername = null;
	private $username = null;
	private $password = null;
	private $port = 3306;
	private $database = null;
	private $charset = 'utf8';
	private $connection = null;
	private $result = null;
	
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
		$this->connection = mysqli_connect($this->servername,$this->username,$this->password,$this->database,$this->port);
		if(!$this->connection){
			$this->error("Could not connect：". mysqli_connect_error());
		}
		$this->charset($this->charset);
	}
	
    /**
     * close connection
     *
     * @return $this
     */
	public function close(){
		return mysqli_close($this->connection);
	}
	
    /**
     * execute sql
     *
	 * param string $sql
     * @return $this
     */
	public function query($sql){
		$this->result = mysqli_query($this->connection,$sql);
		if(!$this->result){
			$this->error("$sql");
		}
	}
	
    /**
     * execute sql
     *
	 * param string $sql
     * @return $this
     */
	public function execute($sql){
		return mysqli_query($this->connection,$sql);
	}

    /**
     * get data lists
     *
     * @return $this
     */
	public function select(){
		$list = array();
		while(($row = mysqli_fetch_assoc($this->result)) != false){
			$list[] = $row;
		}
		mysqli_free_result($this->result);
		return $list;
	}
	
    /**
     * get data first row
     *
     * @return $this
     */
	public function find(){
		$row = mysqli_fetch_assoc($this->result);
		return $row;
	}
	
    /**
     * get insert id
     *
     * @return int
     */
	public function insert(){
		return mysqli_insert_id($this->connection);
	}
	
    /**
     * Start transaction
     *
     * @return void
     */
	public function startTrans(){
		mysqli_autocommit($this->connection,false);
	}
	
    /**
     * Transaction commit
     *
     * @return void
     */
	public function commit(){
		mysqli_commit($this->connection);
	}
	
    /**
     * Transaction rollback
     *
     * @return void
     */
	public function rollback(){
		mysqli_rollback($this->connection);
	}
	
    /**
     * Transaction end
     *
     * @return void
     */
	public function transEnd(){
		mysqli_autocommit($this->connection,true);
	}
	
    /**
     * charset
     *
	 * @param string $charset
     * @return void
     */
	public function charset($charset){
		mysqli_set_charset($this->connection,$charset);
	}
	
    /**
     * escape
     *
	 * @param string $string
     * @return string
     */
	public function escape($string){
		if($string != ''){
			$string = mysqli_real_escape_string($this->connection,$string);
		}
		return $string;
	}
	
    /**
     * mysql error
     *
	 * @param string $message 
     * @return void
     */
	public function error($message = null){
		if($this->connection != null){
			$error = mysqli_error($this->connection);
		}else{
			$error = mysqli_connect_error();
		}
		throw new DatabaseException(sprintf("%s：%s",$message,$error));
	}
	
	public function __destruct(){
		$this->close();
	}
}

?>