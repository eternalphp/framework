<?php

namespace framework\Support;


class Arr
{
	
	private $arrayList;
	private $handle;
	
	public function __construct(Array $array = []){
		$this->arrayList = $array;
	}
	
    /**
     * add value to array
     * @param string $key
	 * @param string $value
     * @return $this
     */
	public function add($key,$value){
		if(is_null($this->get($key))){
			$this->arrayList[$key] = $value;
		}
		return $this;
	}
	
    /**
     * push value to array
     * @param string | array $values
     * @return $this
     */
	public function push($values){
		if(is_array($values)){
			foreach($values as $value){
				$this->arrayList[] = $value;
			}
		}else{
			$this->arrayList[] = $values;
		}
		return $this;
	}
	
    /**
     * find key form array
     * @param string $key
     * @return bool
     */
	public function exists($key){
		return array_key_exists($key, $this->arrayList);
	}
	
    /**
     * get value of array 
     * @param string $key
	 * @param string $default
     * @return string | $default
     */
	public function get($key,$default = null){
		
		if(is_null($key)){
			return $this->arrayList;
		}
		
		if($this->exists($key)){
			return $this->arrayList[$key];
		}
		
		$this->handle = &$this->arrayList;
		$keys = explode(".",$key);
		
		while(count($keys) > 1){
			$key = array_shift($keys);
			if(isset($this->handle[$key])){
				$this->handle = &$this->handle[$key];
			}else{
				return $default;
			}
		}
		
		$key = array_shift($keys);
		if(isset($this->handle[$key])){
			return $this->handle[$key];
		}else{
			return $default;
		}
		
		return $this;
	}
	
    /**
     * update value to array 
     * @param string $key
	 * @param string $value
     * @return array
     */
	public function set($key,$value){
		
		if(is_null($key)){
			return $this->arrayList = $value;
		}
		
		$this->handle = &$this->arrayList;
		$keys = explode(".",$key);
		
		while(count($keys) > 1){
			$key = array_shift($keys);
			if(!isset($this->handle[$key]) || !is_array($this->handle[$key])){
				$this->handle[$key] = array();
			}
			$this->handle = &$this->handle[$key];
		}
		
		$this->handle[array_shift($keys)] = $value;
		
		return $this->arrayList;
	}
	
    /**
     * remove value from array 
     * @param string $key
     * @return bool
     */
	public function remove($key){
				
		$this->handle = &$this->arrayList;
		$keys = explode(".",$key);
		
		while(count($keys) > 1){
			$key = array_shift($keys);
			if(isset($this->handle[$key])){
				$this->handle = &$this->handle[$key];
			}else{
				return false;
			}
		}
		
		$key = array_shift($keys);
		if(isset($this->handle[$key])){
			unset($this->handle[$key]);
		}
		
		return true;
	}
	
    /**
     * get list from array 
     * @param string $key
	 * @param string $value
     * @return array
     */
	public function toList($key,$value = null){
		$data = array();
		foreach($this->arrayList as $k=>$val){
			if(is_null($value)){
				$data[] = $val[$key];
			}else{
				$data[$val[$key]] = $val[$value];
			}
		}
		return $data;
	}
}
?>