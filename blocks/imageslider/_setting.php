<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT id, CONCAT(title,' (',width,' x ',height,'px)') AS title FROM imageslider_cat ORDER BY title ASC";
$r = $db->getAll($q);
$_setting = array(
	'cat_id' => array(
		'text'   => 'Category',
		'type'   => 'select',
		'option' => $r,
		'tips'   => 'Select which category will show in this block'
		),
	'fixsize' => array(
		'text'    => 'Fix Image Sizes',
		'type'    => 'checkbox',
		'default' => '0',
		'option'  => 'Fix image size as in category'
		),
	'caption' => array(
		'text'   => 'Image Caption',
		'type'   => 'checkbox',
		'option' => 'Display Image Title'
		),
	'indicator' => array(
		'text'   => 'Page Indicator',
		'type'   => 'checkbox',
		'option' => 'Display Page Indicator'
		),
	'control' => array(
		'text'   => 'Page Control',
		'type'   => 'checkbox',
		'option' => 'Display Page Control'
		)
	);