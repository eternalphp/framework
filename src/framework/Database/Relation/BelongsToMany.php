<?php

/*************************************
  * @模块：      模型关联关系
  * @说明：      用于构造关联对象
  * @作者:       564165682@qq.com
  * @日期:       2021-12-24
  
**************************************/

namespace System\Core\Relation;

use framework\Database\Eloquent\Model;
use framework\Container\Container;


if(!defined('BASEPATH')) exit('No direct script access allowed');

class BelongsToMany extends Relation{
	
	public function __construct(Model $parent, string $model, string $middle, string $foreignKey, string $localKey){
		$this->parent = $parent;
		$this->model = $this->parseModel($model);
		$this->middle = $this->parseModel($middle);
		$this->foreignKey = $foreignKey;
		$this->localKey = $localKey;
		$this->createModel();
		$this->createMiddleModel();
	}
	
    /**
     * 创建查询实例对象
     * @access public
     * @return Model
     */
	public function createMiddleModel(){	
		Loader::getContainer()->bind($this->middle,array(
			'class'=>$this->middle
		));
	}
	
    /**
     * 获取当前的关联模型类的实例
     * @access public
     * @return Model
     */
	public function getMiddleModel(){
		return Loader::getContainer()->get($this->middle);
	}
	
	public function belongsToManyQuery(){
		
		$table = $this->getModel()->tableName();
		$fulltable = $this->getModel()->fullTableName();
		$middleTable = $this->getMiddleModel()->tableName('t1');
		$field = $this->getForeignKey();
		$primaryKey = $this->getMiddleModel()->primaryKey();
		$localKey = $this->getLocalKey();
		
		return $this->getModel()
		->join($middleTable,"$fulltable.$localKey=t1.$localKey")
		->field("$fulltable.*")
		->getSubQuery(function($query){
			return $query;
		});
	}
}

?>