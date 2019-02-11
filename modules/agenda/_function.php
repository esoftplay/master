<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once _ROOT.'modules/content/_class.php';
function agenda_delete($ids)
{
	$ids = is_array($ids) ? $ids : (is_numeric($ids) ? array($ids) : array());
	if(!empty($ids))
	{
		global $db;
		$ids = implode(',', $ids);
		$q = "SELECT content_id FROM `agenda` WHERE id IN ($ids)";
		$c_ids = $db->getCol($q);
		content_delete($c_ids);
		$q = "DELETE FROM `agenda` WHERE id IN ($ids)";
		$db->Execute($q);
	}
}
function agenda_cat($id = 0)
{
	$output = array(
		1 => 'Events'
	,	2 => 'Daily'
	,	3 => 'Weekly'
	,	4 => 'Monthly'
	,	5 => 'Yearly'
	);
	if($id <= 5 && $id > 0) return $output[$id];
	else return $output;
}

function agenda_calendar($year = 'none', $month = 'none')
{
	global $db;
	if(isset($year) && is_numeric($year) && strlen($year)==4) {
	}else	$year = date('Y');
	if(isset($year) && is_numeric($month) && $month < 13) {
		$month = strlen($month) == 1 ? '0'.$month : $month;
	}else	$month = date('m');
	$q = "SELECT DAY(start_date) AS `date`, a.content_id, t.title 
			FROM agenda AS a LEFT JOIN bbc_content_text AS t ON (t.content_id=a.content_id AND lang_id=".lang_id().")
			WHERE MONTH(start_date)='$month' AND YEAR(start_date)='".$year."' AND publish=1";
	$r = $db->GetAll($q);
	$d = array();
	foreach ((array)$r as $v) {
		$d[$v['date']][] = content_link($v['content_id'], $v['title']);
	}
	$data = array();
	foreach((array)$d AS $date => $dt) {
		$date_now = strlen($date) == 1 ? '0'.$date : $date;
		if(isset($dt[1])) $data[$date] = site_url($Bbc->mod['circuit'].'.events&id='.$year.'-'.$month.'-'.$date_now);
		else $data[$date] = $dt[0];
	}
	$prefs = array (
		'show_next_prev'  => TRUE
	,	'next_prev_url'   => 'index.php?mod=agenda.calendar&id='
	);
	$calendar = _class('calendar', $prefs);
	return $calendar->generate($year, $month, $data);
}