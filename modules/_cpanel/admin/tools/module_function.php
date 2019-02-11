<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function tools_module_list()
{
	_func('path');
	$r = path_list(_ROOT.'modules');
	$output = array();
	foreach($r AS $dir)
	{
		if(is_file(_ROOT.'modules/'.$dir.'/_switch.php'))
		{
			$output[] = $dir;
		}
	}
	return $output;
}

function tools_module_delete($r_module_del = array())
{
	if(!empty($r_module_del))
	{
		global $db, $sys;
		$q = "SELECT id, name FROM bbc_module WHERE id IN(".implode(',', $r_module_del).")";
		$r_module_delete = $db->getAssoc($q);

		$q = "DELETE FROM bbc_module WHERE id IN(".implode(',', $r_module_del).")";
		$db->Execute($q);

		// DELETE MENU
		$q = "SELECT id FROM bbc_menu WHERE module_id IN(".implode(',', $r_module_del).")";
		$menu_ids = $db->getCol($q);
		if(!empty($menu_ids))
		{
			$q = "SELECT * FROM bbc_user_group";
			$r = $db->getAll($q);
			foreach((array)$r AS $dt)
			{
				$menu_news = array();
				$menus = repairExplode($dt['menus']);
				foreach((array)$menus AS $id)
				{
					if(!in_array($id, $menu_ids))	$menu_news[] = $id;
				}
				$q = "UPDATE bbc_user_group SET menus='".repairImplode($menu_news)."' WHERE id=".$dt['id'];
				$db->Execute($q);
			}
			$q = "DELETE FROM bbc_menu WHERE id IN (".implode(',', $menu_ids).")";
			$db->Execute($q);
			$q = "DELETE FROM bbc_menu_text WHERE menu_id IN (".implode(',', $menu_ids).")";
			$db->Execute($q);
		}

		// DELETE CONFIG
		$q = "DELETE FROM bbc_config WHERE module_id IN(".implode(',', $r_module_del).")";
		$db->Execute($q);

		// DELETE LANGUAGE
		$q = "SELECT id FROM bbc_lang_code WHERE module_id IN(".implode(',', $r_module_del).")";
		$lang_ids = $db->getCol($q);
		if(count($lang_ids) > 0)
		{
			$q = "DELETE FROM bbc_lang_text WHERE code_id IN(".implode(',', $lang_ids).")";
			$db->Execute($q);
			$q = "DELETE FROM bbc_lang_code WHERE module_id IN(".implode(',', $r_module_del).")";
			$db->Execute($q);
		}

		// DELETE EMAIL TEMPLATE
		$q = "SELECT id FROM bbc_email WHERE module_id IN(".implode(',', $r_module_del).")";
		$is= $db->getCol($q);
		if(count($is) > 0)
		{
			$q = "DELETE FROM bbc_email WHERE module_id IN(".implode(',', $r_module_del).")";
			$db->Execute($q);
			$q = "DELETE FROM bbc_email_text WHERE email_id IN(".implode(',', $is).")";
			$db->Execute($q);
		}

		// DELETE ORDER AND BILLING..
		$q = "SELECT id FROM bbc_order WHERE module_id IN(".implode(',', $r_module_del).")";
		$oids = $db->getCol($q);
		if(count($oids) > 0)
		{
			$q = "DELETE FROM bbc_order WHERE module_id IN(".implode(',', $r_module_del).")";
			$db->Execute($q);
			$q = "DELETE FROM bbc_order_billing WHERE order_id IN (".implode(',', $oids).")";
			$db->Execute($q);
		}

		// DROP TABLE IN DATABASE..
		$q = "SHOW TABLES";
		$r = $db->getCol($q);
		$r_db_tbl_del = array();
		foreach((array)$r AS $tbl)
		{
			foreach((array)$r_module_delete AS $module)
			{
				if(preg_match('~^'.$module.'_?~is', $tbl))
				{
					$r_db_tbl_del[] = $tbl;
				}
			}
		}
		if($r_db_tbl_del)
		{
			$q = "DROP TABLE `".implode('`, `', $r_db_tbl_del)."`";
			$db->Execute($q);
		}
		$sys->clean_cache();
	}
}

