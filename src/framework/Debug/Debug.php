<?php

namespace framework\Debug;

use Exception;

class Debug extends Exception{
	
	private $debugPath = '/debug/';
	private $format = 'Ym';
	private $splitStr = '--';
	protected $message;
	const DEBUG_FILE_SIZE = 1024*1024*10;
	
	public function __construct($message,$print = false){
		
		$this->message = $message;
		$this->debugPath   = storage_path($this->debugPath . date($this->format) . '/');
		if($print == false){
			$this->output();
		}else{
			$this->show();
		}
		
	}
	
	private function getDebugData(){
		$line = array();
		$line[] = date("Y-m-d H:i:s");
		$line[] = parent::getLine();
		$line[] = parent::getFile();
		if(is_array($this->message)){
			$message = print_r($this->message,true);
		}elseif(is_object($this->message)){
			$message = var_export($this->message,true);
		}
		$line[] = $message;
		return implode($this->splitStr,$line)."\r\n";
	}
	
	private function getFilename(){
		return $this->debugPath . date("Ymd").'_debug.log';
	}
	
	private function output(){
		
		if(!file_exists($this->debugPath)){
			mkdir($this->debugPath,0777,true);
		}
		
		$filename = $this->getFilename();
		
		if(is_file($filename) && filesize($filename) >= self::DEBUG_FILE_SIZE){
			$bak_filename = $this->debugPath . date("Ymd_His").'_debug.log';
			rename($filename, $bak_filename.'.bak');
			file_put_contents($filename,$this->getDebugData());
		}else{
			file_put_contents($filename,$this->getDebugData(),FILE_APPEND);
		}
	}
	
	private function show(){
		print_r($this->message);
	}

}
?>
