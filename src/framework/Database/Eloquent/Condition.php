<?php

namespace framework\Database\Eloquent;


class Condition
{
	private $whereList = array();
	
	public function __construct(){
		
	}
	
	public function where(){
		$args = func_get_args();
		$num = func_num_args();
		switch($num){
			case 1:
				$this->whereList[] = $args[0];
			break;
			case 2:
			
				$handle = $args[0];
				$data = $args[1];
				switch($handle){
					case "like":
						if($data){
							foreach($data as $key=>$val){
								$this->like($key,$val);
							}
						}
					break;
					case "not like":
						if($data){
							foreach($data as $key=>$val){
								$this->notLike($key,$val);
							}
						}
					break;
					case "in":
						if($data){
							foreach($data as $key=>$val){
								$this->in($key,$val);
							}
						}
					break;
					case "not in":
						if($data){
							foreach($data as $key=>$val){
								$this->notIn($key,$val);
							}
						}
					break;
					case "between":
						if($data){
							foreach($data as $key=>$val){
								$this->between($key,$val);
							}
						}
					break;
					
					default:
						if(strstr($args[0],"%s") || strstr($args[0],"%d")){
							$this->whereList[] = call_user_func_array('sprintf',$args);
						}else{
							if(is_numeric($args[1])){
								$this->whereList[] = sprintf("%s=%d",$args[0],$args[1]);
							}else{
								$this->whereList[] = sprintf("%s='%s'",$args[0],$args[1]);
							}
						}
					break;
				}
			break;
			default:
				if(strstr($args[0],"%s") || strstr($args[0],"%d")){
					$this->whereList[] = call_user_func_array('sprintf',$args);
				}
			break;
		}
	}
	
	public function like($field,$value){
		if(is_array($value) && count($value) > 0){
			$likes = array();
			foreach($value as $val){
				$likes[] = "$field like '%".$val."%'";
			}
			$this->whereList[] = sprintf("(%s)",implode(" or ",$likes));
		}else{
			$this->whereList[] = "$field like '%".$value."%'";
		}
		return $this;
	}
	
	public function notLike($field,$value){
		if(is_array($value) && count($value) > 0){
			$likes = array();
			foreach($value as $val){
				$likes[] = "$field not like '%".$val."%'";
			}
			$this->whereList[] = sprintf("(%s)",implode(" or ",$likes));
		}else{
			$this->whereList[] = "$field not like '%".$value."%'";
		}
		return $this;
	}
	
	public function in($field,$value){
		if(is_array($value) && count($value) > 0){
			foreach($value as $k=>$val){
				if(is_string($val)){
					$value[$k] = sprintf('%s',$val);
				}
			}
			$this->whereList[] = sprintf("%s in (%s)",implode(",",$value));
		}else{
			$this->whereList[] = sprintf("%s in (%s)",$value);
		}
		return $this;
	}
	
	public function notIn($field,$value){
		if(is_array($value) && count($value) > 0){
			foreach($value as $k=>$val){
				if(is_string($val)){
					$value[$k] = sprintf('%s',$val);
				}
			}
			$this->whereList[] = sprintf("%s not in (%s)",implode(",",$value));
		}else{
			$this->whereList[] = sprintf("%s not in (%s)",$value);
		}
		return $this;
	}
	
	public function between($field,$value){
		if(is_array($value) && count($value) == 2){
			foreach($value as $k=>$val){
				if(is_string($val)){
					$value[$k] = sprintf('%s',$val);
				}
			}
			$this->whereList[] = sprintf("%s between %s and %s",$field,$value[0],$value[1]);
		}
		return $this;
	}
	
	public function getCondition(){
		return implode(" and ",$this->whereList);
	}
}
?>