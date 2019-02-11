<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($_FILES['params'])
	&& is_uploaded_file($_FILES['params']['tmp_name']) 
	&& strtolower(substr($_FILES['params']['name'], -5)) == '.json')
{
	_func('path');
	$q="SELECT * FROM bbc_module WHERE id={$id}";
	$mod	= $db->getRow($q);
	if(!$db->Affected_rows()) die();

	$_file = _ROOT.'images/param.json';
	move_uploaded_file($_FILES['params']['tmp_name'], $_file);
	@chmod ($_file, 0777);
	$txt = file_read($_file);
	@unlink($_file);
	$param = config_decode($txt);
	if (!empty($param) && !empty($param['data']))
	{
		// PARSING MODULE DATA
		$fields = array();
		foreach((array)$param['data'] AS $var => $val) {
			if($var != 'id' && $var != 'name' && $var != 'created')
				$fields[] = "`$var`='$val'";
		}
		$q="UPDATE bbc_module
				SET ".implode(', ', $fields)."
				WHERE id = ".$mod['id'];
		$db->Execute($q);

		// PARSING CONFIG
		$r_config = $param['config'];
		if(is_array($r_config) AND count($r_config) > 0)
		{
			$q = "DELETE FROM bbc_config WHERE module_id = ".$mod['id'];
			$db->Execute($q);
			foreach($r_config AS $key => $value)
			{
				$q = "INSERT INTO bbc_config
							SET name		= '".$key."'
							,	params		= '".$value."'
							,	module_id	= ".$mod['id'];
				$db->Execute($q);
			}
			$db->cache_clean('config/');
		}

		// PARSING MODULE EMAIL TEMPLATE
		$r_email			= $param['email'];
		$r_email_text = $param['email_text'];
		if(is_array($r_email) AND count($r_email) > 0)
		{
			$q = "SELECT id FROM bbc_email WHERE module_id='".$mod['id']."'";
			$ids = $db->getCol($q);$ids[] = 0;
			$q = "DELETE FROM bbc_email WHERE module_id='".$mod['id']."'";
			$db->Execute($q);
			$q = "DELETE FROM bbc_email_text WHERE email_id IN(".implode(',', $ids).")";
			$db->Execute($q);
			$r_lang = get_lang();
			foreach($r_email AS $id => $data)
			{
				$q="INSERT INTO bbc_email
						SET module_id		= '".$mod['id']."'
						,	name					= '".$data['name']."'
						,	global_subject= '".$data['global_subject']."'
						,	global_footer	= '".$data['global_footer']."'
						,	global_email	= '".$data['global_email']."'
						,	from_email		= '".$data['from_email']."'
						,	from_name			= '".$data['from_name']."'
						,	description		= '".$data['description']."'
						";
				$db->Execute($q);
				$email_id = $db->Insert_ID();
				foreach($r_lang AS $lang_id => $lang_code)
				{
					$subject = @$r_email_text[$id][$lang_code]['subject'];
					$content = @$r_email_text[$id][$lang_code]['content'];
					$q = "INSERT INTO bbc_email_text SET email_id=$email_id, lang_id=$lang_id, subject='".$subject."', content='".$content."'";
					$db->Execute($q);
				}
			}
		}


		// PARSING MODULE MENU
		function insertMenuRecure($array, $menu_id = 0, $par_id = 0)
		{
			global $db, $mod, $used, $r_cat_id, $r_lang, $l_code;
			if(is_array($array['menu']) and count($array['menu']) > 0)
			{
				$orderby = 0;
				foreach($array['menu'] AS $id => $data)
				{
					if($data['par_id'] == $par_id and !in_array($id, $used))
					{
						if($orderby==0)
						{
							$q ="SELECT COUNT(*) FROM bbc_menu WHERE par_id=".intval($par_id)
								.	" AND is_admin=".$data['is_admin'];
							$orderby = $db->getOne($q);
						}
						$orderby++;
						if($data['is_admin']==0)
						{
							$data['seo'] = @menu_seo($data['seo'], $array['menu_text'][$data['id']][$l_code]);
						}
						if($data['is_admin'])
							$data['cat_id'] = 0;
						elseif(!in_array($data['cat_id'], $r_cat_id))
							$data['cat_id'] = $r_cat_id[0];
						$q="INSERT INTO bbc_menu
								SET seo				= '".$data['seo']."'
								, module_id		= '".$mod['id']."'
								, par_id			= '".$menu_id."'
								, link				= '".$data['link']."'
								, orderby			= '".$orderby."'
								, cat_id			= '".$data['cat_id']."'
								, protected		= '".$data['protected']."'
								, is_admin		= '".$data['is_admin']."'
								, is_content	= '0'
								, active			= '".$data['protected']."'
						"; $db->Execute($q);
						$menu_id_new = $db->Insert_ID();
						foreach((array) $array['menu_text'][$data['id']] AS $code => $title)
						{
							if(isset($r_lang[$code]))
							{
								$q="INSERT INTO bbc_menu_text
										SET menu_id= '".$menu_id_new."'
										, title		= '".$title."'
										, lang_id	= '".$r_lang[$code]."'
										";
								$db->Execute($q);
							}
						}
						$used[] = $id;
						insertMenuRecure($array, $menu_id_new, $data['id']);
					}
				}
			}
		}
		$r_menu   = array('menu' => $param['menu'], 'menu_text' => $param['menu_text']);
		$r_lang   = array_flip(get_lang());
		$l_code   = $Bbc->lang_array[lang_id()];
		$used     = array();
		$q        = "SELECT id FROM bbc_menu_cat ORDER BY orderby ASC";
		$r_cat_id = $db->getCol($q);
		$q        = "SELECT id FROM bbc_menu WHERE module_id=".$mod['id'];
		$menu_ids = $db->getCol($q);
		menu_delete($menu_ids);
		insertMenuRecure($r_menu);
		foreach($r_menu['menu'] AS $id => $menu)
		{
			if(!in_array($id, $used))
			{
				insertMenuRecure($r_menu, 0, $menu['par_id']);
			}
		}

		// PARSING MODULE DIRECTORY
		$r_dir = $param['directory'];
		$_path = $_path = _ROOT.'images/modules/'.$mod['name'].'/';
		if(is_array($r_dir) AND count($r_lang) > 0)
		{
			if(!is_dir($_path)) @mkdir($_path, 0777);
			foreach($r_dir AS $dir){
				if(!is_dir($_path.$dir.'/')) @mkdir($_path.$dir.'/', 0777);
			}
		}

		// PARSING MODULE LANGUAGE
		$r_lang = $param['language'];
		if(is_array($r_lang) AND count($r_lang) > 0)
		{
			// DELETE AVAILABLE LANGUAGE
			$q = "SELECT id FROM bbc_lang_code WHERE module_id=".$mod['id'];
			$code_ids = $db->getCol($q);
			if(count($code_ids) > 0)
			{
				$r = array_chunk($code_ids, 10);
				foreach ($r as $l)
				{
					$db->Execute("DELETE FROM bbc_lang_text WHERE code_id IN(".implode(',', $l).")");
				}
				$db->Execute("DELETE FROM bbc_lang_code WHERE module_id=".$mod['id']);
			}

			$lang_ref = array_flip(get_lang());
			foreach($r_lang AS $code => $dt)
			{
				$q = "INSERT INTO bbc_lang_code SET code='".addslashes($code)."', module_id=".$mod['id'];
				$db->Execute($q);
				$code_id = $db->Insert_ID();
				foreach($lang_ref AS $lang_code => $lang_id) {
					$q = "INSERT INTO bbc_lang_text SET code_id='{$code_id}', lang_id='{$lang_id}', content='".@$dt[$lang_code]."'";
					$db->Execute($q);
				}
			}
		}
		echo msg('Parameter of this module has been replaced', 'success');
	}else{
		echo msg('Failed to replace module parameter', 'danger');
	}
}else echo msg('Please upload file .json format to replace current parameter', 'danger');