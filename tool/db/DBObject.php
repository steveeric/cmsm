<?php

class DBObject{
	//private $ip='192.168.53.74';
        private $ip = '127.0.0.1';
	private $port='3306';
	private $db_name='cms_mobile';
	private $user='shota';
	private $pass_word='cewk4193';

	public function getHost(){
		return $this -> ip;
	}

	public function getIPAddress(){
		return $this -> ip;
	}
	public function getDBName(){
		return $this -> db_name;
	}
	public function getUser(){
		return $this -> user;
	}
	public function getPass(){
		return $this -> pass_word;
	}
}

?>
