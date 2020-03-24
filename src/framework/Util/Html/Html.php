<?php

namespace framework\Util\Html;


class Html
{
	
	private $sections = [];
	private $layout;
	private $filename;
	
	public function __construct(){
		
	}
	
	public function create($name,callable $callback){
		$table = new Table();
		call_user_func($table);
	}
}
?>