<?php

namespace framework\Exception;

use framework\Http\Request;
use framework\View\View;
use Exception;

class Handler extends Exception{
	
	const E_ERROR = 1;
	const E_WARNING = 2;
	const E_PARSE = 4;
	const E_NOTICE = 8;
	const E_CORE_ERROR = 16;
	const E_CORE_WARNING = 32;
	const E_COMPILE_ERROR = 64;
	const E_COMPILE_WARNING = 128;
	const E_USER_ERROR = 256;
	const E_USER_WARNING = 512;
	const E_USER_NOTICE = 1024;
	const E_ALL = 2047;
	const E_STRICT = 2048;
	
	private $path = 'logs';
	
	public function __construct($message,$code = 0){
		$this->levels = array(
			self::E_ERROR => '致命错误(E_ERROR)',
			self::E_WARNING =>'警告(E_WARNING)',
			self::E_PARSE =>'语法解析错误(E_PARSE)',  
			self::E_NOTICE =>'提示(E_NOTICE)',  
			self::E_CORE_ERROR =>'E_CORE_ERROR',  
			self::E_CORE_WARNING =>'E_CORE_WARNING',  
			self::E_COMPILE_ERROR =>'编译错误(E_COMPILE_ERROR)', 
			self::E_COMPILE_WARNING =>'编译警告(E_COMPILE_WARNING)',  
			self::E_USER_ERROR =>'致命错误(E_USER_ERROR)',  
			self::E_USER_WARNING =>'警告(E_USER_WARNING)', 
			self::E_USER_NOTICE =>'提示(E_USER_NOTICE)',  
			self::E_ALL =>'E_ALL', 
			self::E_STRICT =>'E_STRICT'
		);
		parent::__construct($message,$code);
	}
   
	/**
	 * 获取错误级别
	 *
	 * @return string
	 */
	protected function getLevel(){
		return isset($levels[$this->getCode()]) ? $levels[$this->getCode()] : '未知错误';
	}
   
	/**
	 * 输出日志文件
	 *
	 * @return void
	 */
	protected function output(){
		$path = storage_path($this->path . date("/Ym/"));
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		$line = $this->getData();
		$filename = $path .date("Ymd").".log";
		@error_log(implode(" | ",$line)."\r\n",3,$filename); 
	}
	
	/**
	 * 显示调试页面
	 *
	 * @return void
	 */
	public function errorMessage(){
		
		$this->output();
		
		if(config("app.debug")){
 			$view = new View();
			$view->templatePath(__DIR__);
			$view->cachePath(__DIR__ . "/cache/");
			$view->assign("title",$this->getLevel());
			
			$line = $this->getData();
			$line["trace"] = $this->getTrace();
			unset($line["trace"][1]);
			$view->assign("row",$line);
			$view->display("views.error");
			exit;
			
		}else{
			return array(
				'code'=>$this->getCode(),
				'message'=>$this->getMessage()
			);
		}
	}
	
	/**
	 * 获取错误信息
	 *
	 * @return array
	 */
	protected function getData(){
		$line = array();
		$line['time'] = date("Y-m-d H:i:s");
		$line['code'] = $this->getCode();
		$line['ip'] = app('request')->getIp();
		$line['file'] = $this->getFile();
		$line['line'] = $this->getLine();
		$line['leave'] = $this->getLevel();
		$line['message'] = $this->getMessage();
		return $line;
	}
   
}
?>
