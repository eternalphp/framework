<?php

namespace framework\Session;

use SessionHandlerInterface;
use framework\Database\Connection\Connector;

class DatabaseSessionHandler implements SessionHandlerInterface
{
	private $table;
	private $minutes;
	private $connection;
	private $query;
	
	public function __construct($connection,$table,$minutes = 120){
		$this->connection = $connection;
		$this->table = $table;
		$this->minutes = $minutes;
		$this->query = $this->connection->table($this->table);
	}
	
    /**
     * open session
     *
     * @param string $save_path
	 * @param string $name
     * @return bool
     */
	public function open($save_path,$name){
		return true;
	}
	
    /**
     * read session
     *
     * @param string $sessionId
     * @return string
     */
	public function read($sessionId){
		$row = $this->query->where("sessionId",$sessionId)->find();
		if(!$row){
			return '';
		}
		
		if(time() - strtotime($row["updatetime"]) <= $this->minutes*60){
			return base64_decode($row["data"]);
		}else{
			return '';
		}
	}
	
    /**
     * write session
     *
     * @param string $sessionId
	 * @param string $data
     * @return bool
     */
	public function write($sessionId,$data){
		
		$row = $this->query->where("sessionId",$sessionId)->find();
		if(!$row){
			$this->query->insert(array(
				'sessionId'=>$sessionId,
				'data'=>base64_encode($data),
				'updatetime'=>date("Y-m-d H:i:s")
			));
		}else{
			$this->query->where("sessionId",$sessionId)->update(array(
				'data'=>base64_encode($data),
				'updatetime'=>date("Y-m-d H:i:s")
			));
		}
		
		return true;
	}
	
    /**
     * destroy session
     *
     * @param string $sessionId
     * @return bool
     */
	public function destroy($sessionId){
		$this->query->where("sessionId",$sessionId)->delete();
		return true;
	}
	
    /**
     * gc session
     *
     * @param int $lifetime
     * @return bool
     */
	public function  gc($lifetime){
		
		$this->query->where("updatetime<='%s'",date("Y-m-d H:i:s",time() - $lifetime))->delete();
		return true;
	}
	
    /**
     * close session
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }
}
?>