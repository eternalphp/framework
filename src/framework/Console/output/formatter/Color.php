<?php

namespace framework\Console\output\formatter;

use framework\Exception\InvalidArgumentException;

class Color
{
	
    /**
     * 颜色名称
     */
	private $name;
	
    /**
     * 颜色值
     */
	private $code;
	
	public function __construct(string $name,int $code){
		$this->name = $name;
		$this->code = $code;
	}
	
    /**
     * 获取颜色名称
     * @return string
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * 获取颜色值
     * @return string
     */
	public function getCode(){
		return $this->code;
	}
}
