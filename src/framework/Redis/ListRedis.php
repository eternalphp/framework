<?php

namespace framework\Redis;

class ListRedis
{
	
	private $client;
	
	public function __construct($client){
		$this->client = $client;
	}
	
    /**
     * push values to redis
     *
     * @param  string  $key
     * @param  array  $data
     * @return string
     */
	public function push($key,$data = array()){
		$result = 0;
		if($data){
			foreach($data as $val){
				$result = $this->client->lpush($key,$val);
			}
		}
		return $result;
	}
	
    /**
     * append values to redis
     *
     * @param  string  $key
     * @param  array  $data
     * @return string
     */
	public function append($key,$data = array()){
		$result = 0;
		if($data){
			foreach($data as $val){
				$result = $this->client->rpush($key,$val);
				if($result == false){
					return false;
				}
			}
		}
		return $result;
	}
	
    /**
     * pop values from redis
     *
     * @param  string  $key
     * @return string
     */
	public function pop($key){
		return $this->client->lpop($key);
	}
	
    /**
     * get size of list from redis
     *
     * @param  string  $key
     * @return int
     */
	public function size($key){
		return $this->client->lsize($key);
	}
	
    /**
     * get value of list from redis
     *
     * @param  string  $key
     * @param  int  $index
     * @return string
     */
	public function getList($key,$index = 0){
		return $this->client->lget($key,$index);
	}
	
    /**
     * update value of list from redis
     *
     * @param  string  $key
     * @return string
     */
	public function update($key,$value,$index = 0){
		return $this->client->lset($key,$index,$value);
	}
	
    /**
     * get values of list from redis
     *
     * @param  string  $key
     * @return string
     */
	public function getLists($key,$start = 0,$end = -1){
		return $this->client->lgetrange($key,$start,$end);
	}
	
    /**
     * delete value from redis
     *
     * @param  string  $key
     * @return bool
     */
	public function remove($key,$value,$count = 0){
		return $this->client->lremove($key,$value,$count);
	}
}
?>