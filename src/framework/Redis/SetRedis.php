<?php

namespace framework\Redis;

class SetRedis
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
	public function sadd($key,$data = array()){
		$result = 0;
		if($data){
			foreach($data as $val){
				$result = $this->client->sadd($key,$val);
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
	public function spop($key){
		return $this->client->spop($key);
	}
	
    /**
     * get size of list from redis
     *
     * @param  string  $key
     * @return int
     */
	public function ssize($key){
		return $this->client->ssize($key);
	}
	
    /**
     * delete value from redis
     *
     * @param  string  $key
     * @return bool
     */
	public function sremove($key,$value){
		return $this->client->sremove($key,$value);
	}
	
    /**
     * move value from redis
     *
     * @param  string  $key1
     * @param  string  $key2
     * @param  string  $value
     * @return bool
     */
	public function move($key1,$key2,$value){
		return $this->client->smove($key1,$key2,$value);
	}
	
    /**
     * find value from redis
     *
     * @param  string  $key
     * @param  string  $value
     * @return bool
     */
	public function find($key,$value){
		return $this->client->scontains($key,$value);
	}
	
    /**
     * sinter value from redis
     *
     * @param  array  $keys
     * @return array
     */
	public function sinter($keys = array()){
		return call_user_func_array(array($this->client,'sinter'),$keys);
	}
	
    /**
     * sinterstore value from redis
     *
     * @param  string  $key
     * @param  array  $keys
     * @return array
     */
	public function sinterstore($key,$keys = array()){
		array_unshift($keys,$key);
		return call_user_func_array(array($this->client,'sinterstore'),$keys);
	}
	
    /**
     * sunion value from redis
     *
     * @param  array  $keys
     * @return array
     */
	public function sunion($keys = array()){
		return call_user_func_array(array($this->client,'sunion'),$keys);
	}
	
    /**
     * sunionstore value from redis
     *
     * @param  string  $key
     * @param  array  $keys
     * @return array
     */
	public function sunionstore($key,$keys = array()){
		array_unshift($keys,$key);
		return call_user_func_array(array($this->client,'sunionstore'),$keys);
	}
	
    /**
     * sdiff value from redis
     *
     * @param  array  $keys
     * @return array
     */
	public function sdiff($keys = array()){
		return call_user_func_array(array($this->client,'sdiff'),$keys);
	}
	
    /**
     * sdiffstore value from redis
     *
     * @param  string  $key
     * @param  array  $keys
     * @return array
     */
	public function sdiffstore($key,$keys = array()){
		array_unshift($keys,$key);
		return call_user_func_array(array($this->client,'sdiffstore'),$keys);
	}
	
    /**
     * smembers value from redis
     *
     * @param  string  $key
     * @return array
     */
	public function smembers($key){
		return $this->client->smembers($key);
	}
}
?>