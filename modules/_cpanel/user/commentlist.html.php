<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$cfg = array(
	'table'   => 'bbc_content_comment',
	'field'   => 'content',
	'id'      => 0,						// id dari detail data misal content_id
	'par_id'  => 0,						// par_id jika ini reply message maka pastinya par_id > 0
	'list'    => 9,						// number of comment to show per page
	'page'    => 0,						// current page
	'module'  => 'content'		// module name
	);
foreach ($_GET as $key => $value)
{
	if (isset($cfg[$key]))
	{
		switch ($key)
		{
			case 'table':
			case 'field':
			case 'module':
				$cfg[$key] = preg_replace('~[^a-z0-9\_]+~is', '', $_GET[$key]);
				break;

			default:
				$cfg[$key] = intval($_GET[$key]);
				break;
		}
	}
}
extract($cfg);
$start  = $page*$list;
$pages  = 1;
$total  = 0;
$r_list = $db->getAll( "SELECT *, `reply_on` AS reply FROM `{$table}` WHERE {$field}_id={$id} AND par_id={$par_id} AND publish=1 ORDER BY id ASC LIMIT {$start}, {$list}");
if (!empty($r_list))
{
	$total = intval($db->getOne( "SELECT COUNT(*) FROM `{$table}` WHERE {$field}_id={$id} AND par_id={$par_id} AND publish=1"));
	$pages = ceil($total/$list);
	$r     = array(
		'unset'  => array('user_id', 'reply_all', 'reply_on', $field.'_id', $field.'_title', 'publish'),
		'intval' => array('id', 'par_id', 'reply'),
		);
	foreach ($r_list as &$d)
	{
		foreach ($r['unset'] as $k)
		{
			unset($d[$k]);
		}
		foreach ($r['intval'] as $k)
		{
			$d[$k] = intval($d[$k]);
		}
		if (empty($d['image']))
		{
			$d['image'] = $sys->avatar($d['email'], 1);
		}
		if (!empty($d['website']) && !preg_match('/^(?:ht|f)tps?:\/\//is', $d['website']))
		{
			$d['website'] = 'http://'.$d['website'];
		}
	}
}
$output = array(
	'list'       => $r_list,
	'total'      => $total,
	'total_page' => $pages
	);
$data_output = _cpanel_result($output);