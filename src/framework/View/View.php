<?php

namespace framework\View;
use framework\Container\Container;

class View extends Engine
{
	
	public function __construct(){
		parent::__construct();
		$this->templatePath(app("Application")->appPath("views/template"));
		$this->cachePath(app("Application")->appPath("views/cache"));
	}
	
	/**
	 * set variable to template
	 * @param string $name
	 * @param string $value
	 * @return void;
	 */
	public function assign($name,$value = null){
		$this->set($name,$value);
	}

	/**
	 * load template file
	 * @param string $tFile
	 * @return void;
	 */
	public function display($tFile){
		$this->load($tFile);
	}
}
?>