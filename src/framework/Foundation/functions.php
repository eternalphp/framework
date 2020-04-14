<?php

use framework\Container\Container;
use framework\Foundation\Application;
use framework\Debug\Debug;
use framework\Session\SessionManager;

function app($abstract = null, array $parameters = []){
	if (is_null($abstract)) {
		return Container::getInstance();
	}
	return Container::getInstance()->make($abstract, $parameters);
}

function application(){
	return Application::getInstance();
}

function public_path($path){
	return application()->publicPath($path);
}

function storage_path($path){
	return application()->storagePath($path);
}

function resource_path($path){
	return application()->resourcePath($path);
}

function language_path($path = 'zh'){
	return application()->languagePath($path);
}

function route_path($path){
	return application()->routePath($path);
}

function app_path($path){
	return application()->appPath($path);
}

function config_path($path){
	return application()->configPath($path);
}

function config($key = null,$default = null){
	if($key == null){
		return app()->get("config");
	}
	
	if(is_array($key)){
		config()->set($key);
	}
	
	return config()->get($key, $default);
}

function session(){
	$args = func_get_args();
	$num = func_num_args();
	$session = app()->get("session");
	if($num == 1){
		$key = $args[0];
		return $session->get($key);
	}elseif($num == 2){
		$key = $args[0];
		$data = $args[1];
		if($data != null){
			$session->put($key,$data);
		}else{
			$session->remove($key);
		}
	}else{
		return $session;
	}
}

function cookie(){
	$args = func_get_args();
	$num = func_num_args();
	$cookie = app()->get("cookie");
	if($num == 1){
		$key = $args[0];
		return $cookie->get($key);
	}elseif($num == 2){
		$key = $args[0];
		$data = $args[1];
		if($data != null){
			$cookie->save($key,$data);
		}else{
			$cookie->destroy($key);
		}
	}else{
		return $cookie;
	}
}

function cache(){
	$args = func_get_args();
	$num = func_num_args();
	$cache = app()->get("cache");
	if($num == 1){
		$key = $args[0];
		return $cache->get($key);
	}elseif($num == 2){
		$key = $args[0];
		$data = $args[1];
		if($data != null){
			$cache->set($key,$data);
		}else{
			$cache->remove($key);
		}
	}else{
		return $cache;
	}
}

function language($key = null,$default = null){
	if($key == null){
		return app()->get("language");
	}
	
	if(is_array($key)){
		language()->set($key);
	}
	return language()->get($key, $default);
}

function L($key = null,$default = null){
	
	$text = language($key);
	if(is_null($text)){
		return is_null($default) ? $key : $default;
	}else{
		$args = func_get_args();
		if(count($args)>1){
			$args[0] = $text;
			foreach($args as $k=>$val){
				if(is_array($val)){
					$args[$k] = count($val);
				}
			}
			return call_user_func_array('sprintf',$args);
		}
		return $text;
	}
	
}

function response($content = '', $status = 200, $headers = []){
	return app('response',array('content'=>$content,'status'=>$status,'headers'=>$headers));
}

// success({'errcode':0,'errmsg':'ok'},'parent.callback');
function success($res = array(),$callback = 'json'){
	
	if(is_string($res) && $res != ''){
		$errmsg = $res;
		$res = array();
		$res['errmsg'] = $errmsg;
		$callback = 'parent.callback';
	}
	
	if(!isset($res['errcode'])){
		$res['errcode'] = 0;
	}
	if(!isset($res['errmsg'])){
		$res['errmsg'] = 'success';
	}
	$res = json_encode($res);
	
	if($callback == 'json'){
		return $res;
	}else{
		echo "<script>$callback(".$res.");</script>";
	}
}

function fail($res = array(),$callback = 'json'){
	
	if(is_string($res) && $res != ''){
		$errmsg = $res;
		$res = array();
		$res['errmsg'] = $errmsg;
		$callback = 'parent.callback';
	}
	
	if(!isset($res['errcode'])){
		$res['errcode'] = 200;
	}
	if(!isset($res['errmsg'])){
		$res['errmsg'] = 'fail';
	}
	$res = json_encode($res);
	
	if($callback == 'json'){
		return $res;
	}else{
		echo "<script>$callback(".$res.");</script>";
	}
}

function request($name,$value = false){
	if(isset($_POST[$name])){
		return $_POST[$name];
	}elseif(isset($_GET[$name])){
		return $_GET[$name];
	}else{
		return $value;
	}
}

function requestInt($name,$value = false){
	if(isset($_POST[$name])){
		return intval($_POST[$name]);
	}elseif(isset($_GET[$name])){
		return intval($_GET[$name]);
	}else{
		return $value;
	}
}