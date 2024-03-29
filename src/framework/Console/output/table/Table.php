<?php

namespace framework\Console\output\table;

use framework\Console\Output;
use framework\Console\output\formatter\Style;
use framework\Exception\InvalidArgumentException;
use framework\Console\output\Formatter;


class Table
{
	
    /**
     * 行
     */
	private $rows = [];
	private $header;
	private $rowStyle = '-';
	private $output;
	
	public function __construct(Style $style = null){
		$this->rows = $rows;
		$this->output = new Output();
		if($style != null){
			$this->style = $style;
		}
	}
	
    /**
     * 添加行对象
     * @return string
     */
	public function addRow(array $values = []){
		$row = new Row();
		foreach($values as $value){
			$value = empty($value) ? "" : $value;
			$row->addColumn($value);
		}
		$this->rows[] = $row;
		return $this;
	}
	
    /**
     * 设置表头
     * @return void
     */
	public function setHeader(array $values = []){
		$row = new Row();
		foreach($values as $value){
			$row->addColumn($value);
		}
		$this->header = $row;
		$row->setStyle(function($style){
			$style->setForeground('green');
			$style->setBackground('default');
		});
	}
	
    /**
     * 获取行中所有列
     * @return array
     */
	public function getRows(){
		return $this->rows;
	}
	
    /**
     * 获取行中最长的列宽
     * @return int
     */
	public function getMaxColWidth($index = 0){
		$maxWidth = 0;
		if($this->rows){
			foreach($this->rows as $row){
				$width = $row->getColumn($index)->getColWidth();
				if($width > $maxWidth){
					$maxWidth = $width;
				}
			}
		}
		
		return $maxWidth;
	}
	
    /**
     * 重置所有列宽
     * @return void
     */
	public function setColWidth(){
		if($this->rows){
			foreach($this->rows as $row){
				foreach($row->getColumns() as $index=>$column){
					$width = $this->getMaxColWidth($index);
					$column->setDisplayColWidth($width + 10);
				}
			}
		}
		
		if($this->header instanceof Row){
			foreach($this->header->getColumns() as $index=>$column){
				$width = $this->getMaxColWidth($index);
				$column->setDisplayColWidth($width + 10);
			}
		}
	}
	
    /**
     * 获取行宽
     * @return int
     */
	public function getRowWidth(){
		$width = 0;
		foreach($this->rows[0]->getColumns() as $column){
			$width = $width + $column->getDisplayColWidth() + 1;
		}
		return $width;
	}
	
	public function displayRowLine($rowWidth,$style = null){
		$style = $style ? $style : $this->rowStyle;
		$this->write(str_pad($style,$rowWidth,$style));
	}
	
	public function write($message){
		$message = $this->style->text($message);
		$this->output->write($message);
	}
	
    /**
     * 打印表格
	 * @param bool showLine
     * @return output
     */
	public function display($showLine = true){

		$this->setColWidth();
		$rowWidth = $this->getRowWidth() + 1;
		
		if($this->header instanceof Row){
			if($showLine) $this->displayRowLine($rowWidth);
			$header = $this->header->getRow();
			
			//表头设置样式
			preg_match_all('/(\w)+/',$header,$matchs);
			foreach($matchs[0] as $text){
				$header = str_replace($text,$this->header->getStyle()->text($text),$header);
			}
			
			$this->write($header);
			$this->displayRowLine($rowWidth,'=');
		}else{
			if($showLine) $this->displayRowLine($rowWidth);
		}
		
		if($this->rows){
			foreach($this->rows as $row){
				$this->write($row->getRow());
				if($showLine) $this->displayRowLine($rowWidth);
			}
		}
	}
}
