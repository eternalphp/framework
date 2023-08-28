<?php

use framework\Container\Container;
use framework\Foundation\Application;
use framework\Debug\Debug;
use framework\Session\SessionManager;
use framework\Util\Html\HtmlControl;
use framework\Event\Dispatcher;

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

function database_path($path){
	return application()->databasePath($path);
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

function route(){
	return app()->get('route');
}

function getController(){
	$controller = app()->get('route')->getController();
	return str_replace(['Action','Controller'],['',''],$controller);
}

function getAction(){
	return app()->get('route')->getAction();
}

function getParams(){
	
	$params = app()->get('route')->getParams();
	if($_GET){
		foreach($_GET as $key=>$val){
			$params[$key] = filter_input(INPUT_GET, $key, FILTER_DEFAULT);
		}
	}
	
	return $params;
}

function getPrefix(){
	return app()->get('route')->getPrefix();
}

function get($name){
	if($name == 'class'){
		return getController();
	}elseif($name == 'method'){
		return getAction();
	}else{
		return getParams();
	}
}

function event($event = null){
	$dispatcher = Dispatcher::getInstance();
	if($event != null){
		$dispatcher->fire($event);
	}else{
		return $dispatcher;
	}
}

function abort($code = 404){
	$view = app('view');
	$view->templatePath(dirname(__DIR__) . '/Exception/');
	$view->cachePath(dirname(__DIR__) . '/Exception/' . "/cache/");
	$view->realtime();
	$view->assign("title","404");
	$view->assign("message","Not Found");
	$view->display("views/404");
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
		if(count($args) > 1){
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

function HtmlControl(){
	return new HtmlControl();
}

function menulink($data = array()){
	$links  = array();
	foreach($data as $k=>$val){
		
		$text = isset($val['icon']) ? sprintf('<i class="%s"></i>',$val['icon']) : $val['text'];
		$val['type'] = isset($val['type']) ? $val['type'] : 'js';
		
		if(isset($val['type']) && $val['type'] == 'url'){
			$links[] = HtmlControl()
			->Link($text,$val['url'])
			->class($val['name'])
			->target($val['target'])
			->title($val['text'])
			->create();
		}else{
			$links[] = HtmlControl()
			->Link($text,'javascript:void(0)')
			->class($val['name'])
			->title($val['text'])
			->attr('url',$val['url'])
			->create();
		}
	}
	return implode('<span class="split"> | </span>',$links);
}

function response($content = '', $status = 200, $headers = []){
	return app('response',array('content'=>$content,'status'=>$status,'headers'=>$headers));
}

function csrf_token(){
	$token = session()->token();
	session('token',$token);
	return $token;
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

/**
 * @param $params
 * @param int $type
 * @return array
 */
function filter($params, $type = INPUT_GET)
{
	$ret = [];
	foreach ($params as $key => $param) {
		$ret[$key] = filter_input($type, $key, FILTER_DEFAULT);
	}
	return $ret;
}

function url($path,$params = array()){
	$path = '/'.trim($path,'/');
	if($params){
		$path = $path . "?" . urldecode(http_build_query($params));
	}
	return $path;
}

function autolink($data = array()){
	$prefix = app()->get('route')->getPrefix();
	$paths = array();
	if($prefix != 'index'){
		$paths[] = $prefix;
	}
	$params = array();
	if($data){
		foreach($data as $key=>$val){
			if(is_int($key)){
				if($val != 'index'){
					$paths[] = $val;
				}
			}else{
				$params[$key] = $val;
			}
		}
	}
	
	$path = implode('/',$paths);
	return url($path,$params);
}

/*
功能：用来过滤字符串和字符串数组，防止被挂马和sql注入
参数$data，待过滤的字符串或字符串数组，
$force为true，忽略get_magic_quotes_gpc
*/
function sql_in($data,$force = false){
	if(is_string($data)){
		
		$data = trim(htmlspecialchars($data,ENT_QUOTES));
		$data = trim($data);
		if(($force == true) || (!get_magic_quotes_gpc())){
		   $data = addslashes($data);
		}
		return  $data;
		
	}else if(is_array($data)){
		
		foreach($data as $key=>$value){
			 $data[$key] = sql_in($value,$force);
		}
		return $data;
		
	}else{
		return $data;
	}	
}

//用来还原字符串和字符串数组，把已经转义的字符还原回来
function sql_out($data){
	if(is_string($data)){
		
		return $data = stripslashes(htmlspecialchars_decode($data,ENT_QUOTES));
		
	}else if(is_array($data)){
		
		foreach($data as $key=>$value){
			$data[$key] = sql_out($value);
		}
		return $data;
		
	}else{
		
		return $data;
		
	}	
}

//html代码输入
function html_in($str){
	$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
					 "'<iframe[^>]*?>.*?</iframe>'si", // 去掉iframe
					);
	$replace = array ("",
					  "",
					);			  
	$str = @preg_replace ($search, $replace, $str);
	$str = htmlspecialchars($str);
   	if(!get_magic_quotes_gpc()) 
	{
		$str = addslashes($str);
	}
	return $str;
}

//html代码输出
function html_out($str){
	if(function_exists('htmlspecialchars_decode'))
		$str = htmlspecialchars_decode($str);
	else
		$str = html_entity_decode($str);

    $str = stripslashes($str);
	return $str;
}

function remainTime($date){
	$time = strtotime($date) - time();
	if($time > 0){
		$day    = floor($time / 60 / 60 / 24);
		$hour   = floor($time / 60 / 60);
		$minute = floor($time / 60);
		if($day > 0){
			$time	= $time%86400;
			return $day.'天'.gmstrftime("%Hh%Mm%Ss", $time);
		}elseif($hour > 0){
			return gmstrftime("%Hh%Mm%Ss",$time);
		}elseif($minute > 0){
			return gmstrftime("%Mm%Ss",$time);
		}else{
			return gmstrftime("%Ss",$time);
		}
	}else{
		return false;
	}
}

/**
 * 时间差计算
 *
 * @param Timestamp $time 时间差
 * @return String Time Elapsed
 */
function time2Units($date){
	$time   = time() - strtotime($date);
	$year   = floor($time / 60 / 60 / 24 / 365);
	$time  -= $year * 60 * 60 * 24 * 365;
	$month  = floor($time / 60 / 60 / 24 / 30);
	$time  -= $month * 60 * 60 * 24 * 30;
	$week   = floor($time / 60 / 60 / 24 / 7);
	$time  -= $week * 60 * 60 * 24 * 7;
	$day    = floor($time / 60 / 60 / 24);
	$time  -= $day * 60 * 60 * 24;
	$hour   = floor($time / 60 / 60);
	$time  -= $hour * 60 * 60;
	$minute = floor($time / 60);
	$time  -= $minute * 60;
	$second = $time;
	$elapse = '';
	 
	$unitArr = array('年前'=>'year','个月前'=>'month','周前'=>'week','天前'=>'day','小时前'=>'hour','分钟前'=>'minute','秒前'=>'second');

	foreach ($unitArr as $cn => $u){
		if ($year > 0) {//大于一年显示年月日
			$elapse = date('Y/m/d',time()-$time);
			break;
		}else if ($$u > 0){
			$elapse = $$u . $cn;
			break;
		}
	}
 
	return $elapse;
}

/**
  CURL 请求
*/
function https_request($url,$data = null,$options = array()){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

	
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	if(isset($options['timeout']) && $options['timeout']>0) curl_setopt($curl, CURLOPT_TIMEOUT,$options['timeout']);
	
	if(isset($options["return_header"])){
		curl_setopt($curl, CURLOPT_HEADER, $options["return_header"]);
	}
	
	if(isset($options['header']) && is_array($options['header']) && $options['header']){
		curl_setopt($curl, CURLOPT_HTTPHEADER,$options['header']);
	}elseif(isset($options['cookie']) && !empty($options['cookie'])){
		curl_setopt($curl, CURLOPT_COOKIE, $options['cookie']);
	}
	
	//保存cookie文件路径
	if(isset($options['saveCookieFile']) && $options['saveCookieFile']!=''){
		curl_setopt($curl, CURLOPT_COOKIEJAR, $options['saveCookieFile']); 
	}
	
	//读取cookie文件路径
	if(isset($options['readCookieFile']) && $options['readCookieFile']!=''){
		curl_setopt($curl, CURLOPT_COOKIEFILE, $options['readCookieFile']); 
	}

	curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1); 
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}