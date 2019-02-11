<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function avatar($email = '', $size=50)
{
	global $sys;
	return $sys->avatar($email, $size);
}