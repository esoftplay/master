<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$path  = _class('content')->prune_path;
	if (is_file($path.$id.'cfg'))
	{
		_func('path');
		$path .= $id;
		$class = _class('content');
		$files = array($path.'.cfg', $path.'-cat.cfg');
		$data  = content_fetch($id, false);
		$langs = $class->r_lang;
		foreach ($langs as $i => $d)
		{
			$files[] = $path.'-'.$i.'.cfg';
		}
		if (is_dir($class->path.$id))
		{
			$files[] = $class->path.$id;
		}
		if (is_file($class->img_path.$data['image']))
		{
			$files[] = $class->img_path.$data['image'];
			$files[] = $class->img_path.'p_'.$data['image'];
		}
		foreach ($files as $file)
		{
			path_delete($file);
		}
		$q = "DELETE FROM `bbc_content_comment` WHERE `content_id`={$id}";
		$db->Execute($q);
		$q = "DELETE FROM `bbc_content_registrant` WHERE `content_id`={$id}";
		$db->Execute($q);
		$q = "DELETE FROM `bbc_content_related` WHERE `content_id`={$id}";
		$db->Execute($q);
		$q = "DELETE FROM `bbc_content_related` WHERE `related_id`={$id}";
		$db->Execute($q);
		if (config('manage', 'webtype') == '1')
		{
			$r    = $db->getCol("SELECT `tag_id` FROM `bbc_content_tag_list` WHERE `content_id`={$id}");
			$tags = array();
			foreach ($r as $i)
			{
				if (empty($tags[$i]))
				{
					$tags[$i] = 1;
				}else{
					$tags[$i]++;
				}
			}
			if (!empty($tags))
			{
				$db->Execute("DELETE FROM `bbc_content_tag_list` WHERE `content_id`={$id}");
				foreach ($tags as $tag_id => $count)
				{
					$db->Execute("UPDATE `bbc_content_tag` SET `total`=(`total`-{$count}), `updated`=NOW() WHERE `id`={$tag_id}");
				}
			}
		}
	}
}
$return = !empty($_GET['return']) ? $_GET['return'] : 'index.php?mod=content.content';
// redirect($return);