<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id     = 0;
$page   = @intval($_GET['page']);
$cat    = array();
$config = config('list');

if(!empty($_GET['id']) && is_numeric($_GET['id']))
{
    $id        = intval($_GET['id']);
    $q         = "SELECT * FROM `bbc_content_type` WHERE id={$id}";
    $cat       = $db->cacheGetRow($q);
    $cat['id'] = $id;
}else{
    $q   = "SELECT * FROM `bbc_content_type` WHERE active=1 LIMIT 1";
    $cat = $db->cacheGetRow($q);
    $id  = $cat['id'];
}
if(!empty($config))
{
	if ($Bbc->mod['task']=='popular')
	{
		$time = '-1 MONTH';
		if (!empty($_GET['id']) && !is_numeric($_GET['id']))
		{
			$t = strtotime($_GET['id']);
			if (is_numeric($t))
			{
				$time = '-'.$_GET['id'];
			}
		}
		$cat['title'] = lang('Popular Content');
		$limit_time   = date('Y-m-d H:i:s', strtotime($time));
		$q_where      = "(c.`modified` > '{$limit_time}' || c.`created` > '{$limit_time}')";
		$q_order      = 'hits';
	}else{
		$cat['title'] = lang('Latest Content');
		$q_where       = '1';
		$q_order       = 'id';
	}
	if(!$sys->menu_real)
	{
		$sys->nav_change($cat['title']);
	}
	$_url    = 'index.php?mod=content.'.$Bbc->mod['task'];
	$start   = $config['tot_list'] * $page;
	$content = ($config['intro']) ? 't.intro' : 't.content' ;
	$q       = "SELECT SQL_CALC_FOUND_ROWS c.*, t.`title`, $content AS `intro`
	FROM  `bbc_content` AS c
	LEFT JOIN `bbc_content_text` AS t ON (c.`id`=t.`content_id` AND t.`lang_id`=".lang_id().")
	WHERE c.`publish`=1 AND {$q_where} ORDER BY `{$q_order}` DESC LIMIT ".$start.", ".$config['tot_list'];
	$r_type = $db->cacheGetAll($q);
	$total	= $db->cacheGetOne("SELECT FOUND_ROWS(), '".md5($q)."'");
	$cat['list']       = $r_type;
	$cat['link']       = site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task']);
	$cat['total']      = $total;
	$cat['total_page'] = ceil($total / $config['tot_list']);
	$cat['config'] = $config;
	include tpl(@$config['template'], 'list.html.php');
}else{
	echo msg(lang('not found'));
}
