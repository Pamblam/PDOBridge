<?php

define("PDBC_PATH", realpath(dirname(__FILE__)));
require PDBC_PATH."/PDObcStatement.php";

class PDObc{
	
	private $dsn;
	private $user;
	private $pass;
	private $pdob_host;
	private $pdob_key;
	
	public function __construct($dsn, $user, $pass, $pdob_host, $pdob_key){
		$this->dsn = $dsn; 
		$this->user = $user; 
		$this->pass = $pass; 
		$this->pdob_host = $pdob_host;
		$this->pdob_key = $pdob_key;
	}
	
	public function query($stmt){
		$stmt = new PDObcStatement($this, $stmt);
		$stmt->execute();
		return $stmt;
	}
	
	public function prepare($stmt){
		return new PDObcStatement($this, $stmt);
	}
	
	public function __get($name){
		return $this->$name;
	}
}