<?php

namespace framework\Exception;

use Exception;

class Handler extends Exception{
	
	private $level = 0;
	protected $heading = '';
	protected $message = '';
	private $path = 'logs';
	
	function __construct($message){
		$this->level = ob_get_level();
		parent::__construct($message,$this->level);
	}
   
	protected function getLevel(){
		$levels = array(
			1=>'致命错误(E_ERROR)',
			2 =>'警告(E_WARNING)',
			4 =>'语法解析错误(E_PARSE)',  
			8 =>'提示(E_NOTICE)',  
			16 =>'E_CORE_ERROR',  
			32 =>'E_CORE_WARNING',  
			64 =>'编译错误(E_COMPILE_ERROR)', 
			128 =>'编译警告(E_COMPILE_WARNING)',  
			256 =>'致命错误(E_USER_ERROR)',  
			512 =>'警告(E_USER_WARNING)', 
			1024 =>'提示(E_USER_NOTICE)',  
			2047 =>'E_ALL', 
			2048 =>'E_STRICT'
		);
		return isset($levels[$this->level]) ? $levels[$this->level] : '未知错误';
   }
   
	private function output(){
		$path = ROOT . $this->path . date("Ym/");
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		
		$line = array();
		$line['time'] = date("Y-m-d H:i:s");
		$line['level'] = $this->getLevel();
		$line['ip'] = get_ip();
		$line['file'] = parent::getFile();
		$line['line'] = parent::getLine();
		$line['message'] = parent::getMessage();
		
		$filename = $path .date("Ymd").".log";
		@error_log(implode(" | ",$line)."\r\n",3,$filename); 
	}
	
	public function getMessage(){
		if(Config("debug") == 1){
			ob_end_clean();
			include("./views/layout.blade.php");
			exit;
		}else{
			$this->output();
			exit;
		}
	}
   
}
?>
