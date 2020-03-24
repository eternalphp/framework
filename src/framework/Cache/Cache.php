<?php

namespace framework\Cache;
use framework\Cache\CacheInterface;
use framework\Cache\CacheFile;

class Cache implements CacheInterface
{
	private $cache;
	
	public function __construct(CacheInterface $cache){
		$this->cache = $cache;
	}
	
	public function get($key,$default = null){
		return $this->cache->get($key,$default);
	}
	
	public function set($key,$value){
		return $this->cache->set($key,$value);
	}
	
	public function remove($key){
		return $this->cache->remove($key);
	}
	
	public function clear(){
		return $this->cache->clear();
	}
}
?>