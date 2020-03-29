<?php

namespace framework\Session;

use SessionHandlerInterface;
use framework\Database\Connection\Connector;
use framework\Foundation\Manager;

class SessionManager extends Manager
{
	
	private $connector = null;
	
	public function getFileDriver(){
		return new FileSessionHandler($this->app->storagePath($this->app->config("session.sess_path")),$this->app->config("session.sess_expiration"));
	}
	
	public function getDatabaseDriver(){
		$connect = $this->getDatabaseConnector();
		return new DatabaseSessionHandler($connect,$this->app->config("session.sess_expiration"));
	}
	
	private function getDatabaseConnector(){
		$config = array(
			'driver'=>'MySqli',
			'servername'=>config("database.DB_HOST"),
			'username'=>config("database.DB_USER"),
			'password'=>config("database.DB_PWD"),
			'database'=>config("database.DB_NAME"),
			'port'=>config("database.DB_PORT"),
			'prefix'=>config("database.DB_PREFIX")
		);
		if($this->connector == null){
			$this->connector = new Connector($config);
			$this->connector->connect();
		}
		return $this->connector;
	}
	
	public function getDefaultDriver(){
		$driver = $this->app->config("session.driver");
		switch($driver){
			case 'file':
				return $this->getFileDriver();
			break;
			case 'database':
				return $this->getDatabaseDriver();
			break;
		}
	}
}
?>