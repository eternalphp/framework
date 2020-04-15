<?php

namespace framework\View;


abstract class Engine
{
	
	private $templatePath;
	private $cachePath;
	private $expire;
	private $tContent;
	private $tVal;
	private $tFile;
	private $cFile;
	private $sections;
	private $tExtName = '.html';
	private $realtime = false;
	
	public function __construct(){
		$this->expire = 30;
	}
	
	abstract function assign($name,$value = null);

	abstract function display($tFile);
	
	/**
	 * set variable to template
	 * @param string $name
	 * @param string $value
	 * @return void;
	 */
	protected function set($name,$value = null){
		if(is_array($name)){
			foreach($name as $key=>$val){
				$this->tVal[$key] = $val;
			}
		}else{
			$this->tVal[$name] = $value;
		}
	}
	
	/**
	 * load template file
	 * @param string $tFile
	 * @return void;
	 */
	protected function load($tFile){
		$this->getTemplateFile($tFile);
		$this->getCacheFile($tFile);
		if(!file_exists($this->cFile) || $this->expire() == true || $this->realtime){
			$this->parse();
		}
		if($this->tVal){
			extract($this->tVal, EXTR_OVERWRITE);
		}
		include($this->cFile);
	}
	
	/**
	 * get template filename
	 * @param string $tFile
	 * @return string;
	 */
	private function getTemplateFile($tFile){
		$tFile = str_replace(".",DIRECTORY_SEPARATOR,$tFile);
		$this->tFile = rtrim($this->templatePath,'/') .'/'. $tFile . $this->tExtName;
		return $this->tFile;
	}
	
	/**
	 * get cache filename
	 * @param string $tFile
	 * @return string;
	 */
	private function getCacheFile($tFile){
		$tFile = str_replace(".",DIRECTORY_SEPARATOR,$tFile);
		$this->cFile = rtrim($this->cachePath,'/') .'/'. $tFile . ".php";
		return $this->cFile;
	}
	
	/**
	 * set path for template
	 * @param string $path
	 * @return string;
	 */
	public function templatePath($path){
		$this->templatePath = $path;
		return $this;
	}
	
	/**
	 * set path for cache
	 * @param string $path
	 * @return string;
	 */
	public function cachePath($path){
		$this->cachePath = $path;
		return $this;
	}
	
	/**
	 * Verify cache expiration
	 * @return bool;
	 */
	private function expire(){
		if(file_exists($this->tFile)){
			if(time() - filemtime($this->tFile) > $this->expire * 60){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Set cache expiration
	 * @return $this;
	 */
	public function setExpire($minutes){
		$this->expire = $minutes;
		return $this;
	}
	
	/**
	 * Set realtime
	 * @return $this;
	 */
	public function realtime(){
		$this->realtime = true;
		return $this;
	}
	
	/**
	 * Compilation template
	 * @return void;
	 */
	private function parse(){
		$this->tContent = file_get_contents($this->tFile);
		$this->parseExtends();
		$this->parseInclude(); //子模板
		$this->parseSection(); // 循环/判断
		$this->parseVal(); //变量替换
		if(!file_exists(dirname($this->cFile))){
			mkdir(dirname($this->cFile),0777,true);
		}
		file_put_contents($this->cFile,$this->tContent);
	}
	
	/**
	 * Compilation include template
	 * @return void;
	 */
	private function parseInclude(){
		preg_match_all("/@include\([\'\"]+(.*?)[\'\"]+\)/",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				$file = $this->getTemplateFile($val);
				$content = file_get_contents($file);
				$this->tContent = str_replace($matchs[0][$k],$content,$this->tContent);
			}
		}
	}
	
	/**
	 * Compilation extends template
	 * @return void;
	 */
	private function parseExtends(){
		
		//继承模板时，先解析标签
		
		preg_match_all("/@section\([\'\"]+(.*?)[\'\"]+,+[\'\"]+(.*?)[\'\"]+\)/i",$this->tContent,$matchs);
		if($matchs[0]){

			foreach($matchs[1] as $k=>$val){
				$this->sections[$val] = array(
					'tag'=>$matchs[0][$k],
					'content'=>$matchs[2][$k]
				);
				$this->tContent = str_replace($matchs[0][$k],"",$this->tContent);
			}
		}
		
		preg_match_all("/@section\([\'\"]+(.*?)[\'\"]+\)(.*?)@endsection/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				$this->sections[$val] = array(
					'tag'=>$matchs[0][$k],
					'content'=>$matchs[2][$k]
				);
			}
		}
		
		preg_match("/@extends\([\'\"]+(.*?)[\'\"]+\)/",$this->tContent,$matchs);
		if(isset($matchs[0])){
			$file = $this->getTemplateFile($matchs[1]);
			$content = file_get_contents($file);
			$this->tContent = $content;
		}
	}
	
	/**
	 * Compile template logic
	 * @return void;
	 */
	private function parseSection(){
		
		preg_match_all("/@yield\([\'\"]+(.*?)[\'\"]+\)/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				if(isset($this->sections[$val])){
					$this->tContent = str_replace($matchs[0][$k],$this->sections[$val]["content"],$this->tContent);
				}
			}
		}
		
		preg_match_all("/@section\([\'\"]+(.*?)[\'\"]+\)(.*?)@show/is",$this->tContent,$matchs);
		if($matchs[0]){
			
			foreach($matchs[1] as $k=>$val){
				if(isset($this->sections[$val])){
					$content = str_replace("@parent",$matchs[2][$k],$this->sections[$val]["content"]);
					$this->tContent = str_replace($matchs[0][$k],$content,$this->tContent);
				}else{
					$this->tContent = str_replace($matchs[0][$k],"",$this->tContent);
				}
			}
		}
		
		preg_match_all("/@if\s?\((.*?)\)/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				$this->tContent = str_replace($matchs[0][$k],sprintf('<?php if(%s) {?>',$val),$this->tContent);
			}
		}
		
		preg_match_all("/@elseif\s?\((.*?)\)/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				$this->tContent = str_replace($matchs[0][$k],sprintf('<?php } elseif(%s) {?>',$val),$this->tContent);
			}
		}
		
		preg_match_all("/@else/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[0] as $val){
				$this->tContent = str_replace($val,'<?php } else {?>',$this->tContent);
			}
		}
		
		preg_match_all("/@endif/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[0] as $val){
				$this->tContent = str_replace($val,'<?php }?>',$this->tContent);
			}
		}
		
		preg_match_all("/@foreach\((.*?)\)/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				$this->tContent = str_replace($matchs[0][$k],sprintf('<?php foreach(%s) {?>',$val),$this->tContent);
			}
		}
		
		preg_match_all("/@endforeach/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs as $val){
				$this->tContent = str_replace($val[0],'<?php }?>',$this->tContent);
			}
		}
		
	}
	
	/**
	 * Compile template variable
	 * @return void;
	 */
	private function parseVal(){
		// 变量表达式:		
		preg_match_all("/\{\{\s?(.*?)\s?\}\}/is",$this->tContent,$matchs);
		if($matchs[0]){
			foreach($matchs[1] as $k=>$val){
				$this->tContent = str_replace($matchs[0][$k],sprintf('<?=%s?>',$val),$this->tContent);
			}
		}
	}
}
?>