<?php

namespace framework\Cache;
use framework\Cache\CacheInterface;
use framework\Filesystem\Filesystem;

class CacheFile implements CacheInterface
{
	
	private $path;
	private $expire;
	private $prefix;
	private $directory;
	private $handler;
	
	public function __construct($directory){
		$this->expire = 120;
		$this->handler = new Filesystem();
		$this->directory = $directory;
	}
	
    /**
     * Get value from cache
     *
     * @param  string  $key
     * @return string
     */
	public function get($key,$default = null){
		$path = $this->path($key);
		
		if(!$this->handler->exists($path)){
			return $default;
		}
		
		$content = $this->handler->get($path);
		$time = (int)substr($content,0,10);
		if(time() > $time){
			$this->remove($key);
			return $default;
		}
		
		$data = substr($content,10);
		if(function_exists('gzuncompress')){
			$data = gzuncompress($data);
		}
		return unserialize($data);
	}
	
    /**
     * Set value to cache file
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
	public function set($key,$value){
		$path = $this->path($key);
		$data = serialize($value);
		if(function_exists('gzcompress')){
			$data = gzcompress($data,3);
		}
		$time = $this->expiration($this->expire);
		$this->handler->put($path,$time.$data,true);
	}
	
    /**
     * delete value from cache file
     *
     * @param  string  $key
     * @return bool
     */
	public function remove($key){
		$path = $this->path($key);
		return $this->handler->delete($path);
	}
	
    /**
     * delete all cache file
     *
     * @return bool
     */
	public function clear(){
		return $this->handler->delete($this->directory);
	}
	
    /**
     * Get the full path for the given cache key.
     *
     * @param  string  $key
     * @return string
     */
    protected function path($key)
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

        return $this->directory.'/'.implode('/', $parts).'/'.$hash;
    }
	
    /**
     * Set expire to cache file.
     *
     * @param  int  $expire
     * @return $this
     */
	public function expire($expire){
		$this->expire = $expire;
		return $this;
	}
	
    /**
     * Set directory to cache file.
     *
     * @param  string  $directory
     * @return $this
     */
	public function directory($directory){
		$this->directory = $directory;
		return $this;
	}
	
    /**
     * Get the expiration time based on the given minutes.
     *
     * @param  float|int  $minutes
     * @return int
     */
	protected function expiration($minutes){
		$time = time() + $minutes*60;
		return $minutes === 0 || $time > 9999999999 ? 9999999999 : (int) $time;
	}
}
?>