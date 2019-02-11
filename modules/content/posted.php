<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(content_posted_permission())
{
	$title = lang('Posted List');
	if(!$sys->menu_real)
	{
		$sys->nav_change($title);
	}
	$conf = get_config('content', 'entry');
	$page = @intval($_GET['page']);
	if(!empty($conf['animated']))
	{
		$q     = "SELECT COUNT(*) FROM bbc_content WHERE created_by=".$user->id;
		$found = $db->getOne($q);
		echo page_ajax($found, $conf['tot'], $Bbc->mod['circuit'].'.posted_list&id=');
	}else{
		if(content_posted_permission())
		{
			$conf   = get_config('content', 'entry');
			$config = get_config('content', 'list');
			$post   = array();
			$sql    = ' ORDER BY ';
			switch(@$conf['orderby'])
			{
				case '1': $sql .= '`id` DESC';	break;
				case '2': $sql .= '`id` ASC';		break;
				case '3': $sql .= '`hits` ASC';	break;
				case '4':
				default	: $sql .= '`hits` DESC';	break;
			}
			$conf['tot'] = @intval($conf['tot']);
			$sql .= ' LIMIT '.($page*$conf['tot']).', '.$conf['tot'];
			$q = "SELECT * FROM bbc_content AS c LEFT JOIN bbc_content_text AS t
					ON(c.`id`=t.`content_id` AND `lang_id`=".lang_id().")
			 WHERE created_by=".$user->id.$sql;
			$post['list']       = $db->getAll($q);
			$post['total']      = $db->getOne("SELECT COUNT(*) FROM bbc_content WHERE created_by=".$user->id);
			$post['total_page'] = ceil($post['total'] / $conf['tot']);
			$cat = array(
				'id'         => 0,
				'title'      => $title,
				'list'       => $post['list'],
				'link'       => site_url('index.php?mod=content.posted'),
				'total'      => $post['total'],
				'total_page' => $post['total_page'],
				'rss'        => '',
				'config'     => $config,
				);
			include tpl('list.html.php');
		}else{
			$sys->denied();
		}
	}
	?>
	<button type="button" class="btn btn-default" onclick="document.location.href='<?php echo $Bbc->mod['circuit'].'.posted_form'; ?>'"><?php echo lang('Add Entry'); ?></button>
	<?php
}else{
	$sys->denied();
}