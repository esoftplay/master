<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/**
 * overide file function for third party
 * please add in /config.php
 * define('_IMAGE_CLASS', 'redis');
 */

if (defined('_IMAGE_CLASS'))
{
	require_once __DIR__.'/'._IMAGE_CLASS.'.php';
}