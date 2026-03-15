<?php

/*************************************
  * @模块：      模型关联关系
  * @说明：      用于构造关联对象
  * @作者:       564165682@qq.com
  * @日期:       2021-12-24
  
**************************************/

namespace framework\Database\Relation;

use framework\Database\Eloquent\Model;

class BelongsToMany extends Relation{

	private $middle;

    /**
     * BelongsToMany constructor.
     * @param Model $parent
     * @param string $model 关联的表
     * @param string $middle 中间表
     * @param string $foreignKey 关联模型对应中间表的外键
     * @param string $localKey  当前模型表对应中间表的外键
     */
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
		app()->bind($this->middle,$this->middle);
	}
	
    /**
     * 获取当前的关联模型类的实例
     * @access public
     * @return Model
     */
	public function getMiddleModel(){
		return app()->get($this->middle);
	}
	
	public function belongsToManyQuery($id){

		$table = $this->getModel()->tableName();
		$fulltable = $this->getModel()->fullTableName();
		$middleTable = $this->getMiddleModel()->tableName('t1');
		$foreignKey = $this->getForeignKey();
		$primaryKey = $this->getModel()->primaryKey();//关联表主键
		$localKey = $this->getLocalKey();

		return $this->getModel()
		->join($middleTable,"$fulltable.$primaryKey=t1.$foreignKey")
        ->where("t1.$localKey",$id)
		->field("$fulltable.*")
		->getSubQuery(function($query){
			return $query;
		});
	}
}

?>