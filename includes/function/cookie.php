<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function cookie_set($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '')
{
	if (is_array($name))
	{
		foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'name') as $item)
		{
			if (isset($name[$item]))
			{
				$$item = $name[$item];
			}
		}
	}
	cookie_prefix($prefix);
	if($path != '/')
	{
		$path = _URI.$path;
	}

	if ( ! is_numeric($expire))
	{
		$expire = time() - 86500;
	}
	else
	{
		if ($expire > 0)
		{
			$expire = time() + $expire;
		}
		else
		{
			$expire = 0;
		}
	}
	setcookie($prefix.$name, $value, $expire, $path, $domain, 0);
}

function cookie_fetch($index = '', $prefix='')
{
	cookie_prefix($prefix);
	$output = '';
	if(isset($_COOKIE[$prefix.$index]))
	{
		$output = $_COOKIE[$prefix.$index];
	}else
	if(isset($_COOKIE[$index]))
	{
		$output = $_COOKIE[$index];
	}
	return $output;
}

function cookie_delete($name = '', $domain = '', $path = '/', $prefix = '')
{
	cookie_set($name, '', '', $domain, $path, $prefix);
}

function cookie_prefix(&$prefix)
{
	if($prefix == '')
	{
		$prefix = menu_save(_URL);
	}
}
