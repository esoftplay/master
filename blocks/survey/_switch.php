<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// menampilkan form polling yang telah anda buat pada module survey, polling yang ditampilkan otomatis akan hilang jika user telah mengisi poling tersebut
$ids = array();
$config['ids'] = (isset($config['ids']) && is_array($config['ids'])) ? $config['ids'] : array();
$survey_polling = isset($_SESSION['survey_polling']) ? $_SESSION['survey_polling'] : array();
foreach($config['ids'] AS $i)
{
	if(!in_array($i, $survey_polling)) $ids[] = $i;
}
if(count($ids) > 0)
{
	$pollings = array();
	$q = "SELECT id, question FROM survey_polling AS p LEFT JOIN survey_polling_text AS t ON (p.id=t.polling_id AND lang_id=".lang_id().")
	WHERE publish=1 AND id IN (".implode(',', $ids).") ORDER BY RAND() LIMIT ".intval($config['limit']);
	$r = $db->getAll($q);
	$ids = array();
	foreach((array)$r AS $d)
	{
		$pollings[$d['id']] = array(
		  'question'=> $d['question']
		, 'option'	=> array()
		);
		$ids[] = $d['id'];
	}
	$q = "SELECT * FROM survey_polling_option AS o LEFT JOIN survey_polling_option_text AS t ON (o.id=t.polling_option_id AND lang_id=".lang_id().")
	WHERE polling_id IN (".implode(',', $ids).") AND publish=1 ORDER BY polling_id, orderby ASC";
	$r = $db->getAll($q);
	foreach((array)$r AS $d)
	{
		$pollings[$d['polling_id']]['option'][] = array($d['id'], $d['title']);
	}
	foreach($pollings AS $polling_id => $data)
	{
		include tpl(@$config['template'].'.html.php', 'survey.html.php');
	}
}elseif(count($config['ids']) > 0)
{
	echo lang('Polling finished');
}else{
	echo lang('No Polling');
}
