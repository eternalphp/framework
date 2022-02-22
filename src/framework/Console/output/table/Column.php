<?php

namespace framework\Console\output\table;

use framework\Exception\InvalidArgumentException;

class Column
{
	
    /**
     * 列
     */
	private $value;
	private $colWidth = 0;
	private $displayColWidth = 0;
	
	public function __construct(string $value){
		$this->value = $value;
		$this->colWidth = strlen($this->value);
	}
	
    /**
     * 获取列数据
     * @return string
     */
	public function getValue(){
		return $this->value;
	}
	
    /**
     * 获取列宽度
     * @return int
     */
	public function getColWidth(){
		return $this->colWidth;
	}
	
    /**
     * 设置列宽度
     * @return int
     */
	public function setDisplayColWidth($width){
		$this->displayColWidth = $width;
		return $this;
	}
	
    /**
     * 获取列宽度
     * @return int
     */
	public function getDisplayColWidth(){
		return $this->displayColWidth;
	}
}
