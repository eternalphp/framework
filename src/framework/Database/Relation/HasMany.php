<?php

/*************************************
  * @模块：      模型关联关系
  * @说明：      用于构造关联对象
  * @作者:       564165682@qq.com
  * @日期:       2021-12-24
  
**************************************/

namespace framework\Database\Relation;

use framework\Database\Eloquent\Model;


if(!defined('BASEPATH')) exit('No direct script access allowed');

class HasMany extends Relation{
	
	public function __construct(Model $parent, string $model, string $foreignKey, string $localKey){
		$this->parent = $parent;
		$this->model = $this->parseModel($model);
		$this->foreignKey = $foreignKey;
		$this->localKey = $localKey;
		$this->createModel();
	}
}

?>