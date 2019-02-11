<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'forget'=> array(
		'text'   => 'Forget Password link',
		'tips'   => 'Show / Hide forget password link ?',
		'type'   => 'radio',
		'option' => array(1 => 'Show', 0 => 'Hide')
		),
	'register'	=> array(
		'text'   => 'Register link',
		'tips'   => 'Show / Hide register link ?',
		'type'   => 'radio',
		'option' => array(1 => 'Show', 0 => 'Hide')
		)
	);