<?php

namespace framework\View;


class View extends Engine
{
	
	public function __construct(){
		parent::__construct();
		$this->templatePath(ROOT . "/template");
		$this->cachePath(ROOT ."/cache");
	}
	
	public function assign($name,$value = null){
		$this->set($name,$value);
	}

	public function display($tFile){
		$this->load($tFile);
	}
}
?>