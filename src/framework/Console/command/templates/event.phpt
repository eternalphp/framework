<?php

namespace App\Event;

use framework\Event\Event;
use App\Event\{%event_name%}Handler;

class {%event_name%}Event extends Event{

	protected $name = '{%event_name%}';

	public $model;

	public function __construct($model){

		$this->model = $model;

		$this->bind('{%event_name%}',{%event_name%}Handler::class);
		
	}
}