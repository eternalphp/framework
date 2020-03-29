<?php

use framework\Container\Container;
use framework\Debug\Debug;
use framework\Session\SessionManager;

function app($abstract = null, array $parameters = []){
	if (is_null($abstract)) {
		return Container::getInstance();
	}
	return Container::getInstance()->make($abstract, $parameters);
}

function public_path($path){
	return app('Application')->publicPath($path);
}

function storage_path($path){
	return app('Application')->storagePath($path);
}

function resource_path($path){
	return app('Application')->resourcePath($path);
}

function route_path($path){
	return app('Application')->routePath($path);
}

function app_path($path){
	return app('Application')->appPath($path);
}

function config_path($path){
	return app('Application')->configPath($path);
}

function config($key = null,$default = null){
	if($key == null){
		return app('config');
	}
	
	if(is_array($key)){
		app('config')->set($key);
	}
	
	return app('config')->get($key, $default);
}

function session(){
	$args = func_get_args();
	$num = func_num_args();
	$session = app('session');
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
	$cookie = app('cookie');
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