<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT id, question FROM survey_polling AS p LEFT JOIN survey_polling_text AS t "
		.	"ON (p.id=t.polling_id AND lang_id=".lang_id().") WHERE publish=1 ORDER BY id DESC";
$_setting = array(
	'ids'=> array(
		'text'		=> 'Select Polling',
		'type'		=> 'select',
		'is_arr'	=> true,
		'option'	=> $db->getAll($q),
		'tips'		=> 'Select Polling'
		),
	'limit'=> array(
		'text'		=> 'Total show',
		'type'		=> 'text',
		'default'	=> '1',
		'tips'		=> 'Items to show in block'
		)
	);
