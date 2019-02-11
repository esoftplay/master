<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(@is_numeric($_GET['id']) && isset($_POST['Submit']))
{
	$id = @intval($_GET['id']);
	$option = intval($_POST['option']);
	$survey_polling = isset($_SESSION['survey_polling']) ? $_SESSION['survey_polling'] : array();
	if(!in_array($id, $survey_polling))
	{
		$q = "UPDATE survey_polling_option SET voted=(voted+1) WHERE polling_id=$id AND id=$option AND publish=1";
		$db->Execute($q);
		$survey_polling[] = $id;
		$_SESSION['survey_polling'] = $survey_polling;
	}
	redirect();
}

if(!$sys->menu_real) $sys->nav_add(lang('Polling Result'));

$id		= @intval($_GET['id']);
$page	= @intval($_GET['page']);
$limit_per_page = 12;
$sql	= ($id > 0) ? " AND id=$id" : '';

$found= "SELECT COUNT(*) FROM survey_polling AS p LEFT JOIN survey_polling_text AS t ON 
					(p.id=t.polling_id AND lang_id=".lang_id().") WHERE publish=1 $sql";
$q = "SELECT id, question FROM survey_polling AS p LEFT JOIN survey_polling_text AS t ON (p.id=t.polling_id AND lang_id=".lang_id().")
WHERE publish=1 $sql ORDER BY id DESC LIMIT $page, $limit_per_page";
$r = $db->getAll($q);
$pollings = $ids = array();
foreach((array)$r AS $d)
{
	$pollings[$d['id']] = array(
	  'question'=> $d['question']
	, 'option'	=> array()
	, 'total'		=> 0
	);
	$ids[] = $d['id'];
}
$q = "SELECT * FROM survey_polling_option AS o LEFT JOIN survey_polling_option_text AS t ON (o.id=t.polling_option_id AND lang_id=".lang_id().")
WHERE polling_id IN (".implode(',', $ids).") AND publish=1 ORDER BY polling_id, orderby ASC";
$r = $db->getAll($q);
foreach((array)$r AS $d)
{
	$pollings[$d['polling_id']]['option'][]= $d;
	$pollings[$d['polling_id']]['total']	+= $d['voted'];
}
if(count($pollings) > 0)
{
	include tpl ('polling.html.php');
}else{
	echo msg(lang('No Polling Found.'));
}
