<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class Encrypt {

	var $encryption_key	= '*#@jh$%[*H@nb]+)@Dhl;,E';
	var $_hash_type	= 'sha1';
	var $_mcrypt_exists = FALSE;
	var $_mcrypt_cipher;
	var $_mcrypt_mode;

	function __construct()
	{
		$this->_mcrypt_exists = ( ! function_exists('mcrypt_encrypt')) ? FALSE : TRUE;
	}

	function decode($string, $key = '')
	{
		$key = $this->get_key($key);
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
		{
			return FALSE;
		}
		$dec = base64_decode($string);
		$ssl = false;

		if ($this->_mcrypt_exists === TRUE)
		{

			if (($dec = $this->mcrypt_decode($dec, $key)) === FALSE)
			{
				$ssl = true;
			}
			if (preg_match('/[^\x00-\x7F]/', $dec))
			{
				$ssl = true;
				$dec = base64_decode($string);
			}
		}else{
			$ssl = true;
		}
		if ($ssl)
		{
			$ivlen          = openssl_cipher_iv_length($cipher='AES-128-CBC');
			$iv             = substr($dec, 0, $ivlen);
			$hmac           = substr($dec, $ivlen, $sha2len=32);
			$ciphertext_raw = substr($dec, $ivlen+$sha2len);
			$dec            = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
			if (function_exists('hash_equals'))
			{
				$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
				if (!hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
				{
					$dec = '';
				}
			}

		}
		return $this->_xor_decode($dec, $key);
	}

	function encode($string, $key = '')
	{
		$key = $this->get_key($key);
		$enc = $this->_xor_encode($string, $key);
		if ($this->_mcrypt_exists === TRUE)
		{
			$enc = $this->mcrypt_encode($enc, $key);
		}else{
			$ivlen          = openssl_cipher_iv_length($cipher='AES-128-CBC');
			$iv             = openssl_random_pseudo_bytes($ivlen);
			$ciphertext_raw = openssl_encrypt($enc, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
			$hmac           = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
			$enc            = $iv.$hmac.$ciphertext_raw;
		}
		return base64_encode($enc);
	}

	function get_key($key = '')
	{
		if ($key == '')
		{
			if(defined('_SALT')) {
				$key = _SALT;
			}else{
				$key = $this->encryption_key;
			}
		}
		return md5($key);
	}

	function set_key($key = '')
	{
		$this->encryption_key = $key;
	}

	function _xor_encode($string, $key)
	{
		$rand = '';
		while (strlen($rand) < 32)
		{
			$rand .= mt_rand(0, mt_getrandmax());
		}
		$rand = $this->hash($rand);
		$enc = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
		}
		return $this->_xor_merge($enc, $key);
	}

	function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);
		$dec = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));
		}
		return $dec;
	}

	function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++)
		{
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
		}
		return $str;
	}

	function mcrypt_encode($data, $key)
	{
		$this->_show_error_message(false);
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		$output    = $this->_add_cipher_noise($init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), $key);
		$this->_show_error_message(true);
		return $output;
	}

	function mcrypt_decode($data, $key)
	{
		$this->_show_error_message(false);
		$data = $this->_remove_cipher_noise($data, $key);
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		if ($init_size > strlen($data))
		{
			$this->_show_error_message(true);
			return FALSE;
		}
		$init_vect = substr($data, 0, $init_size);
		$data = substr($data, $init_size);
		$output = rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
		$this->_show_error_message(true);
		return $output;
	}

	function _show_error_message($show)
	{
		if ($show)
		{
			if (!empty($this->_show_error_message_flag))
			{
				ini_set('display_errors', 1);
			}
		}else{
			$display_errors = ini_get('display_errors') ? 1 : 0;
			if ($display_errors)
			{
				ini_set('display_errors', 0);
				$this->_show_error_message_flag = 1;
			}
		}
	}

	function _add_cipher_noise($data, $key)
	{
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';
		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j)
		{
			if ($j >= $keylen)
			{
				$j = 0;
			}
			$str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
		}
		return $str;
	}

	function _remove_cipher_noise($data, $key)
	{
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';
		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j)
		{
			if ($j >= $keylen)
			{
				$j = 0;
			}
			$temp = ord($data[$i]) - ord($keyhash[$j]);
			if ($temp < 0)
			{
				$temp = $temp + 256;
			}
			$str .= chr($temp);
		}
		return $str;
	}

	function set_cipher($cipher)
	{
		$this->_mcrypt_cipher = $cipher;
	}

	function set_mode($mode)
	{
		$this->_mcrypt_mode = $mode;
	}

	function _get_cipher()
	{
		if ($this->_mcrypt_cipher == '')
		{
			$this->_mcrypt_cipher = MCRYPT_RIJNDAEL_256;
		}

		return $this->_mcrypt_cipher;
	}

	function _get_mode()
	{
		if ($this->_mcrypt_mode == '')
		{
			$this->_mcrypt_mode = MCRYPT_MODE_ECB;
		}

		return $this->_mcrypt_mode;
	}

	function set_hash($type = 'sha1')
	{
		$this->_hash_type = ($type != 'sha1' AND $type != 'md5') ? 'sha1' : $type;
	}

	function hash($str)
	{
		return ($this->_hash_type == 'sha1') ? $this->sha1($str) : md5($str);
	}

	function sha1($str)
	{
		if ( ! function_exists('sha1'))
		{
			if ( ! function_exists('mhash'))
			{
				$SH = _class('sha');
				return $SH->generate($str);
			}
			else
			{
				return bin2hex(mhash(MHASH_SHA1, $str));
			}
		}
		else
		{
			return sha1($str);
		}
	}
}
function encode($text, $key='')
{
	global $Bbc;
	$Bbc->encrypt = isset($Bbc->encrypt) ? $Bbc->encrypt : new Encrypt();
	return $Bbc->encrypt->encode($text, $key);
}
function decode($text, $key='')
{
	global $Bbc;
	$Bbc->encrypt = isset($Bbc->encrypt) ? $Bbc->encrypt : new Encrypt();
	return $Bbc->encrypt->decode($text, $key);
}
