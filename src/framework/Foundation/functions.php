<?php

use framework\Container\Container;
use framework\Debug\Debug;

function app($abstract = null, array $parameters = []){
	if (is_null($abstract)) {
		return Container::getInstance();
	}
	return Container::getInstance()->make($abstract, $parameters);
}

function public_path($path){
	return app('Application')->publicPath($path);
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

function Debug($message){
	return new Debug($message);
}