<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=====================================
 * Menu Position..
/*===================================*/
$q = "SELECT id, name FROM bbc_menu_cat ORDER BY orderby ASC";
$_setting = array(
	'cat_id' => array(
		'text'   => 'Category',
		'type'   => 'select',
		'option' => $db->getAll($q)
		),
	'submenu' => array(
		'text'   => 'Submenu',
		'type'   => 'select',
		'option' => array(
			'top left'     => 'Up Left',
			'top right'    => 'Up Right',
			'bottom left'  => 'Down Left',
			'bottom right' => 'Down Right'
			),
		'default' => 'bottom right',
		'tips'    => 'Submenu direction is only work if you select Block Template as vertical or horizontal'
		)
	);