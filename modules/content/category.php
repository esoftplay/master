<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$category = $db->cacheGetRow("SELECT * FROM bbc_content_cat WHERE publish=1 AND id={$id}");
	if (!empty($category))
	{
		$limit    = 15;
		$page     = @intval($_GET['page']);
		$start    = $page*$limit;
		$link     = site_url('index.php?content.category&id='.$id);
		$text     = $db->cacheGetRow("SELECT * FROM bbc_content_cat_text WHERE lang_id=".lang_id()." AND cat_id={$id}");
		$category = array_merge($category, $text);
		$total    = $db->cacheGetOne("SELECT COUNT(*) FROM bbc_content_cat WHERE publish=1 AND par_id={$id}");
		$subcat   = $db->cacheGetAll("SELECT * FROM bbc_content_cat WHERE publish=1 AND par_id={$id} ORDER BY id ASC LIMIT {$start}, {$limit}");
		foreach ($subcat as &$sub)
		{
			$text           = $db->cacheGetRow("SELECT * FROM bbc_content_cat_text WHERE lang_id=".lang_id()." AND cat_id=".$sub['id']);
			$sub            = array_merge($sub, $text);
			$sub['total']   = $db->cacheGetOne("SELECT COUNT(*) FROM bbc_content_category WHERE cat_id=".$sub['id']);
			if ($sub['total'])
			{
				$sub['updated'] = $db->GetOne("SELECT c.created FROM bbc_content_category AS y 
					LEFT JOIN bbc_content AS c ON (c.id=y.content_id) 
					WHERE y.cat_id=".$sub['id']." ORDER BY y.content_id DESC LIMIT 1");
			}else{
				$sub['updated'] = '';
			}
		}
		include tpl('category.html.php');
	}
}
