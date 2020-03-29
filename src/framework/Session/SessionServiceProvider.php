<?php

namespace framework\Session;

use framework\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
	
	public function register(){
		$this->app->bind("session",Session::class);
	}
	
	public function boot(){
		
	}
}
?>