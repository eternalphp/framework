<?php

namespace framework\Logger;

use framework\Foundation\Application;
use framework\Http\Request;
use framework\View\View;

class Logger{
	
	/**
	 * 错误
	 * @var string
	 */
	const ERROR = 'ERROR';

	/**
	 * 警告
	 * @var string
	 */
	const WARN = 'WARN';

	/**
	 * 通知
	 * @var string
	 */
	const NOTICE = 'NOTICE';

	/**
	 * 调试信息
	 * @var string
	 */
	const INFO = 'INFO';

	/**
	 * SQL错误
	 * @var string
	 */
	const SQL = 'SQL';

	/**
	 * 异常
	 * @var string
	 */
	const EXCEPTION = 'EXCEPTION';

	/**
	 * 日志文件大小
	 * @var int
	 */
	const LOG_FILE_SIZE = 10097152;

	/**
	 * 日志信息
	 * @var array
	 */
	private $logs = array();
	
	/**
	 * 错误追溯信息
	 * @var array
	 */
	private $traces = array();

	/**
	 * 信息级别
	 * @var array
	 */
	private $levels = array('ERROR','WARN', 'INFO', 'SQL', 'EXCEPTION');
	
	private $app;
	private $request;
	
	public function __construct(Request $request){
		error_reporting(0);
		$this->app = Application::getInstance();
		$this->request = $request;
		
	}
	

	/**
	 * 初始化错误绑定函数和脚本终止前回调函数
	 *
	 * @return void
	 */
	public function init(){

		if($this->app->config("app.debug") == 1){
			$whoops = new \Whoops\Run;
			$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
			$whoops->register();
		}
		
		//错误处理绑定函数
		set_error_handler(array($this, 'errorHandler'));
		
		//注册页面脚本终止前回调函数
		register_shutdown_function(array($this, 'shutdonwHandler'));
	}


	/**
	 * 页面脚本终止前回调函数
	 *
	 * @return void
	 */
	public function shutdonwHandler(){
		$error = error_get_last();
		if ($error){
			$this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
		}
		
		if($this->app->config("app.log") == 1){
			$this->writeAccessLog(); // 记录访问日志
			$this->writeLog(); // 记录系统日志
		}
		
		if($this->app->config("app.debug") == 1){
			$this->output();
		}
	}
	

	/**
	 * 错误处理绑定函数
	 *
	 * @param int $error_no
	 * @param string $msg
	 * @param string $file
	 * @param int $line
	 * @param array $vars
	 * @return void
	 */
	public function errorHandler($errno, $errmsg, $file, $line){
		$this->traces['errno'] = $errno;
		$this->traces['errmsg'] = $errmsg;
		$this->traces['file'] = $file;
		$this->traces['line'] = $line;

		switch($errno){
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				$errmsg = mb_convert_encoding($errmsg, 'utf-8', 'gbk');
				$level = self::ERROR;
				break;
					
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
				$errmsg = mb_convert_encoding($errmsg, 'utf-8', 'gbk');
				$level = self::WARN;
				break;
					
			case E_NOTICE:
			case E_STRICT:
				$level = self::NOTICE;
				break;
					
			case E_USER_ERROR:
				$level = self::SQL;
				break;
					
			case self::EXCEPTION:
				$errno = 1000;
				$level = self::EXCEPTION;
				break;
					
			default :
				$level = self::INFO;
				break;
		}

		$separator = self::getSeparator();
		$errmsg = strip_tags(str_replace(array(chr(10), chr(13)), '', $errmsg));
		$errmsg = str_replace(chr(13).chr(10), '', $errmsg);
		$message = implode($separator,array("[$errno]",$errmsg,$file,$line));
		$this->record($message, $level);
	}


	/**
	 * 获取日志数据分隔符
	 *
	 * @return string
	 */
	private function getSeparator(){
		return ' | ';
	}
	
	
	/**
	 * 获取访问日志目录
	 *
	 * @param string $path
	 * @return string
	 */
	private function getAccessPath($path = ''){
		$paths = array('access');
		if($path != '') $paths[] = $path;
		return $this->app->storagePath(implode(DIRECTORY_SEPARATOR,$paths));
	}
	
	/**
	 * 获取错误日志目录
	 *
	 * @param string $path
	 * @return string
	 */
	private function getDebugPath($path = ''){
		$paths = array('debug');
		if($path != '') $paths[] = $path;
		return $this->app->storagePath(implode(DIRECTORY_SEPARATOR,$paths));
	}
	
	/**
	 * 获取存储目录
	 *
	 * @return string
	 */
	private function getPath(){
		return date("/Ym/");
	}
	
	/**
	 * 获取文件名
	 *
	 * @return string
	 */
	private function getFilename($format = 'Ymd'){
		return sprintf("%s.log",date($format));
	}

	/**
	 * 记录日志，过滤未经设置的级别
	 *
	 * @param string $message
	 * @param string $level
	 * @return void
	 */
	private function record($message, $level = self::ERROR){
		// 按日志级别来记录
		if(in_array($level, $this->levels)){
			$now = date("Y-m-d H:i:s");
			$this->logs[] = implode($this->getSeparator(),array("[$now]",$level,$message));
		}
	}


	/**
	 * 将程序中运行的各种类型信息保存到文件中
	 *
	 * @return void
	 */
	public function writeLog(){
		if($this->logs){
			$path = $this->getDebugPath($this->getPath());
			if(!file_exists($path)){
				mkdir($path,0777,true);
			}
			$filename = $path . $this->getFilename();
			if(file_exists($filename)){
				
				if(filesize($filename) >= self::LOG_FILE_SIZE){
					rename($filename,$path . $this->getFilename("YmdHis"));
				}
			}
			
			file_put_contents($filename,implode("\r\n",$this->logs)."\r\n",FILE_APPEND);
		}
	}
	
	/**
	 * 访问日志
	 *
	 * @return string
	 */
	private function writeAccessLog(){
		$now = date("Y-m-d H:i:s");
		$line = array();
		$line[] = "[$now]";
		$line[] = $this->request->getIp();
		$line[] = $this->request->fullUrl();
		$access = implode(self::getSeparator(),$line);
		$path = $this->getAccessPath($this->getPath());
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		$filename = $path . $this->getFilename();
		file_put_contents($filename,$access ."\r\n",FILE_APPEND);
	}

	/**
	 * 输出客户端访问信息
	 *
	 * @return void
	 */
	public function output(){
		if($this->logs){
			$view = new View();
			$view->templatePath(__DIR__);
			$view->cachePath(__DIR__ . "/cache/");
			ob_clean();
			$view->assign("title","NOTICE");
			$view->assign("message",implode("<br><br>",$this->logs));
			$view->display("views.notice");
			exit;
		}
	}

	/**
	 * 记录SQL错误信息
	 * 
	 * @param string $message
	 * @return void
	 */
	public static function sql($message){
		trigger_error($message, E_USER_ERROR);
	}


	/**
	 * 记录调式信息
	 * 
	 * @param string $message
	 * @return void
	 */
	public static function info($message){
		trigger_error($message, E_USER_NOTICE);
	}
}
?>