<?php

namespace framework\Database\Eloquent;

class pagination{
	
	private $rows;
	private $pageSize;
	private $currentPage;
	private $totalPage;
	private $offset;
	private $hasPage = true;

	public function __construct($rows,$pageSize = 30){
		$this->pageSize = $pageSize;
		$this->rows = $rows;
		
		$this->totalPage = max(ceil($this->rows / $this->pageSize), 1); //总页数
		
		$page = requestInt("page",1);
		$this->currentPage = max(min($this->totalPage, $page), 1); //当前页
		
		if($page <= $this->totalPage){

			$this->offset = ($this->currentPage - 1) * $this->pageSize;
			
		}else{
			$this->hasPage = false;
		}
	}
	
	public function count(){
		return $this->rows;
	}

	public function currentPage(){
		return $this->currentPage;
	}
	
	public function lastPage(){
		return $this->totalPage;
	}
	
	public function total(){
		return $this->totalPage;
	}
	
	public function pageSize(){
		return $this->pageSize;
	}
	
	public function offset(){
		return $this->offset;
	}
	
	public function hasPage(){
		return $this->hasPage;
	}
	
	function __destruct(){
		
	}
}
?>