<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// in /config.php
// define('_LOCK_REDIS', '10.139.26.43:6379');

require_once __DIR__.'/lib/index.php';

use shared\cache\Bcache;

function lock_start($key, $errFunc = '', $timeout = 15)
{
	global $Bbc;
	if (!defined('_LOCK_REDIS'))
	{
		die('const _LOCK_REDIS must be defined in your config');
	}
	if (empty($Bbc->lock))
	{
		$lock_redis  = explode(':', _LOCK_REDIS);
		$lock_host   = $lock_redis[0];
		$lock_port   = !empty($lock_redis[1]) ? $lock_redis[1] : 6379;
		$domain      = preg_replace('~[^a-z0-9_]+~is', '_', @$_SERVER['HTTP_HOST']);
		$cacheEngine = new BCache($lock_host, $lock_port, 'redis', $domain.'-', $timeout);
		$Bbc->lock   = $cacheEngine;
	}
	$Bbc->lockKey = $key;
	$response = $Bbc->lock->set($key, 'locked');
	if (!$response)
	{
		// echo 'mengisi<br />';
		if (!empty($errFunc)) {
			if (is_callable($errFunc))
			{
				return call_user_func($errFunc, $response);
			}
		}
		return false;
	}
	return true;
}

function lock_end($key='')
{
	global $Bbc;
	if (!empty($Bbc->lock))
	{
		if (empty($key) && !empty($Bbc->lockKey))
		{
			$key = $Bbc->lockKey;
		}
		if (!empty($key))
		{
			$Bbc->lock->delete($key);
		}
	}
}