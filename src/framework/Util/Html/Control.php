<?php

namespace framework\Util\Html;


class Control
{
	
	private $layout;
	private $filename;
	private $sections = [];
	private $yields = array();
	
	public function __construct($layout = '',$filename = ''){
		$this->layout = $layout;
		$this->filename = $filename;
		$this->tContent = file_get_contents($this->layout);
		
		preg_match_all('/@extends\([\'\"]+(.*?)[\'\"]+\)/i',$this->tContent,$matchs);
		if($matchs){
			foreach($matchs[1] as $k=>$filename){
				if(file_exists($filename)){
					$matchs[1][$k] = file_get_contents($filename);
				}
			}
			$replace = array_combine($matchs[0],$matchs[1]);
			$this->tContent = strtr($this->tContent,$replace);
		}
		
		preg_match_all('/@yield\([\'\"]+(.*?)[\'\"]+\)/i',$this->tContent,$matchs);
		$this->yields = array_combine($matchs[1],$matchs[0]);
		
		preg_match_all('/@section\([\'\"]+(.*?)[\'\"]+\)(.*?)@endsection/is',$this->tContent,$matchs);
		if($matchs){
			foreach($matchs[1] as $k=>$val){
				$this->sections[$val] = array(
					'html'=>$matchs[0][$k],
					'content'=>$matchs[2][$k]
				);
			}
		}
		
		preg_match_all('/@section\([\'\"]+(.*?)[\'\"]+,+[\'\"]+(.*?)[\'\"]+\)/i',$this->tContent,$matchs);
		if($matchs){
			foreach($matchs[1] as $k=>$val){
				$this->sections[$val] = array(
					'html'=>$matchs[0][$k],
					'text'=>$matchs[2][$k]
				);
			}
		}
		
		print_r($this->sections);
		print_r($this->yields);
		
		if($this->yields){
			foreach($this->yields as $key=>$val){
				
			}
		}
		
	}
	
	public function create($name,callable $callback){
		$table = new Table();
		call_user_func($callback,$table);
		$content = $table->create();
		$this->tContent = strtr($this->tContent,array($this->yields[$name]=>$content));
		file_put_contents($this->filename,$this->tContent);
	}
}
?>