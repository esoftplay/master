<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include_once _ROOT.'modules/agenda/_function.php';
$r = agenda_cat();
$r[6] = 'Calendar';
$_setting = array(
	'type' => array(
		'text'   => 'Type',
		'type'   => 'select',
		'option' => $r,
		'tips'   => 'Select Type of Agenda'
		),
	'show' => array(
		'text'    => 'Max. Show',
		'type'    => 'text',
		'default' => '5',
		'tips'    => 'define max. content to show, if selection is not calendar'
		)
	);