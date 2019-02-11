<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class writecookie{

	var $name;
	var $value;
	var $int_expire;

	function __construct( $name, $value, $int_expire ){
		$this->name 		= $name;
		$this->value 		= $value;
		if (is_string($int_expire))
		{
			$this->int_expire = strtotime($int_expire);
		}else{
			$time = time();
			if ($time < $int_expire)
			{
				$this->int_expire	= $int_expire+$time;
			}else{
				$this->int_expire	= $int_expire;
			}
		}
	}

	function check(){
		$name	= $this->name;
		if ( !isset($_COOKIE[$name]) ) {
			return true;
		}else{
			return false;
		}
	}

	function set(){
		// ini untuk ngeset cookie yg ndukung all browser
		if (preg_match("/MSIE/", getenv("HTTP_USER_AGENT"))) {
			$time = mktime() + $this->int_expire;
			$date = date("l, d-M-y H:i:s", $this->int_expire);
		}	else {
			$date = time() + $this->int_expire;
		}
		$host = '.'.preg_replace('~^www\.~', '', getenv('HTTP_HOST'));
		setcookie( $this->name, $this->value, $this->int_expire, '', $host );
		$this->name;
	}
}