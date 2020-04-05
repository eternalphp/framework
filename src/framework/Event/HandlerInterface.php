<?php

namespace framework\Event;

Interface HandlerInterface {
	
	/**
	 * 事件处理
	 * @param array $data
	 * @return void
	 */
	function handle($data = array());
}
?>