function tools_module_uninstall($r_module_del = array())
{
	if(!empty($r_module_del))
	{
		global $db, $sys;
		_func('path');
		$r_block = array();
		$block = _ROOT.'blocks/';
		$r = path_list($block);
		foreach((array)$r AS $d)
		{
			if(is_file($block.$d.'/_switch.php'))
			{
				$r_block[] = $d;
			}
		}
		$path = _ROOT.'modules/';
		$q = "SELECT name FROM bbc_module WHERE id IN(".implode(',', $r_module_del).")";
		$r = $db->getCol($q);
		foreach((array)$r AS $name)
		{
			if(is_dir($path.$name))
			{
				tools_delete($path.$name, true);
				$bids = array();
				foreach((array)$r_block AS $b)
				{
					if(preg_match('~^'.$name.'_?~is', $b))
					{
						tools_delete($block.$b, true);
						$bids[] = intval($db->getOne("SELECT id FROM bbc_block_ref WHERE `name`='$b'"));
					}
				}
				if(count($bids) > 0)
				{
					$db->Execute("DELETE FROM bbc_block_ref WHERE id IN(".implode(',', $bids).")");
					$bids2 = $db->getCol("SELECT id FROM bbc_block WHERE block_ref_id IN(".implode(',', $bids).")");
					if(count($bids2) > 0)
					{
						$db->Execute("DELETE FROM bbc_block WHERE block_ref_id IN(".implode(',', $bids).")");
						$db->Execute("DELETE FROM bbc_block_text WHERE block_id IN(".implode(',', $bids2).")");
					}
				}
			}
			tools_delete(_ROOT.'images/modules/'.$name);
		}
		tools_module_delete($r_module_del);
	}
}

function tools_module_insert($r_module_dir = array(), $r_module_db = array())
{
	global $db, $sys;
	foreach((array)$r_module_dir AS $dt)
	{
		if(!in_array($dt, $r_module_db))
		{
			$q = "INSERT INTO bbc_module SET name='$dt', created=NOW(), protected=0, allow_group=',all,', active=1";
			$db->Execute($q);
		}
	}
	$sys->clean_cache();
}

