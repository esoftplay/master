<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$r_period = array('SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH');
$_setting = array(
	'duration' => array(
		'text' => 'Idle Duration (public)',
		'tips' => 'Insert value for how long user stay login in idle, user will logout automatically after specified time.',
		'type' => 'text'
	),
	'period'         => array(
		'text'   => 'Idle Period (public)',
		'tips'   => 'Select period type for duration',
		'type'   => 'select',
		'option' => $r_period
	),
	'duration_admin' => array(
		'text' => 'Idle Duration (admin)',
		'tips' => 'Insert value for how long admin stay login in idle time, this admin will logout automatically after specified time.',
		'type' => 'text'
	),
	'period_admin' => array(
		'text'   => 'Idle Period (admin)',
		'tips'   => 'Select period type for duration',
		'type'   => 'select',
		'option' => $r_period
	),
	'method_admin' => array(
		'text'    => 'Admin Authorization',
		'tips'    => 'This is where you can define which method to be used for user to login into the admin section, Make sure to insert email in "Edit Contact" tab in "Control Panel / User Manager" if you select Google/Yahoo',
		'type'    => 'select',
		'option'  => array(1=>'Google', 2=>'Yahoo', 3=>'Facebook', 0=>'Password'),
		'default' => '0'
	)
);
$params = array(
	'config'=> $_setting,
	'name'	=> 'logged',
	'title'	=> 'Idle Logged Configuration',
	'id'		=> 0
);
$conf->set($params);
