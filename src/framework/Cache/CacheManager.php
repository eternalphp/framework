<?php

namespace framework\Cache;

use framework\Foundation\Manager;

class CacheManager extends Manager
{
	
	public function getFileDriver(){
		return new CacheFile($this->app->storagePath($this->app->config("cache.DATA_CACHE_PATH")));
	}
	
	public function getDriver(){
		return $this->getFileDriver();
	}
}
?>