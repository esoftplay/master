<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class crypt
{
	private $use_sha = false;
	private $method = 'AES-256-CBC';
	function __construct() {}
	public function encode($text)
	{
		date_default_timezone_set('UTC');
		$text   = $this->sha5(substr(date('c'),0,19) . "$text", true, $this->use_sha);
		$iv     = substr(bin2hex(openssl_random_pseudo_bytes(16)),0,16);
		$output = base64_encode(base64_encode($iv) . openssl_encrypt($text, $this->method, _SALT, 0, $iv));
		return $output;
	}
	public function decode($text)
	{
		$output = '';
		$text   = base64_decode($text);
		if (strlen($text) > 24)
		{
			$iv        = base64_decode(substr($text, 0, 24));
			$decrypted = openssl_decrypt(substr($text, 24), $this->method, _SALT, 0, $iv);
			$text_raw  = $this->sha5($decrypted, false, $this->use_sha);
			$output    = substr($text_raw, 19);
		}
		return $output;
	}
	public function sha5($string, $toogle, $use_sha = true)
	{
		if ($use_sha)
		{
			$o = '';
			$r = str_split($string);
			if ($toogle)
			{
				foreach ($r as $i => $a)
				{
					$j = rand(97, 122);
					if (rand(0,1)) {
						$j -= 32;
					}
					$x = ord($a)+$j;
					if ($x > 256)
					{
						$x -= 256;
					}
					$o .= chr($j).chr($x);
				}
			}else{
				$j = 0;
				$x = 0;
				foreach ($r as $i => $a)
				{
					if($i%2)
					{
						$x = ord($a)-$j;
						if ($x < 0)
						{
							$x += 256;
						}
						$o .= chr($x);
					}else{
						$j = ord($a);
					}
				}
			}
		}else{
			$o = $string;
		}
		return $o;
	}
}