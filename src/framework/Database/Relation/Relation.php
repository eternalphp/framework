<?php

/*************************************
  * @模块：      模型关联关系
  * @说明：      用于构造关联对象
  * @作者:       564165682@qq.com
  * @日期:       2021-12-24
  
**************************************/

namespace framework\Database\Relation;

use framework\Database\Eloquent\Model;
use framework\Container\Container;

abstract class Relation{
	
    /**
     * 父模型对象
     * @var Model
     */
    protected $parent;

    /**
     * 当前关联的模型类名
     * @var string
     */
    protected $model;

    /**
     * 关联模型查询对象
     * @var Query
     */
    protected $query;

    /**
     * 关联表外键
     * @var string
     */
    protected $foreignKey;

    /**
     * 关联表主键
     * @var string
     */
    protected $localKey;

	public function __construct(){

	}
	
    /**
     * 获取关联的所属模型
     * @access public
     * @return Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * 获取当前的关联模型类的Query实例
     * @access public
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * 获取关联表外键
     * @access public
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * 获取关联表主键
     * @access public
     * @return string
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }
	
    /**
     * 创建查询实例对象
     * @access public
     * @return Model
     */
	protected function createModel(){	
		Container::getInstance()->bind($this->model,array(
			'class'=>$this->model
		));
	}
	
    /**
     * 获取当前的关联模型类的实例
     * @access public
     * @return Model
     */
	public function getModel(){
		return Container::getInstance()->get($this->model);
	}
	
    /**
     * 解析当前关联模型类名
     * @access public
     * @return Model
     */
	protected function parseModel($model){
		if (false === strpos($model, '\\')) {
			$models = explode("\\",$model);
			return implode("\\",$models);
		}
		
		return $model;
	}
}

?>