function tools_module_install($id)
{
	$do_delete = true;
	if($id > 0)
	{
		global $db, $Bbc, $sys;
		_func('path');
		$path    = _CACHE;
		$modules = _ROOT.'modules/';
		$blocks  = _ROOT.'blocks/';
		$uploads = _ROOT.'images/modules/';
		$target  = $path.'modules';
		$q = "SELECT * FROM bbc_module WHERE id=$id";
		$data = $db->getRow($q);
		if($db->Affected_rows())
		{
			if(is_file($path.$data['name']))
			{
				if(!is_dir($target)) path_create($target);
				$unzip	= _class('unzip');
				$unzip->setFileName($path.$data['name']);
				$unzip->unzipAll($target);
				$unzip->close();
				$r = path_list($target);
				if(file_exists($target.'/params.json'))
				{
					$params = addslashes_r(json_decode(file_read($target.'/params.json'), 1));
				}else{
					$params = addslashes_r(urldecode_r(unserialize(file_read($target.'/params.cfg'))));
				}
				if(is_dir($target.'/'.$params['data']['name']))
				{
					$do_delete = false;
					$module_name = $params['data']['name'];
					tools_move($target.'/'.$module_name, $modules.$module_name);
					tools_move($target.'/uploads/'.$module_name, $uploads.$module_name);
					$b = path_list($target.'/blocks/');
					foreach((array)$b AS $bn)
					{
						tools_move($target.'/blocks/'.$bn, $blocks.$bn);
						$db->Execute("INSERT INTO bbc_block_ref SET `name`='$bn'");
					}
					$lang_ref = $db->getAssoc("SELECT LOWER(code), id FROM bbc_lang");

					/*=================================
					 * PARSING DATA module...
					 *================================*/
					$fields = array();
					foreach((array)$params['data'] AS $f => $v)
					{
						if($f != 'id')
						{
							$fields[] = "`$f`='$v'";
						}
					}
					$q = "UPDATE bbc_module SET ".implode(',', $fields)." WHERE id=$id";
					$db->Execute($q);

					/*=================================
					 * PARSING module MENU...
					 *================================*/
					$r_menu		= $params['menu'];
					$Bbc->used	= array();
					$menu_ids = $db->getCol("SELECT id FROM bbc_menu WHERE module_id=$id");
					if(count($menu_ids) > 0)
					{
						$q="SELECT id, menus FROM bbc_user_group";
						$groups = $db->getAll($q);
						foreach($groups AS $group)
						{
							$arr = repairExplode($group['menus']);
							$menus = array();
							foreach((array)$arr AS $menu_id)
							{
								if(!in_array($menu_id, $menu_ids))
								{
									$menus[] = $menu_id;
								}
							}
							$q="UPDATE bbc_user_group SET menus='".repairImplode($menus)."'
									WHERE id=".$group['id'];
							$db->Execute($q);
						}
						$q="DELETE FROM bbc_menu WHERE module_id=$id";
						$db->Execute($q);
						$q="DELETE FROM bbc_menu_text WHERE menu_id IN(".implode(',', $menu_ids).")";
						$db->Execute($q);
					}
					$q = "SELECT id FROM bbc_menu_cat ORDER BY orderby ASC";
					$r_cat				= $db->getCol($q);
					$def_lang_code= array_search(lang_id(), $lang_ref);
					_func('menu');
					$n_id= array();
					foreach($r_menu AS $i => $m)
					{
						if(!empty($m['id']))
						{
							$n_par_id = @intval($n_id[$m['is_admin']][$m['par_id']]);
							$q ="SELECT COUNT(*) FROM bbc_menu WHERE par_id=".$n_par_id." AND is_admin=".$m['is_admin'];
							$orderby = $db->getOne($q)+1;
							if($m['is_admin'])
							{
								$m['seo']		= '';
								$m['cat_id']= 1;
							} else {
								$m['seo']		= menu_seo($m['seo'], $r_menu['title'][$m['id']][$def_lang_code]);
								$m['cat_id']= in_array($m['cat_id'], $r_cat) ? $m['cat_id'] : 1;
							}
							$q="INSERT INTO bbc_menu
									SET seo    = '".$m['seo']."'
									, module_id= '".$id."'
									, par_id   = '".$n_par_id."'
									, link     = '".$m['link']."'
									, orderby  = '".$orderby."'
									, cat_id   = '".$m['cat_id']."'
									, protected= '".$m['protected']."'
									, is_admin = '".$m['is_admin']."'
									, active   = '".$m['active']."'
							"; $db->Execute($q);
							$n_id[$m['is_admin']][$m['id']] = $db->Insert_ID();
							$is_insert			= 0;
							foreach((array)$r_menu['title'][$m['id']] AS $lang_code => $title)
							{
								if(isset($lang_ref[$lang_code]))
								{
									$q = "INSERT INTO bbc_menu_text SET menu_id=".$n_id[$m['is_admin']][$m['id']].", title='$title', lang_id=".$lang_ref[$lang_code];
									$db->Execute($q);
									$is_insert = 1;
								}
							}
							if(!$is_insert)
							{
								$q = "INSERT INTO bbc_menu_text SET menu_id=".$n_id[$m['is_admin']][$m['id']].", title='$title', lang_id=".lang_id();
								$db->Execute($q);
							}
						}
					}

					/*=================================
					 * PARSING CONFIG...
					 *================================*/
					$r_config = @$params['config'];
					if(is_array($r_config) AND count($r_config) > 0)
					{
						$q = "DELETE FROM bbc_config WHERE module_id=$id";
						$db->Execute($q);
						foreach($r_config AS $key => $value)
						{
							$q = "INSERT INTO bbc_config
										SET name	= '".$key."'
										,	params	= '".$value."'
										,	module_id=$id";
							$db->Execute($q);
						}
					}

					/*=================================
					 * PARSING LANGUAGE...
					 *================================*/
					$r_lang = @$params['language'];
					if(is_array($r_lang) AND count($r_lang) > 0)
					{
						$q = "SELECT id FROM bbc_lang_code WHERE module_id=$id";
						$ids = array_merge(array(0), $db->getCol($q));
						$q="DELETE FROM bbc_lang_text WHERE code_id IN(".implode(',', $ids).")";
						$db->Execute($q);
						$q="DELETE FROM bbc_lang_code WHERE module_id=$id";
						$db->Execute($q);
						foreach($r_lang AS $lang => $d)
						{
							$q = "INSERT INTO bbc_lang_code
									SET code    = '".strtolower($lang)."'
									,	module_id = $id	";
							$db->Execute($q);
							$code_id = $db->Insert_ID();
							foreach((array)$d AS $code => $content)
							{
								if(isset($lang_ref[$code]))
								{
									$q="INSERT INTO bbc_lang_text
											SET lang_id= '".$lang_ref[$code]."'
											,	code_id	 = '".$code_id."'
											,	content  = '".$content."'
											";
									$db->Execute($q);
								}
							}
						}
					}

					/*=================================
					 * PARSING EMAIL TEMPLATE...
					 *================================*/
					$r_email = @$params['email'];
					if(is_array($r_email) AND count($r_email) > 0)
					{
						$q="SELECT id FROM bbc_email WHERE module_id=$id";
						$ids = $db->getCol($q);$ids[]=0;
						$q="DELETE FROM bbc_email WHERE module_id=$id";
						$db->Execute($q);
						$q="DELETE FROM bbc_email_text WHERE email_id IN (".implode(',', $ids).")";
						$db->Execute($q);
						$new_ids = array();
						foreach($r_email AS $d)
						{
							$d['module_id'] = $id;
							$f = array();
							foreach($d AS $f1 => $f2)
							{
								if($f1 != 'id')
								{
									$f[] = "`$f1`='$f2'";
								}
							}
							$f = implode(',', $f);
							$q="INSERT INTO bbc_email
									SET $f";
							$db->Execute($q);
							$new_ids[$d['id']] = $db->Insert_ID();
						}
						foreach((array)$params['email_text'] AS $old_id => $email)
						{
							foreach((array)$email AS $code => $dt)
							{
								if(isset($lang_ref[$code]))
								{
									$q = "INSERT INTO bbc_email_text
										SET email_id= '".$new_ids[$old_id]."'
										,	lang_id		= '".$lang_ref[$code]."'
										,	subject		= '".$dt['subject']."'
										,	content		= '".$dt['content']."'";
									$db->Execute($q);
								}
							}
						}
					}

					/*=================================
					 * INSERT DATABASE...
					 *================================*/
					$text = file_read($target.'/database.sql');
					$text = str_replace(";\nCREATE TABLE `", ";\n\n-- \nCREATE TABLE `", $text);
					$r_sql = explode(";\n\n-- ", $text);
					foreach((array)$r_sql AS $q)
					{
						$q = trim(preg_replace("~\n?\s{0,}\-{2,}.*?\n~", "", $q));
						if(!empty($q))
						{
							$db->Execute($q);
						}
					}
				}
			}
		}
		$sys->clean_cache();
	}
	if($do_delete)
	{
		$q = "DELETE FROM bbc_module WHERE id=$id";
		$db->Execute($q);
	}
}
