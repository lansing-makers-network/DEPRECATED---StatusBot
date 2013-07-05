<?php

class DbAdapter{
	protected $connection;
	
	public function __construct(){
		$this->connection = new mysqli(DbConstants::$dbHost, DbConstants::$dbUser, DbConstants::$dbPassword, DbConstants::$dbName);
	}
}
?>