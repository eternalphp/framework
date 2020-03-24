<?php

namespace framework\Database\Connection;

use framework\Database\Connection\ConnectorInterface;


class MySqlConnector implements ConnectorInterface
{
	private $servername = null;
	private $username = null;
	private $password = null;
	private $port = 3306;
	private $database = null;
	private $charset = 'utf-8';
	private $connection = null;
	
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
		$this->connection = mysql_connect($this->servername,$this->username,$this->password);
		if(!$this->connection){
			$this->error("Could not connect");
		}
		if(!mysql_select_db($this->database,$this->connection)){
			$this->error("Can't use $this->database");
		}
		$this->charset($this->charset);
	}
	
    /**
     * close connection
     *
     * @return $this
     */
	public function close(){
		return mysql_close($this->connection);
	}
	
    /**
     * execute sql
     *
	 * param string $sql
     * @return $this
     */
	public function query($sql){
		$this->result = mysql_query($sql,$this->connection);
		if(!$this->result){
			$this->error("$sql");
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
		return mysql_query($this->connection,$sql);
	}
	
    /**
     * get data lists
     *
     * @return $this
     */
	public function select(){
		$list = array();
		while(($row = mysql_fetch_assoc($this->result)) != false){
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
		$row = mysql_fetch_assoc($this->result);
		return $row;
	}
	
    /**
     * get insert id
     *
     * @return int
     */
	public function insert(){
		return mysql_insert_id($this->connection);
	}
	
    /**
     * Start transaction
     *
     * @return void
     */
	public function startTrans(){
		$this->query("BEGIN");
	}

    /**
     * Transaction commit
     *
     * @return void
     */
	public function commit(){
		$this->query("COMMIT");
	}

    /**
     * Transaction rollback
     *
     * @return void
     */
	public function rollback(){
		$this->query("ROLLBACK");
	}

    /**
     * Transaction end
     *
     * @return void
     */
	public function transEnd(){
		$this->query("END");
	}
	
    /**
     * charset
     *
	 * @param string $charset
     * @return void
     */
	public function charset($charset){
		$this->query("set names '$charset'");
	}
	
    /**
     * escape
     *
	 * @param string $string
     * @return string
     */
	public function escape($string){
		if($string != ''){
			$string = mysql_real_escape_string($string);
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
		$error = mysql_error();
		exit(sprintf("%s：%s",$message,$error));
	}
	
	public function __destruct(){
		$this->close();
	}
}

?>