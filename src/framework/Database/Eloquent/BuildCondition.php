<?php

namespace framework\Database\Eloquent;

class BuildCondition{
	
	private $conditions = []; //条件语句集合

	public function __construct(){
		
	}

    /**
     * Set the condition of the query statement
     * @param string $condition
	 * @param string|int ...
     * @return $this
     */
	public function where(){
		$args = func_get_args();
		$command = $args[0];
		unset($args[0]);
		$args = array_values($args);
		
		$params = array();
		
		if(func_num_args() > 1){
			switch($command){
				case 'in':
					foreach($args[0] as $key => $val){
						$params[] = $key;
						$params[] = $val;
					}
					call_user_func_array(array($this,'whereIn'),$params);
				break;
				case 'not in':
					foreach($args[0] as $key => $val){
						$params[] = $key;
						$params[] = $val;
					}
					call_user_func_array(array($this,'whereNotIn'),$params);
				break;
				case 'between':
					foreach($args[0] as $key => $val){
						$params[] = $key;
						$params[] = $val;
					}
					call_user_func_array(array($this,'between'),$params);
				break;
				case 'like':
					if(isset($args[0]['field'])){
						$params[] = implode(",",$args[0]['field']);
						$params[] = "'%".$args[0]['data']."%'";
					}else{
						foreach($args[0] as $key => $val){
							$params[] = $key;
							$params[] = $val;
						}
					}
					call_user_func_array(array($this,'whereLike'),$params);
				break;
				case 'not like':
					if(isset($args[0]['field'])){
						$params[] = implode(",",$args[0]['field']);
						$params[] = "'%".$args[0]['data']."%'";
					}else{
						foreach($args[0] as $key => $val){
							$params[] = $key;
							$params[] = $val;
						}
					}
					call_user_func_array(array($this,'whereNotLike'),$params);
				break;
				default:
					if(strstr($command,'%s') || strstr($command,'%d')){
						$this->conditions[] = call_user_func_array('sprintf',func_get_args()); 
					}else{
						
						$params = func_get_args();
						$this->conditions[] = sprintf("%s='%s'",$params[0],$params[1]);
					}
			}

		}else{
			$this->conditions[] = $command;
		}
		
		return $this;
	}
	
    /**
     * Set the condition of the query statement
     * @param string $field
	 * @param array $values
     * @return $this
     */
	public function whereIn($field,$values = array()){
		if($values){
			foreach($values as $k=>$val){
				$values[$k] = sprintf("'%s'",$val);
			}
		}
		$this->conditions[] = sprintf("%s in (%s)",$field,implode(",",$values));
		
		return $this;
	}
	
    /**
     * Set the condition of the query statement
     * @param string $field
	 * @param array $values
     * @return $this
     */
	public function whereNotIn($field,$values = array()){
		if($values){
			foreach($values as $k=>$val){
				$values[$k] = sprintf("'%s'",$val);
			}
		}
		$this->conditions[] = sprintf("%s not in (%s)",$field,implode(",",$values));
		
		return $this;
	}
	
    /**
     * Set the condition of the query statement
     * @param string $field
	 * @param array $values
     * @return $this
     */
	public function between($field,$values = array()){
		if($values){
			foreach($values as $k=>$val){
				$values[$k] = sprintf("'%s'",$val);
			}
		}
		$this->conditions[] = sprintf("%s between %s",$field,implode(" and ",$values));
		
		return $this;
	}
	
    /**
     * Set the condition of the query statement
     * @param string $field
	 * @param string $value
     * @return $this
     */
	public function whereLike($field,$value){
		
		$field = explode(",",$field);
		if(is_array($field)){
			$conditions = array();
			foreach($field as $name){
				$conditions[] = sprintf("%s like %s",$name,$value);
			}
			$this->conditions[] = sprintf("(%s)",implode(" or ",$conditions));
		}else{
			$this->conditions[] = sprintf("%s like %s",$field,$value);
		}

		return $this;
	}
	
    /**
     * Set the condition of the query statement
     * @param string $field
	 * @param string $value
     * @return $this
     */
	public function whereNotLike($field,$value){
		$this->conditions[] = sprintf("%s not like %s",$field,$value);
		
		return $this;
	}
	
    /**
     * Get the conditions of the query statement
     * @return array
     */
	public function getConditions(){
		return $this->conditions;
	}

	function __destruct(){
		unset($this->conditions);
	}
}
?>