<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include '../_config.php';
$_setting = array(
	'template' => array(
		'text'   => 'Template',
		'type'   => 'select',
		'option' => $style,
		'tips'   => 'Select style to show survey list'
	),
	'noreentry' => array(
		'text'   => 'No Re-entry',
		'type'   => 'radio',
		'option' => array('1'=>'Yes', '0'=>'No'),
		'tips'   => 'if user already post the survey, the form will not available any more'
	),
	'def_user_id' => array(
		'text' => 'User ID Default',
		'type' => 'text',
		'help' => 'This is were you can declare User ID for default when user submit survey without login, Or leave it blank/zero to make user insert their personal data by them selves'
	),
	'publish' => array(
		'text'   => 'Publish entry',
		'type'   => 'radio',
		'option' => array('1'=>'Auto', '0'=>'Manual'),
		'tips'   => 'publish all survey entries from visitor'
	),
	'alert' => array(
		'text'   => 'Alert entry',
		'type'   => 'radio',
		'option' => array('1'=>'Yes', '0'=>'No'),
		'tips'   => 'Alert author if entry is comming.'
	),
	'email' => array(
		'text'    => 'Author Email',
		'type'    => 'text',
		'checked' => 'email',
		'tips'    => 'Insert author email or leave it blank to use global email if alert entry is "ON"'
	)
);
$output = array(
	'config' => $_setting,
	'name'   => 'main',
	'title'  => 'Survey Configuration'
);
$f = _class('bbcconfig');
$f->set($output);
echo $f->show();
