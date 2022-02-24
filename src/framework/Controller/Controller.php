<?php

namespace framework\Controller;
use framework\Container\Container;
use stdClass;
use Exception;

class Controller
{
	
	protected $app;
	protected $view;
	protected $viewData;
	
	public function __construct(){
		$this->app = Container::getInstance();
		$this->view = $this->app['view'];
		$this->viewData = new stdClass();
	}
	
	/*
	 * 视图加载
	 * @param string $path
	 * @param array $data
	 * @return View
	 **/
	public function view(){
		$num = func_num_args();
		$args = func_get_args();
		$data = array();
		$namespace = $this->app["route"]->getNamespace();
		$directory = $this->app["route"]->getController();
		$directory = str_replace("Action",'',$directory);
		$path = $this->app["route"]->getAction();
		
		if($num == 1){
			if(is_array($args[0])){
				$data = $args[0];
			}else{
				$path = $args[0];
			}
		}
		
		if($num > 1){
			list($path,$data) = $args;
		}
		
		if($path != ''){
			$path = implode('/',explode('.',$path));
			if(strstr($path,'/') == false){
				$path = implode('/',array($namespace,ucfirst($directory),$path));
			}else{
				$path = implode('/',array($namespace,$path));
			}
		}
		
		$data['viewData'] = $this->viewData;
		
		$this->view->assign($data);
		$this->view->display($path);
	}
	
	/*
	 * 错误响应
	 * @param string | array $message
	 * @param int $code
	 * @return Response
	 **/
	function httpFail($message,$code = 40001){
		header('Content-Type:application/json; charset=utf-8');
		
		$data = array('errmsg'=> $message,'errcode'=> $code,'status'=> 'fail');
		$this->app['response']->json($data);
	}
	
	/*
	 * 成功响应
	 * @param string | array $message
	 * @param int $code
	 * @return Response
	 **/
	function httpSuccess($data,$message = 'ok',$code = 0){
		header('Content-Type:application/json; charset=utf-8');
		
		if(isset($data['data'])){
			$data = array_merge(array('errmsg'=> $message,'data'=> '','errcode'=> $code),$data);
		}else{
			$data = array('errmsg'=> $message,'data'=> $data,'errcode'=> $code);
		}
		
		$this->app['response']->json($data);
	}
	
	/*
	 * 错误响应
	 * @param Exception $ex
	 * @return Response
	 **/
	function httpException(Exception $ex){
		$this->httpFail($ex->getMessage(),$ex->getCode());
	}
}
?>