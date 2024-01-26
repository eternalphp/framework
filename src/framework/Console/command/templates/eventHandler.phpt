<?php

namespace App\Event;

use framework\Event\HandlerInterface;
use framework\Database\Eloquent\DB;

class {%event_name%}Handler implements HandlerInterface{

	public function handle($event){
		print_r($event->model);
		//业务逻辑
	}
}