<?php

namespace framework\View;
use framework\Container\Container;

class View extends Engine
{
	
	public function __construct(){
		parent::__construct();
		$app = Container::getInstance();
		$this->templatePath($app["application"]->appPath("views/template"));
		$this->cachePath($app["application"]->appPath("views/cache"));
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