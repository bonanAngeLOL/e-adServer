<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class memcached {
	private $memcache;
	public function __construct(){
		$this->memcache = new Memcache;
		$this->memcache->connect("127.0.0.1",11211);
	}
	public function __destruct(){
		$this->memcache->close();
	}
	public function mem(){
		return $this->memcache;
	}
}