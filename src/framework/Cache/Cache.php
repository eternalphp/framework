<?php

namespace framework\Cache;

class Cache implements CacheInterface
{
	private $handler;
	
	public function __construct(CacheManager $cache){
		$this->handler = $cache->getDriver();
	}
	
    /**
     * Get value from cache
     *
     * @param  string  $key
     * @return string
     */
	public function get($key,$default = null){
		return $this->handler->get($key,$default);
	}
	
    /**
     * Set value to cache file
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
	public function set($key,$value){
		return $this->handler->set($key,$value);
	}
	
    /**
     * delete value from cache file
     *
     * @param  string  $key
     * @return bool
     */
	public function remove($key){
		return $this->handler->remove($key);
	}
	
    /**
     * delete all cache file
     *
     * @return bool
     */
	public function clear(){
		return $this->handler->clear();
	}
}
?>