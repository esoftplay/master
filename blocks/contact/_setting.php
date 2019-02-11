<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'type' => array(
		'text'   => 'Display Block Type',
		'type'   => 'select',
		'option' => array('default', 'widget'),
		'tips'   => 'Select which block you would like to display in this area'
		)
	);