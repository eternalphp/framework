<?php

namespace framework\Logger;

use framework\Foundation\Application;
use framework\Http\Request;

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
	
	public function __construct(Application $app,Request $request){
		
		$this->app = $app;
		$this->request = $request;
		
	}
	

	/**
	 * 初始化错误绑定函数和脚本终止前回调函数
	 *
	 * @return void
	 */
	public function init(){
		
		//错误处理绑定函数
		set_error_handler(array(__CLASS__, 'errorHandler'));
		
		//注册页面脚本终止前回调函数
		register_shutdown_function(array(__CLASS__, 'shutdonwHandler'));
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
		
		if($this->app->config("config.access_log") == 1){
			$this->writeAccessLog(); // 记录访问日志
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
		
		if($this->app->config("config.debug_log") == 1){
			$this->writeLog(); // 记录系统日志
		}
		
		if($this->app->config("config.debug_mode") == 1){
			$this->output();
		}
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
		if(in_array($level, self::$levels)){
			$now = date("Y-m-d H:i:s");
			$this->logs[] = implode($this->getSeparator(),array("[$row]",$leave,$message));
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
			
			file_put_contents($filename,implode("\r\n",$this->logs),FILE_APPEND);
		}
	}
	
	/**
	 * 访问日志
	 *
	 * @return string
	 */
	private function writeAccessLog(){
		$now = date("Y-m-d H:i:s");
		RunTime::stop();
		$line = array();
		$line[] = "[$now]";
		$line[] = $this->request->getIp();
		$line[] = $this->request->fullUrl();
		$line[] = RunTime::spent();
		$access = implode(self::getSeparator(),$line);
		$path = $this->getAccessPath($this->getPath());
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		file_get_contents($path . $this->getFilename(),$access ."\r\n",FILE_APPEND);
	}

	/**
	 * 输出客户端访问信息
	 *
	 * @return void
	 */
	public function output(){
		if($this->logs){
			print_r($this->logs);
		}
	}

	/**
	 * 记录SQL错误信息
	 * 
	 * @param string $message
	 * @return void
	 */
	public static function sql($message){
		$debug_backtrace = debug_backtrace();
		trigger_error($message, E_USER_ERROR);
	}


	/**
	 * 记录调式信息
	 * 
	 * @param string $message
	 * @return void
	 */
	public static function info($message){
		$debug_backtrace = debug_backtrace();
		trigger_error($message, E_USER_NOTICE);
	}
	
	
	/**
	 * 显示错误追溯信息
	 */
	public function showDebugBackTrace(){
		$text1 = $text2 = null;
		$traceArr = (isset($this->traces['debug_backtrace']) && !empty($this->traces['debug_backtrace'])) ? $this->traces['debug_backtrace'] : debug_backtrace();
		if(!empty($this->traces['file'])){
			$text1 .= '<div class="info"><h1>('.$this->traces['error_no'].')'.$this->traces['message'].'</h1><div class="info2">FILE: '.$this->traces['file'].' &#12288;LINE:'.$this->traces['line'].'</div></div>';
		}else{
			$text1 .= '<div class="info"><h1>'.$this->traces['message'].'</h1></div>';
		}
		if(is_array($traceArr)){
			$text2 = '<div class="info"><p><strong>PHP Debug</strong></p><table cellpadding="5" cellspacing="1" width="100%" class="table"><tr class="bg2"><td>No.</td><td>File</td><td>Line</td><td>Code</td></tr>';
			$dapArr = array('halt()', 'Log::errorHandler()', 'Log::writeDebugLog()', 'Log::showDebugBackTrace()');
			foreach ($traceArr as $k=>$v){
				$file = isset($v['file']) ? $v['file'] : '';
				$line = isset($v['line']) ? $v['line'] : '';
				$function = isset($v['function']) ? $v['function'] : '';
				$class = isset($v['class']) ? $v['class'] : '';
				$type = isset($v['type']) ? $v['type'] : '';
				$callText ="{$class}{$type}{$function}()";
				if(in_array($callText, $dapArr)) continue;
				$text2 .= "<tr class='bg1'><td>".($k+1)."</td><td>{$file}</td><td>{$line}</td><td>{$callText}</td></tr>";
			}
			$text2 .= '</table></div><div class="help"><a href="http://www.easyz360.com">EasyPHP</a><sup>2.5</sup></div>';
		}
		
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				 <html>
				 <head>
					<title>System Error - EasyPHP Framework</title>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
					<style type="text/css">
					<!--
					body { background-color: white; color: black; font: 9pt/11pt verdana, arial, sans-serif;}
					#container { width: 1024px; }
					#message   { width: 1024px; color: black; }
					 .red  {color: red;}
					 a:link     { font: 9pt/11pt verdana, arial, sans-serif; color: red; }
					 a:visited  { font: 9pt/11pt verdana, arial, sans-serif; color: #4e4e4e; }
					 h1 { color: #FF0000; font: 18pt "Verdana"; margin-bottom: 0.5em;}
					.bg1{ background-color: #FFFFCC;}
					.bg2{ background-color: #EEEEEE;}
					.table {background: #AAAAAA; font: 11pt Menlo,Consolas,"Lucida Console"}
					.info {background: none repeat scroll 0 0 #F3F3F3;border: 0px solid #aaaaaa;border-radius: 10px 10px 10px 10px;color: #000000;font-size: 11pt;line-height: 160%;margin-bottom: 1em;padding: 1em;}
					.help {background: #F3F3F3;border-radius: 10px 10px 10px 10px;font: 12px verdana, arial, sans-serif;text-align: center;line-height: 160%;padding: 1em;}
					.info2 {background: none repeat scroll 0 0 #FFFFCC;border: 1px solid #aaaaaa;color: #000000;font: arial, sans-serif;font-size: 9pt;line-height: 160%;margin-top: 1em;padding: 4px;}
					-->
					</style>
				</head>
				<body>
				<div id="container">'.$text1.$text2.'</div>
				</body>
				</html>';
		exit($html);
	}
	
}
?>