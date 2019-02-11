<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['token']))
{
	@list($token, $config) = _class('comment')->decode($_GET['token']);
	if (!empty($config['db']))
	{
		$db = ${$config['db']};
		unset($config['db']);
	}
	if (!empty($config['id'])
		&& !empty($config['expire'])
		&& $config['expire'] > time())
	{
		extract($config);
		$par_id = !empty($_GET['par_id']) ? intval($_GET['par_id']) : @intval($config['par_id']);
		$reply  = empty($config['admin']) ? 'reply_on' : 'reply_all';
		$sql    = empty($config['admin']) ? ' AND publish=1' : '';
		$sql   .= ' AND par_id='.$par_id;
		if (!empty($config['comment_id']))
		{
			$page  = 0;
			$pages = 0;
			$start = 0;
			$c_Q   = "SELECT *, `{$reply}` AS reply FROM {$table} WHERE id={$comment_id} ORDER BY id ASC LIMIT 1";
		}else{
			$page  = @intval($_GET['page_comment']);
			$pages = 0; // total page (only calculate if par_id>0)
			$start = $page*$list;
			$c_Q   = "SELECT *, `{$reply}` AS reply FROM {$table} WHERE {$field}_id={$id} {$sql} ORDER BY id ASC LIMIT {$start}, {$list}";
		}
		$r_list = 'r_list'.$par_id;
		if ($db->Execute($c_Q))
		{
			$o = array();
			if($db->resid)
			{
			  while ($r = mysqli_fetch_assoc($db->resid))
			  {
			  	$o[] = $r;
			  }
		  }
			$$r_list = $o;
		}else{
			include_once __DIR__.'/repair-comment.php';
			$$r_list = $db->getAll($c_Q);
		}
		// Hitung jumlah halaman jika ini subComment
		if ($par_id > 0 && empty($config['comment_id']))
		{
			$total = $db->getOne("SELECT COUNT(*) FROM {$table} WHERE {$field}_id={$id} {$sql} ");
			$pages = ceil($total/$list);
		}
		include tpl('comment_list.html.php');
	}
}