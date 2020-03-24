<?php

namespace framework\Util\Html;


class Component
{
	
	private $name;
	private $data = array();
	
	public function __construct($name,$data = array()){
		$this->name = $name;
		$this->data = $data;
	}
}
?>