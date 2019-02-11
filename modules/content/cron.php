<?php
if (!defined('_VALID_BBC'))
{
  $_SERVER['HTTP_HOST'] = '';
  include dirname(dirname(dirname(__FILE__))).'/config.php';
  define('_VALID_BBC', 1);
  include_once _ROOT.'includes/system/db.class.php';
  include_once _ROOT.'includes/function/file.php';
  include_once _ROOT.'includes/function/path.php';
  include_once _ROOT.'includes/function/menu.php';
  function _func()
  {
  }
}else $sys->stop();
$id = @intval($_GET['id']);
$limit = @intval($_GET['limit']);
$limit = $limit ? $limit : 1;
$db->debug=1;
if(@is_numeric($_GET['id']))
{
	if(empty($id))
	{
		$tmp= _ROOT.'images/modules_content_cron_php.txt';
		$id = intval(file_read($tmp));
		$q = "SELECT id FROM bbc_content WHERE id > $id";
		$i = $db->getOne($q);
		if($i > $id)
		{
			$id = $i;
			file_write($tmp, $id);
			echo '<script>function init() {setInterval(function(){window.location.reload();}, 5000);};window.onload = init;</script>';
		}else die;
	}
	$path = $Content->img_path;
	$q = "SELECT id, image FROM bbc_content WHERE id=$id";
	$r = $db->getAll($q);
	foreach($r AS $d)
	{
		if(!empty($d['image']))
		{
			if(file_exists($path.$d['image']))
			{
				preg_match('~(\.[a-z]+)$~is', $d['image'], $match);
				$q = "SELECT title FROM bbc_content_text WHERE content_id=".$d['id']." AND lang_id=".lang_id();
				$new = menu_save($db->getOne($q));
				if(!empty($new) && $new.$match[1] != $d['image'])
				{
					if(file_exists($path.$new.$match[1]))
					{
						$new = $d['id'].'-'.$new;
					}
					$new .= $match[1];
					rename($path.$d['image'], $path.$new);
					rename($path.'p_'.$d['image'], $path.'p_'.$new);
					$q = "UPDATE bbc_content SET image='$new' WHERE id=".$d['id'];
					$db->Execute($q);
				}
			}
		}
	}
}else{
	_func('path');
	$cache_file = _ROOT.'images/cron.txt';
	preg_match('~'.date('Ymd').'_([0-9]+)$~is', file_read($cache_file), $match);
	$limit_done = @intval($match[1]);
	if($limit_done < $limit)
	{
		$q = "SELECT id FROM bbc_content WHERE publish=0 ORDER BY id ASC LIMIT 1";
		$r = $db->getCol($q);
		if(!empty($r))
		{
			foreach($r AS $i)
			{
				$q = "UPDATE bbc_content SET publish=1 WHERE id=$i";
				$db->Execute($q);
				$limit_done++;
			}
			$path = _CACHE;
			path_delete($path);
			file_write($path.'index.html', '');
			file_write($cache_file, date('Ymd').'_'.$limit_done);
		}
	}
}
echo implode('<br />',$Bbc->debug);
