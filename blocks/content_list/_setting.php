<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

_func('array');
$q = "SELECT id, par_id, title FROM bbc_content_cat AS c
LEFT JOIN bbc_content_cat_text AS t ON (t.cat_id=c.id AND t.lang_id=".lang_id().")
WHERE 1 ORDER BY par_id, title";
$_setting = array(
	'show_numb' => array(
		'text'    => 'Show Number',
		'type'    => 'text',
		'default' => '1',
		'add'     => 'item(s)',
		'tips'    => 'Insert the number of title to show in this block'
		),
	'cat_id' => array(
		'text'   => 'Category',
		'type'   => 'select',
		'option' => array_path($db->getAll($q)),
		'tips'   => 'Select category content to display in this block'
		),
	'orderby' => array(
		'text'   => 'Ordered',
		'type'   => 'select',
		'option' => array(1 => 'DESC', 2 => 'ASC', 0 => 'RANDOM')
		),
	'more' => array(
		'text'   => 'More Link',
		'type'   => 'radio',
		'option' => array(1=>'Show',0=>'Hidden')
		)
	);