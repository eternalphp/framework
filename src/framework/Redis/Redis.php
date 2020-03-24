<?php

namespace framework\Redis;

class Redis
{
	
	private $client;
	
	public function __construct($client){
		$this->client = $client;
	}
	
    /**
     * Get value from redis
     *
     * @param  string  $key
     * @return string
     */
	public function get($key,$default = null){
		$value = $this->client->get($key);
		if(!$value){
			return $default;
		}
		return $value;
	}
	
    /**
     * Set value to redis
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
	public function set($key,$value){
		return $this->client->set($key,$value);
	}
	
    /**
     * Set value to redis
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
	public function add($key,$value){
		return $this->client->setnx($key,$value);
	}
	
    /**
     * delete value from redis
     *
     * @param  string  $key
     * @return bool
     */
	public function delete($key){
		return $this->client->delete($key);
	}
	
    /**
     * Determine if the given keys exist.
     *
     * @return bool
     */
	public function exists($key){
		return $this->client->exists($key);
	}
	
    /**
     * get values from redis
     *
     * @return array | bool
     */
	public function getVals(){
		$keys = func_get_args();
		if(count($keys) > 0){
			return $this->client->getMultiple($keys);
		}else{
			return false;
		}
	}
	
    /**
     * Determine if the given keys exist.
     *
     * @return bool
     */
	public function incr($key){
		return $this->client->incr($key);
	}
	
    /**
     * Determine if the given keys exist.
     *
     * @return bool
     */
	public function decr($key){
		return $this->client->decr($key);
	}
	
    /**
     * close redis
     *
     * @return bool
     */
	public function close(){
		return $this->client->close();
	}
}
?>