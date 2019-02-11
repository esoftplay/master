<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$is_post = false;
$add_sql = '';
$img     = _class('images', $Bbc->mod['dir']);
$img->setpath($Bbc->mod['dir']);
$image_size = intval(config('manage', 'image_size'));
if($prefix == 'edit_')
{
	if(!empty($_POST['submit_edit_update']))
	{
		if (isset($_FILES['edit_image']))
		{
			$image = $img->upload($_FILES['edit_image']);
			if (!empty($image))
			{
				$img->resize($image_size, $Bbc->mod['dir'].$image);
				$add_sql .= ", `image` = '{$image}'";
				$oldimage = $db->getOne("SELECT image FROM bbc_content_cat WHERE id={$category_id}");
				if (!empty($oldimage))
				{
					@unlink($Bbc->mod['dir'].$oldimage);
				}
			}
		}
		if (empty($add_sql) && !empty($_POST['edit_image_del']))
		{
			$add_sql .= ", `image` = ''";
			$oldimage = $db->getOne("SELECT image FROM bbc_content_cat WHERE id={$category_id}");
			if (!empty($oldimage))
			{
				@unlink($Bbc->mod['dir'].$oldimage);
			}
		}
		$_POST['edit_is_config'] = @intval($_POST['edit_is_config']);
		$config = ($_POST['edit_is_config']=='1') ? json_encode($_POST['config']) : '';
		$q = "UPDATE bbc_content_cat SET
			par_id		= ".$_POST['edit_par_id']."
		{$add_sql}
		,	is_config	= ".$_POST['edit_is_config']."
		,	config		= '".$config."'
		,	publish		= ".@intval($_POST['edit_publish'])."
		WHERE id=$category_id
		";
		$db->Execute($q);
		$q = "SELECT lang_id FROM bbc_content_cat_text WHERE cat_id=$category_id";
		$r_key_lang = $db->getCol($q);
		foreach((array)$_POST['edit_text'] AS $lang_id => $dt)
		{
			if(in_array($lang_id, $r_key_lang)) {
				$q = "UPDATE bbc_content_cat_text SET
					title				= '".$dt['title']."'
				, description	= '".$dt['description']."'
				, keyword			= '".$dt['keyword']."'
				WHERE cat_id	= $category_id AND lang_id=$lang_id
				";
			}else{
				$q = "INSERT INTO bbc_content_cat_text SET
					title				= '".$dt['title']."'
				, description	= '".$dt['description']."'
				, keyword			= '".$dt['keyword']."'
				, cat_id			= $category_id
				, lang_id			= $lang_id
				";
			}
			$db->Execute($q);
		}
		$is_post = true;
		$tmp_category_id = $category_id;
		$tmp_category_title = $_POST['edit_text'][lang_id()]['title'];
		echo msg('Success updating data.');
	}
}else{
	if(!empty($_POST['submit_add_update']))
	{
		if($sub_content)
		{
			$_POST['add_type_id'] = $type_id;
		}else{
			if(@$_POST['add_par_id'] > 0)
			{
				$_POST['add_type_id'] = $db->getOne("SELECT type_id FROM bbc_content_cat WHERE id=".intval($_POST['add_par_id']));
			}
		}
		if (isset($_FILES['add_image']))
		{
			$image = $img->upload($_FILES['add_image']);
			if (!empty($image))
			{
				$img->resize($image_size, $Bbc->mod['dir'].$image);
				$add_sql .= ", `image` = '{$image}'";
			}
		}
		$_POST['add_is_config'] = @intval($_POST['add_is_config']);
		$config = $_POST['add_is_config'] ? json_encode($_POST['config']) : '';
		$q = "INSERT INTO bbc_content_cat SET
			par_id		= ".$_POST['add_par_id']."
		,	type_id		= ".$_POST['add_type_id']."
		{$add_sql}
		,	is_config	= ".$_POST['add_is_config']."
		,	config		= '".$config."'
		,	publish		= ".@intval($_POST['add_publish'])."
		";
		$db->Execute($q);
		$tmp_category_id = $db->Insert_ID();
		$tmp_category_title = $_POST['add_text'][lang_id()]['title'];
		$q = "SELECT lang_id FROM bbc_content_cat_text WHERE cat_id=$tmp_category_id";
		$r_key_lang = $db->getCol($q);
		foreach((array)$_POST['add_text'] AS $lang_id => $dt)
		{
			if(in_array($lang_id, $r_key_lang)) {
				$q = "UPDATE bbc_content_cat_text SET
					title				= '".$dt['title']."'
				, description	= '".$dt['description']."'
				, keyword			= '".$dt['keyword']."'
				WHERE cat_id	= $tmp_category_id AND lang_id=$lang_id
				";
			}else{
				$q = "INSERT INTO bbc_content_cat_text SET
					title				= '".$dt['title']."'
				, description	= '".$dt['description']."'
				, keyword			= '".$dt['keyword']."'
				, cat_id			= $tmp_category_id
				, lang_id			= $lang_id
				";
			}
			$db->Execute($q);
		}
		$is_post = true;
		echo msg('Succees to add data.');
	}
}
if($is_post)
{
	content_category_update();
	$av_menu = @$_SESSION[$prefix.'content_category_menu'];
	if(!empty($av_menu))
	{
		$module_id = $sys->get_module_id('content');
		foreach((array)$av_menu AS $menu)
		{
			if($menu['code']=='delete')
			{
				menu_delete($menu['id']);
			}else
			if($menu['code']=='new')
			{
				$q="INSERT INTO bbc_menu
						SET par_id			= '".$menu['par_id']."'
						, module_id			= '".$module_id."'
						, seo						= '".menu_seo($menu['seo'], $menu['title'])."'
						, link					= 'index.php?mod=content.list&id=$tmp_category_id&title=".urlencode($tmp_category_title)."'
						, orderby				= '".$menu['orderby']."'
						, cat_id				= '".$menu['cat_id']."'
						, is_content_cat= 1
						, content_cat_id= '".$tmp_category_id."'
						, protected			= 0
						, is_admin			= 0
						, active				= 1
				";
				if($db->Execute($q))
				{
					$menu_id = $db->Insert_ID();
					$q = "SELECT lang_id FROM bbc_menu_text WHERE menu_id=$menu_id";
					$r_key_lang = $db->getCol($q);
					// INSERT TITLE
					foreach((array)$menu['titles'] AS $lang_id => $title)
					{
						if(in_array($lang_id, $r_key_lang))
						{
							$q = "UPDATE bbc_menu_text SET title	= '".$title."'
							WHERE menu_id = ".$menu_id." AND lang_id		= $lang_id
							";
						}else{
							$q = "INSERT INTO bbc_menu_text
							SET menu_id = ".$menu_id."
							, title			= '".$title."'
							, lang_id		= $lang_id
							";
						}
						$db->Execute($q);
					}
					// REPAIR ORDERBY..
					$q="UPDATE bbc_menu SET orderby=(orderby+1)
						WHERE cat_id= ".$menu['cat_id']."
						AND par_id	= ".$menu['par_id']."
						AND is_admin= 0
						AND orderby>=".$menu['orderby']."
						AND id		 != ".$menu_id."
					";
					$db->Execute($q);
					menu_repair();
				}
			}
		}
		include 'menu_fetch.php'; // REFETCH MENU IF MENU IS CHANGING...
	}
}

