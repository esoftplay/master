<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'show' => array(
		'text'    => 'Display',
		'type'    => 'radio',
		'option'  => array(1 => 'image', 0 => 'text'),
		'default' => 'text'
		),
	'limit' => array(
		'text'    => 'Number to Show',
		'type'    => 'text',
		'default' => '3',
		'attr'    => 'size=5',
		'add'     => 'link(s)',
		'tips'    => 'This is where you can define the number of links to show in this block'
		)
	);