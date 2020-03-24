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
	
	public function __construct(){
		$this->expire = 30;
	}
	
	abstract function assign($name,$value = null);

	abstract function display($tFile);
	
	//变量赋值
	protected function set($name,$value = null){
		if(is_array($name)){
			foreach($name as $key=>$val){
				$this->tVal[$key] = $val;
			}
		}else{
			$this->tVal[$name] = $value;
		}
	}
	
	//运行并显示模版内容
	protected function load($tFile){
		$this->getTemplateFile($tFile);
		$this->getCacheFile($tFile);
		if(!file_exists($this->cFile) || $this->expire() == true){
			$this->parse();
		}
		extract($this->tVal, EXTR_OVERWRITE);
		include($this->cFile);
	}
	
	private function getTemplateFile($tFile){
		$tFile = str_replace(".",DIRECTORY_SEPARATOR,$tFile);
		$this->tFile = rtrim($this->templatePath,'/') .'/'. $tFile . $this->tExtName;
		return $this->tFile;
	}
	
	private function getCacheFile($tFile){
		$tFile = str_replace(".",DIRECTORY_SEPARATOR,$tFile);
		$this->cFile = rtrim($this->cachePath,'/') .'/'. $tFile . ".php";
		return $this->cFile;
	}
	
	protected function templatePath($path){
		$this->templatePath = $path;
		return $this;
	}
	
	protected function cachePath($path){
		$this->cachePath = $path;
		return $this;
	}
	
	private function expire(){
		if(file_exists($this->tFile)){
			if(time() - filemtime($this->tFile) > $this->expire * 60){
				return true;
			}
		}
		return false;
	}
	
	private function parse(){
		$this->tContent = file_get_contents($this->tFile);
		$this->parseExtends();
		$this->parseInclude(); //子模板
		$this->parseSection(); // 循环/判断
		$this->parseVal(); //变量替换
		$this->parseEval(); //解析PHP语句
		file_put_contents($this->cFile,$this->tContent);
	}
	
	private function parseInclude(){
		preg_match_all("/@include\("(.*?)"\)/",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				$file = $this->getTemplateFile($val[1]);
				$content = file_get_contents($file);
				$this->tContent = str_replace($val[0],$content,$this->tContent);
			}
		}
	}
	
	private function parseExtends(){
		preg_match("/@extends\("(.*?)"\)/",$this->tContent,$matchs);
		if($matchs){
			
			//继承模板时，先解析标签
			preg_match_all("/@yield\([\'\"]+(.*?)[\'\"]+\)/is",$this->tContent,$matchs);
			if($mathcs){
				foreach($matchs as $val){
					$this->sections[$val[1]] = array(
						'tag'=>$val[0];
					);
				}
			}
			
			preg_match_all("/@section\([\'\"]+(.*?)[\'\"]+\)(.*?)@show/is",$this->tContent,$matchs);
			if($mathcs){
				foreach($matchs as $val){
					$this->sections[$val[1]] = array(
						'tag'=>$val[1],
						'content'=>$val[2];
					);
				}
			}
			
			$file = $this->getTemplateFile($matchs[1]);
			$content = file_get_contents($file);
			$this->tContent = str_replace($matchs[0],$content,$this->tContent);
		}
	}
	
	private function parseSection(){
		preg_match_all("/@if\s?\((.*?)\)/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				$this->tContent = str_replace($val[0],sprintf('<?php if(%s) {?>',$val[1]),$this->tContent);
			}
		}
		
		preg_match_all("/@elseif\s?\((.*?)\)/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
			$this->tContent = str_replace($val[0],sprintf('<?php } elseif(%s) {?>',$val[1]),$this->tContent);
			}
		}
		
		preg_match_all("/@else/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
			$this->tContent = str_replace($val[0],'<?php } else {?>',$this->tContent);
			}
		}
		
		preg_match_all("/@endif/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				$this->tContent = str_replace($val[0],'<?php }?>',$this->tContent);
			}
		}
		
		preg_match_all("/@foreach\((.*?)\)/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				$this->tContent = str_replace($val[0],sprintf('<?php foreach(%s) {?>',$val[1]),$this->tContent);
			}
		}
		
		preg_match_all("/@endforeach/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				$this->tContent = str_replace($val[0],'<?php }?>',$this->tContent);
			}
		}
		
		preg_match_all("/@section\([\'\"]+(.*?)[\'\"]+,+[\'\"]+(.*?)[\'\"]+\)/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				if(isset($this->sections[$val[1]])){
					$this->tContent = str_replace($this->sections[$val[1]]["tag"],$val[2],$this->tContent);
				}
			}
		}
		
		preg_match_all("/@section\([\'\"]+(.*?)[\'\"]+\)(.*?)@endsection/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				if(isset($this->sections[$val[1]])){
					$val[2] = str_replace("@parent",$this->sections[$val[1]]["content"],$val[2]);
					$this->tContent = str_replace($this->sections[$val[1]]["tag"],$val[2],$this->tContent);
				}
			}
		}
	}
	
	private function parseVal(){
		// 变量表达式:		
		preg_match_all("/\{\{/s?(.*?)/s?\}\}/is",$this->tContent,$matchs);
		if($mathcs){
			foreach($matchs as $val){
				$this->tContent = str_replace($val[0],sprintf('<?php echo %s;?>',$val[1]),$this->tContent);
			}
		}
	}
	
	private function parseEval(){
		
	}
}
?>