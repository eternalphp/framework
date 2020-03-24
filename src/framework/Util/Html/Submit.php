<?php

namespace framework\Util\Html;


class Submit extends Button
{
	
	private $name;
	private $id;
	private $text;
	private $type;
	private $options = array();
	
	public function __construct($id,$text = ''){
		parent::__construct($id,$text);
		$this->type = 'submit';
	}
	
}
?>