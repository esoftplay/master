<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function tools_template_list()
{
	_func('path');
	$r = path_list(_ROOT.'templates/');
	$output = array();
	foreach($r AS $dir)
	{
		if(!preg_match('/admin/i', $dir))
		{
			if(is_file(_ROOT.'templates/'.$dir.'/index.php'))
			{
				$output[] = $dir;
			}
		}
	}
	return $output;
}

function tools_template_insert($r_temp_dir = array(), $r_temp_db = array())
{
	global $db;
	foreach((array)$r_temp_dir AS $dt)
	{
		if(!in_array($dt, $r_temp_db))
		{
			$q = "INSERT INTO bbc_template SET name='$dt', installed=NOW()";
			$db->Execute($q);
			@chmod(_ROOT.'templates/'.$dt.'/css/style.css', 0777);
		}
	}
}

function tools_template_delete($r_temp_del = array())
{
	if(!empty($r_temp_del))
	{
		global $db, $sys;
		$q = "DELETE FROM bbc_template WHERE id IN(".implode(',', $r_temp_del).")";
		$db->Execute($q);
		$q = "DELETE FROM bbc_block_theme WHERE template_id IN(".implode(',', $r_temp_del).")";
		$db->Execute($q);
		$q = "SELECT id FROM bbc_block WHERE template_id IN(".implode(',', $r_temp_del).")";
		$ids = $db->getCol($q);
		if($ids)
		{
			$q = "DELETE FROM bbc_block WHERE id IN(".implode(',', $ids).")";
			$db->Execute($q);
			$q = "DELETE FROM bbc_block_text WHERE block_id IN(".implode(',', $ids).")";
			$db->Execute($q);
		}
		$sys->clean_cache();
	}
}
function tools_template_uninstall($r_temp_del = array())
{
	if(!empty($r_temp_del))
	{
		global $db;
		_func('path');
		$path = _ROOT.'templates/';
		$q = "SELECT name FROM bbc_template WHERE id IN(".implode(',', $r_temp_del).")";
		$r = $db->getCol($q);
		foreach($r AS $name)
		{
			if(is_dir($path.$name))
			{
				tools_delete($path.$name);
			}
		}
		tools_template_delete($r_temp_del);
	}
}
function tools_template_install($id)
{
	$do_delete = true;
	if($id > 0)
	{
		global $db, $sys;
		_func('path');
		$path			= _CACHE;
		$temp_path= _ROOT.'templates/';
		$target		= $path.'template';
		$q = "SELECT * FROM bbc_template WHERE id=$id";
		$data = $db->getRow($q);
		if($db->Affected_rows())
		{
			if(is_file($path.$data['name']))
			{
				if(!is_dir($target)) path_create($target);
				$unzip		= _class('unzip');
				$unzip->setFileName($path.$data['name']);
#				$unzip->debug=true;
#				$unzip->getList();
				$unzip->unzipAll($target);
				$unzip->close();
				if(is_file($target.'/params.cfg'))
				{
					$r = path_list($target);
					$temp_name = '';
					foreach($r AS $d)
					{
						if(is_dir($target.'/'.$d)){$temp_name = $d; }
					}
					tools_move($target.'/'.$temp_name, $temp_path.$temp_name);

					/*====================================================
					 * INSERT PARAMETERS...
					 *==================================================*/
					$db->Execute("DELETE FROM bbc_block_theme WHERE template_id=$id");
					$del_ids = $db->getCol("SELECT id FROM bbc_block WHERE template_id=$id");$del_ids[] = 0;
					$db->Execute("DELETE FROM bbc_block WHERE template_id=$id");
					$db->Execute("DELETE FROM bbc_block_text WHERE block_id IN(".implode(',', $del_ids).")");

					$theme_ids = $block_ids = $block_ref_ids = $position_ids = $lang_ids = array();
					$block_ref_ids = $db->getAssoc("SELECT name, id FROM bbc_block_ref");
					$position_ids  = $db->getAssoc("SELECT name, id FROM bbc_block_position");
					$lang_ids      = $db->getAssoc("SELECT LOWER(code) AS code, id FROM bbc_lang");

					if(file_exists($target.'/params.json'))
					{
						$params = addslashes_r(json_decode(file_read($target.'/params.json'), 1));
					}else{
						$params = addslashes_r(urldecode_r(unserialize(file_read($target.'/params.cfg'))));
					}
					$params['block'] = $r['block'];
					// INSERT THEME...
					foreach((array)$params['theme'] AS $d)
					{
						$q = "INSERT INTO bbc_block_theme
						    SET template_id=$id
						    , name     = '".$d['name']."'
						    , content  = '".$d['content']."'
						    , active   = '".$d['active']."'
						    ";
						$db->Execute($q);
						$theme_ids[$d['id']] = $db->Insert_ID();
					}
					// INSERT BLOCK...
					foreach((array)$params['block'] AS $i => $d)
					{
						$d = addslashes_r(urldecode_r($d));
						if(isset($block_ref_ids[$d['block_name']]) && isset($position_ids[$d['position_name']]))
						{
							$block_id        	= $d['id'];
							$d['template_id'] = $id;
							$d['block_ref_id']= $block_ref_ids[$d['block_name']];
							$d['position_id'] = $position_ids[$d['position_name']];
							$d['theme_id']    = $theme_ids[$d['theme_id']];
							$d['config']    	= addslashes($params['block'][$i]['config']);
							unset($d['id'], $d['block_name'], $d['position_name']);
							$sql = array();
							foreach($d AS $field => $value) {
								$sql[] = "`$field` = '$value'";
							}
							$q = "INSERT INTO bbc_block SET ".implode(', ', $sql);
							$db->Execute($q);
							$block_ids[$block_id] = $db->Insert_ID();
						}
					}
					// INSERT BLOCK TEXT...
					foreach((array)$params['text'] AS $i => $d)
					{
						if(isset($block_ids[$i]))
						{
							foreach((array)$d AS $c => $t)
							{
								if(isset($lang_ids[$c]))
								{
									$q = "INSERT INTO bbc_block_text
									    SET title  = '".$t."'
									    , block_id = '".$block_ids[$i]."'
									    , lang_id  = '".$lang_ids[$c]."'
									    ";
									$db->Execute($q);
								}
							}
						}
					}
					$q = "UPDATE bbc_template SET name='$temp_name', installed=NOW(), syncron_to=0, last_copy_from=0 WHERE id=$id";
					$db->Execute($q);
					$do_delete = false;
				}
				tools_delete($target);
			}
		}
		$sys->clean_cache();
	}
	if($do_delete)
	{
		$q = "DELETE FROM bbc_template WHERE id=$id";
		$db->Execute($q);
	}
}
