<?php

namespace framework\Util\Components;

use framework\Util\Html\Component;

class Form extends Component
{
	
	private $name;
	private $action;
	private $sections = array();
	private $options = array();
	
	public function __construct($name = ''){
		$this->name = $name;
	}
	
	public function build(){
		
	}
	
	public function create(){
		
	}
}
?>