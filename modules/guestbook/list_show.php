<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$conf = get_config('guestbook', 'guestbook');
_func('avatar');
_func('smiley');
$sql = 'ORDER BY ';
switch(@$conf['orderby'])
{
	case '2': $sql .= '`id` ASC';break;
	case '3': $sql .= '`name` ASC';break;
	default	: $sql .= '`id` DESC';break;
}
$page   = @intval($_GET['id']);
$sql   .= ' LIMIT '.@intval($page*$conf['tot']).', '.@intval($conf['tot']);
$q      = "SELECT * FROM guestbook WHERE publish=1 ".$sql;
$r_list = $db->getAll($q);
if(!empty($r_list))
{
	foreach ($r_list as $i => $data)
	{
		$image = '';
		if (!empty($data['params']))
		{
			$params = config_decode($data['params']);
			if (!empty($params['image']))
			{
				$image = $params['image'];
			}
		}
		if (empty($image))
		{
			$image = $sys->avatar($data['email'], 1);
		}
		$r_list[$i]['image']   = $image;
		$r_list[$i]['message'] = smiley_parse($data['message']);
	}
	include tpl('list_show.html.php');
}
if($Bbc->mod['task'] != 'list_show')
{
	$q     = "SELECT COUNT(*) FROM guestbook WHERE publish=1 ";
	$found = $db->getOne($q);
	if (empty($found))
	{
		echo msg(lang('guestbook empty'));
	}
	echo page_list($found, $conf['tot'], $page, 'id', $Bbc->mod['circuit'].'.list');
}else{
	// echo msg(lang('guestbook empty'));
}
if($Bbc->mod['task'] == 'list_show') $sys->stop();
