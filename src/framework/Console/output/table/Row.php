<?php

namespace framework\Console\output\table;

use framework\Exception\InvalidArgumentException;

class Row
{
	
    /**
     * 列
     */
	private $columns = [];
	
	private $cloumnStyle = '│';
	
	public function __construct(array $columns = []){
		$this->columns = $columns;
	}
	
    /**
     * 获取列数据
     * @return string
     */
	public function addColumn(string $value){
		$this->columns[] = new Column($value);
		return $this;
	}
	
    /**
     * 获取行中所有列
     * @return array
     */
	public function getColumns(){
		return $this->columns;
	}
	
    /**
     * 获取行中所有列
     * @return array
     */
	public function getColumn($index = 0){
		return $this->columns[$index];
	}
	
    /**
     * 获取行中最长的列宽
     * @return int
     */
	public function getMaxColWidth(){
		$maxWidth = 0;
		if($this->columns){
			foreach($this->columns as $column){
				$maxWidth = $column->getColWidth() > $maxWidth ? $column->getColWidth() : $maxWidth;
			}
		}
		
		return maxWidth;
	}
	
	public function getRow(){
		$elements = [];
		if($this->columns){
			foreach($this->columns as $column){
				$elements[] = str_pad(" " . $column->getValue(),$column->getDisplayColWidth()," ");
			}
		}
		
		return sprintf("%s%s%s",$this->cloumnStyle,implode($this->cloumnStyle,$elements),$this->cloumnStyle);
	}
}
