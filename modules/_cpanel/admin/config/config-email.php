<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'name'=> array(
		'text' => 'Name',
		'type' => 'text',
		'tips' => 'This is the global email name. If the email is sent from the website with unspecified sender\'s name then this field will be used.'
	),
	'address'		=> array(
		'text' => 'Address',
		'type' => 'text',
		'tips' => 'This is the global email address. If the email is sent from the website with unspecified sender then this address is used.'
	),
	'subject'		=> array(
		'text' => 'Subject',
		'type' => 'text',
		'tips' => 'This field is used as prefix in email\'s subject.'
	),
	'footer'=> array(
		'text'		=> 'Footer',
		'type'		=> 'textarea',
		'tips'		=> 'This content will be placed at the end of email content.',
		'attr'		=> " cols=60 rows=5"
	)
);
$params = array(
	'config' => $_setting,
	'name'   => 'email',
	'title'  => 'Email Configuration',
	'id'     => 0
);
$conf->set($params);
