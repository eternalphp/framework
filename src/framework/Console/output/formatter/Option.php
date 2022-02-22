<?php

namespace framework\Console\output\formatter;

class Option
{
     /**
     * 样式名称
     */
	private $name;
	
    /**
     * 样式值
     */
	private $code;
	
	private $unsetCode;
	
	public function __construct(string $name,int $code,int $unsetCode){
		$this->name = $name;
		$this->code = $code;
		$this->unsetCode = $unsetCode;
	}
	
    /**
     * 获取样式名称
     * @return string
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * 获取样式值
     * @return string
     */
	public function getCode(){
		return $this->code;
	}
	
    /**
     * 获取样式值
     * @return string
     */
	public function getUnsetCode(){
		return $this->unsetCode;
	}
}
