<?php

namespace framework\Cache;

use framework\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
	
	public function register(){
		$this->app->bind("cache",Cache::class);
	}
	
	public function boot(){
		
	}
}
?>