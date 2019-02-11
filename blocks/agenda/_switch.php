<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// menampilkan data agenda baik itu event, schedule maupun calendar sesuai dengan opsi yang anda pilih berdasarkan data yang telah anda masukkan pada module agenda
include_once _ROOT.'modules/agenda/_function.php';
if($config['type'] == '6')
{
	echo agenda_calendar();
}else{
	include_once _ROOT.'modules/agenda/_class.php';
	$agenda = new agenda_class();
	$q = "SELECT a.*, t.title, t.intro FROM agenda AS a LEFT JOIN bbc_content_text AS t ON
	(t.content_id=a.content_id AND t.lang_id=".lang_id().") WHERE a.publish=1
	AND a.cat_id=".intval($config['type'])." ORDER BY `start_date` DESC LIMIT 0, ".intval($config['show']);
	$r = $db->getAll($q);
	$time_format = $agenda->get_time_format($config['type']);
	include tpl(@$config['template'].'.html.php', 'event.html.php');
}