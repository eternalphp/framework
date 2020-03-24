<?php

namespace framework\Database\Connection;


interface ConnectorInterface
{
	public function connect();
	public function close();
	public function query($sql);
	public function execute($sql);
	public function startTrans();
	public function commit();
	public function rollback();
	public function transEnd();
	public function charset($charset);
}

?>