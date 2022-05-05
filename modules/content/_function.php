<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function content_fetch($id, $use_text = true)
{
	$output = array();
	if(!is_numeric($id) || $id == 0) return $output;
	global $db, $Bbc;
	$continue = false;
	if(is_file(content_prune_path().$id.'.cfg'))
	{
		$output   = content_file_read($id, 'main');
		$continue = true;
		if (!isset($output['kind_id']))
		{
			$output = array_merge(array(
				'par_id'        => 0,
				'type_id'       => 0,
				'kind_id'       => 0,
				'file'          => '',
				'file_url'      => '',
				'file_format'   => '',
				'file_type'     => 0,
				'file_register' => 0,
				'file_hit'      => 0,
				'file_hit_time' => '0000-00-00 00:00:00',
				'file_hit_ip'   => '',
				'video'         => '',
				'audio'         => '',
				'image'         => '',
				'caption'       => '',
				'images'        => '',
				'privilege'     => ',all,',
				), $output);
		}
		$output['prune'] = 1;
	}else{
		$q      = "SELECT * FROM `bbc_content` WHERE `id`={$id}";
		$output = $db->getRow($q);
		if (!empty($output) && !empty($output['id']) && !isset($output['kind_id']))
		{
			if ($db->getOne("SELECT COUNT(1) FROM `bbc_content` WHERE `id`={$id}") == 1)
			{
				// redirect('index.php?mod=user.repair&id=content&redirect='.urlencode(seo_url()));
			}
		}
		$output['prune'] = 0;
		$continue        = !empty($output['id']);
	}
	if($continue)
	{
		if (!empty($output['is_config']) && !empty($output['config']))
		{
			$output['config'] = config_decode($output['config']);
		}
		if (empty($output['config']))
		{
			$output['config'] = content_type($output['type_id'], 'detail');
		}
		if($output['prune'])
		{
			$output['cat_ids'] = content_file_read($id, 'cat');
		}else{
			$q = "SELECT cat_id FROM bbc_content_category WHERE content_id=$id";
			$output['cat_ids'] = $db->getCol($q);
		}
		if($use_text)
		{
			if(!empty($output['cat_ids']))
			{
				$q = "SELECT cat_id, title FROM bbc_content_cat_text WHERE
					cat_id IN (".implode(',', $output['cat_ids']).") AND lang_id=".lang_id();
				$output['cat_names'] = $db->getAssoc($q);
			}else{
				$output['cat_names'] = array();
			}
			if($output['prune'])
			{
				$output = array_merge($output, content_file_read($id, 'text', lang_id()));
			}else{
				$q = "SELECT * FROM bbc_content_text WHERE content_id=$id AND lang_id=".lang_id();
				$output = array_merge($output, (array)$db->getRow($q));
			}
		}
	}
	return $output;
}

function content_fetch_admin($id)
{
	$output = array();
	if(!is_numeric($id) || $id == 0) return $output;
	global $db, $Bbc;
	$continue = false;
	if(is_file(content_prune_path().$id.'.cfg'))
	{
		$output   = content_file_read($id, 'main');
		$continue = true;
		if (!isset($output['kind_id']))
		{
			$output = array_merge(array(
				'par_id'        => 0,
				'type_id'       => 0,
				'kind_id'       => 0,
				'file'          => '',
				'file_url'      => '',
				'file_format'   => '',
				'file_type'     => 0,
				'file_register' => 0,
				'file_hit'      => 0,
				'file_hit_time' => '0000-00-00 00:00:00',
				'file_hit_ip'   => '',
				'video'         => '',
				'audio'         => '',
				'image'         => '',
				'caption'       => '',
				'images'        => '',
				'privilege'     => ',all,',
				), $output);
		}
		$output['prune'] = 1;
	}else{
		$q      = "SELECT * FROM `bbc_content` WHERE `id`={$id}";
		$output = $db->getRow($q);
		if (!empty($output) && !empty($output['id']) && !isset($output['kind_id']))
		{
			if ($db->getOne("SELECT COUNT(1) FROM `bbc_content` WHERE `id`={$id}") == 1)
			{
				// redirect('index.php?mod=user.repair&id=content&redirect='.urlencode(seo_url()));
			}
		}
		$output['prune'] = 0;
		$continue        = !empty($output['id']);
	}
	if($continue)
	{
		if (!empty($output['is_config']) && !empty($output['config']))
		{
			$output['config'] = config_decode($output['config']);
		}
		if (empty($output['config']))
		{
			$output['config'] = content_type($output['type_id'], 'detail');
		}
		if($output['prune'])
		{
			$output['cat_ids'] = content_file_read($id, 'cat');
			$r = lang_assoc();
			foreach($r AS $d)
			{
				$output['text'][$d['id']] = content_file_read($id, 'text', $d['id']);
			}
		}else{
			$q = "SELECT cat_id FROM bbc_content_category WHERE content_id=$id";
			$output['cat_ids'] = $db->getCol($q);
			$q = "SELECT lang_id, title, description, keyword, tags, intro, content
						FROM bbc_content_text WHERE content_id=$id";
			$output['text'] = $db->getAssoc($q);
		}
	}
	return $output;
}

function content_fetch_menu($i = 0, $type = 'content',$_URL='')
{
	global $Bbc, $sys,$db;
	if(empty($Bbc->menu_content))
	{
		$_URL = $_URL ? $_URL : _URL;
		$Bbc->menu_content = array('type' => array(),'cat' => array(),'content' => array());
		if(_ADMIN)
		{
			$q="SELECT * FROM bbc_menu AS m LEFT JOIN bbc_menu_text AS t
					ON (m.id=t.menu_id AND lang_id=".lang_id().")
					WHERE is_admin=0 AND active=1 ORDER BY cat_id, par_id, orderby";
			$Bbc->menu->all_array = $arr = $db->cacheGetAll($q);
		}else{
			$arr = $sys->menu_get_all();
		}
		foreach($arr AS $d)
		{
			if($d['is_content'])
			{
				$Bbc->menu_content['content'][$d['content_id']] = $_URL.$d['seo'].'.html';
			}else
			if($d['is_content_cat'])
			{
				$Bbc->menu_content['cat'][$d['content_cat_id']] = $_URL.$d['seo'].'.html';
			}else
			if(preg_match('~index\.php\?mod=content\.type\&id=[0-9]+~', $d['link']))
			{
				preg_match('~\&id=([0-9]+)~', $d['link'], $match);
				$Bbc->menu_content['type'][$match[1]] = $_URL.$d['seo'].'.html';
			}
		}
	}
	if(empty($Bbc->menu_content[$type][$i])) return false;
	else return $Bbc->menu_content[$type][$i];
}

function content_search_link_list($data)
{
	$output = array(
		'title'			=> $data['title']
	,	'link'			=> content_link($data['id'], $data['title'])
	,	'description'	=> $data['description']
	);
	return $output;
}
function content_file_read($id, $act='main'/*main|cat|text*/, $lang_id = 0)
{
	global $Bbc;
	$output = array();
	$path		= content_prune_path();
	switch($act)
	{
		case 'main':
			$output = json_decode(file_read($path.$id.'.cfg'), 1);
		break;
		case 'cat':
			$output = json_decode(file_read($path.$id.'-cat.cfg'), 1);
		break;
		case 'text':
			if(!is_file($path.$id.'-'.$lang_id.'.cfg'))
			{
				$file = $path.$id.'-'.lang_id().'.cfg';
			}else{
				$file = $path.$id.'-'.$lang_id.'.cfg';
			}
			$output = json_decode(file_read($file), 1);
		break;
	}
	return $output;
}
function content_file_update($id, $arr)
{
	if(is_file(content_prune_path().$id.'.cfg'))
	{
		$r = array_merge(content_fetch($id, false), $arr);
		file_write(content_prune_path().$id.'.cfg', json_encode($r));
		return true;
	}
	return false;
}
function content_prune_path()
{
	$output = _ROOT.'images/modules/content/pruned/';
	if(!is_dir($output))
	{
		_func('path');
		path_create($output);
	}
	return $output;
}
function content_check($id)
{
	if(is_file(content_prune_path().$id.'.cfg'))
	{
		return $id;
	}
	global $db;
	$q = "SELECT id FROM `bbc_content` WHERE `id`=".intval($id);
	$output = intval($db->getOne($q));
	return $output;
}
function content_src($src, $is_imgsrc = false, $is_large_image = false)
{
	$output = '';
	$path   = 'images/modules/content/images/';
	if (is_url($src))
	{
		$output = $src;
	}else
	if (is_file(_ROOT.$src))
	{
		$output = _URL.$src;
	}else
	if ($is_large_image && is_file(_ROOT.$path.'p_'.$src))
	{
		$output = _URL.$path.'p_'.$src;
	}else
	if (is_file(_ROOT.$path.$src))
	{
		$output = _URL.$path.$src;
	}else{
		$p = 'images/modules/content/'.get_config('content', 'manage', 'images');
		if (is_file(_ROOT.$p))
		{
			$output = _URL.$p;
		}
	}
	if ($is_imgsrc)
	{
		$tag = is_string($is_imgsrc) ? $is_imgsrc : ' class="img-thumbnail img-responsive img-fluid"';
		$output = image($output, '', $tag);
	}
	return $output;
}
function content_link($id, $title='', $is_tag = false, $_URL='')
{
	$_URL = $_URL ? $_URL : _URL;
	if($is_tag)
	{
		return $_URL.menu_save($title.'_'.$id).'.htm';
	}
	$output = content_fetch_menu($id, 'content', $_URL);
	if(!empty($output)) return $output;
	if(empty($title))
	{
		$d = content_fetch($id, true);
		$title = @$d['title'];
	}
	$output = $_URL.menu_save($title.'_'.$id).'.htm';
	return $output;
}
function content_date($date, $default = '')
{
	$date = strtotime($date);
	if ($date > 0)
	{
		$output = date( get_config(0, 'rules', 'content_date'), $date);
	}else{
		$output = $default;
	}
	return $output;
}
function content_hit($id)
{
	global $db;
	$r = isset($_SESSION['content_hit']) ? $_SESSION['content_hit'] : array();
	$r = $r ? $r : array();
	$id= intval($id);
	if(!in_array($id, $r))
	{
		$q = "UPDATE bbc_content SET hits=(hits+1), last_hits=NOW() WHERE id=$id";
		if($db->Execute($q))
		{
			$r[] = $id;
			$_SESSION['content_hit'] = array_unique($r);
		}
	}else{
		return false;
	}
}
function content_category($ids, $is_link = true)
{
	global $db, $Bbc;
	$output = array();
	if(empty($ids)) return $output;
	$ids = is_array($ids) ? $ids : array($ids);
	if(!isset($Bbc->content_category_array))
	{
		$q="SELECT c.`id`, t.`title` FROM bbc_content_cat AS c LEFT JOIN bbc_content_cat_text AS t
			ON(c.`id`=t.`cat_id` AND t.`lang_id`=".lang_id().")";
		$Bbc->content_category_array = $db->cacheGetAssoc($q);
	}
	$r_cat = $Bbc->content_category_array;
	$q = "SELECT * FROM bbc_content_category WHERE content_id IN (".implode(',', $ids).")";
	$r = $db->cacheGetAll($q);
	foreach($r AS $dt)
	{
		$title = $r_cat[$dt['cat_id']];
		$output[$dt['content_id']][] = ($is_link) ? '<a href="'.content_cat_link($dt['cat_id'], $title).'" title="'.$title.'" style="padding: 0 5px;">'.$title.'</a>' : $title;
	}
	if (get_config('content', 'manage', 'webtype') == '1')
	{
		$q = "SELECT t.`id`, t.`title`, l.`content_id` FROM bbc_content_tag AS t
		LEFT JOIN bbc_content_tag_list AS l ON (t.`id`=l.`tag_id`)
		WHERE l.`content_id` IN (".implode(',', $ids).")";
		$r = $db->cacheGetAll($q);
		foreach ($r as $t)
		{
			$output[$t['content_id']][] = ($is_link) ? '<a href="'.content_tag_link($t['id'], $t['title']).'" title="'.$t['title'].'" style="padding: 0 5px;">'.$t['title'].'</a>' : $t['title'];
		}
	}
	if(count($ids) == 1
		&& (isset($dt['content_id'])
			||isset($t['content_id']))
		)
	{
		$content_id = isset($dt['content_id']) ? $dt['content_id'] : $t['content_id'];
		$output     = !empty($output[$content_id]) ? $output[$content_id] : array();
	}
	return $output;
}
function content_privilege($data, $return_url = '')
{
	if (!empty($data['id']))
	{
		global $user, $sys;
		$ok = true;
		if (!empty($data['privilege']))
		{
			$allowed = repairExplode($data['privilege']);
			$doLogin = false;
			$ok      = false;
			if (in_array('all', $allowed))
			{
				$ok = true;
			}else{
				$doLogin = $user->id ? false : true;
				foreach((array)@$user->group_ids AS $i)
				{
					if (in_array($i, $allowed))
					{
						$ok = true;
						break;
					}
				}
			}
		}
		if (!$ok)
		{
			if ($doLogin)
			{
				if (empty($return_url))
				{
					$return_url = seo_uri();
				}
				redirect('index.php?mod=user.login&return='.urlencode($return_url));
			}else{
				$sys->denied();
			}
		}
	}
}
function content_posted_permission()
{
	global $Bbc, $user;
	if(isset($Bbc->_content_posted_permission) && is_bool($Bbc->_content_posted_permission))
	{
		return $Bbc->_content_posted_permission;
	}
	$groups = get_config('content', 'entry', 'groups');
	$Bbc->_content_posted_permission = false;
	foreach((array)@$user->group_ids AS $id)
	{
		if(in_array($id, $groups))
		{
			$Bbc->_content_posted_permission = true;
			return $Bbc->_content_posted_permission;
		}
	}
	return $Bbc->_content_posted_permission;
}
function content_delete($ids = array())
{
	ids($ids);
	$output = array();
	if(!empty($ids))
	{
		global $db, $_CONFIG;
		$text = array();
		$q = "SELECT * FROM bbc_content_text WHERE content_id IN ({$ids})";
		$r = $db->getAll($q);
		if($db->Affected_rows())
		{
			$q = "SELECT cat_id, pruned, active FROM bbc_content_category WHERE content_id IN ({$ids})";
			$cat_ids = $db->getAssoc($q);
			foreach($r AS $data)
			{
				$text[$data['content_id']][$data['lang_id']] = $data;
			}
			$q = "SELECT * FROM bbc_content WHERE id IN ({$ids})";
			$contents = $db->getAll($q);
			foreach($contents AS $content)
			{
				$r = array(
					'content' => $content
				,	'text'		=> $text[$content['id']]
				,	'ids'			=> $cat_ids
				);
				if (config('manage', 'webtype') == '1')
				{
					$r['tags'] = $db->getCol("SELECT tag_id FROM bbc_content_tag_list WHERE content_id=".$content['id']);
				}
				$q = "INSERT INTO `bbc_content_trash`
					SET `title`	= '".addslashes(@$text[$content['id']][lang_id()]['title'])."'
					,	`image`		= '".$content['image']."'
					,	`content_id`= '".$content['id']."'
					,	`params`	= '".addslashes(json_encode($r))."'
					,	`trashed`	= NOW()
					,	`restore` = 0
				";
				$db->Execute($q);
				$output[] = $db->Insert_ID();
			}
			$q = "UPDATE `bbc_content` SET `par_id`=0 WHERE `par_id` IN ({$ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content` WHERE `id` IN ({$ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_text` WHERE `content_id` IN ({$ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_category` WHERE `content_id` IN ({$ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_related` WHERE `content_id` IN ({$ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_related` WHERE `related_id` IN ({$ids})";
			$db->Execute($q);
			if (config('manage', 'webtype') == '1')
			{
				$r    = $db->getCol("SELECT `tag_id` FROM `bbc_content_tag_list` WHERE `content_id` IN ({$ids})");
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
					$db->Execute("DELETE FROM `bbc_content_tag_list` WHERE `content_id` IN ({$ids})");
					foreach ($tags as $tag_id => $count)
					{
						$db->Execute("UPDATE `bbc_content_tag` SET `total`=(`total`-{$count}), `updated`=NOW() WHERE `id`={$tag_id}");
					}
				}
			}
			$q = "SELECT `id` FROM `bbc_menu` WHERE `content_id` IN ({$ids})";
			$menu_ids = $db->getCol($q);
			if(!empty($menu_ids))
			{
				menu_delete($menu_ids);
			}
		}
	}
	return $output;
}
function content_restore()
{
	global $db;
	$q = "SELECT `params` FROM `bbc_content_trash` WHERE `restore`=1";
	$params = $db->getCol($q);
	if(!empty($params))
	{
		$r_lang = $db->getCol("SELECT `id` FROM `bbc_lang` WHERE 1");
		$r_cat  = $db->getCol("SELECT id FROM bbc_content_cat WHERE 1");
		foreach($params AS $data)
		{
			$r = json_decode($data, 1);
			$fields = array();
			foreach($r['content'] AS $field => $value)
			{
				$fields[] = "`$field`='$value'";
			}
			$q = "INSERT INTO `bbc_content` SET ".implode(',', $fields);
			$db->Execute($q);
			foreach((array)$r['text'] AS $lang_id => $text)
			{
				if(in_array($lang_id, $r_lang))
				{
					$fields = array();
					foreach($text AS $field => $value)
					{
						$fields[] = "`$field`='$value'";
					}
					$q = "INSERT INTO `bbc_content_text` SET ".implode(',', $fields);
					$db->Execute($q);
				}
			}
			foreach((array)$r['ids'] AS $cat_id => $dt)
			{
				if(in_array($cat_id, $r_cat))
				{
					$q = "INSERT INTO bbc_content_category
						SET cat_id	= ".$cat_id."
						, content_id= ".$r['content']['id']."
						, pruned		= ".$dt['pruned']."
						, active		= ".$dt['active']."
						";
					$db->Execute($q);
				}
			}
			if (get_config('content', 'manage', 'webtype') == '1')
			{
				foreach((array)$r['tags'] AS $tag_id)
				{
					if ($db->getOne("SELECT 1 FROM `bbc_content_tag` WHERE `id`={$tag_id}"))
					{
						$db->Execute("INSERT INTO `bbc_content_tag_list` SET `tag_id`={$tag_id}, `content_id`=".$r['content']['id']);
						$db->Execute("UPDATE `bbc_content_tag` SET `total`=(`total`+1), `updated`=NOW() WHERE `id`={$tag_id}");
					}
				}
			}
		}
		$q = "DELETE FROM `bbc_content_trash` WHERE `restore`=1";
		$db->Execute($q);
	}
}
function content_trash_delete($ids = array())
{
	global $db;
	ids($ids);
	if(!empty($ids))
	{
		$_dir = _class('content')->img_path;
		$q = "SELECT content_id, image FROM bbc_content_trash WHERE id IN ({$ids})";
		$images = $db->getAssoc($q);
		if(!empty($images))
		{
			_func('path');
			$i_path = dirname($_dir).'/';
			$c_ids  = array();

			foreach($images AS $i => $image)
			{
				$c_ids[] = $i;
				path_delete($i_path.$i);
				if(is_file($_dir.$image))
				{
					@unlink($_dir.'p_'.$image);
					@unlink($_dir.$image);
				}
			}
			ids($c_ids);
			$q = "DELETE FROM `bbc_content_comment` WHERE `content_id` IN ({$c_ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_registrant` WHERE `content_id` IN ({$c_ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_related` WHERE `content_id` IN ({$c_ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_schedule` WHERE `content_id` IN ({$c_ids})";
			$db->Execute($q);
			$q = "DELETE FROM `bbc_content_trash` WHERE `id` IN ({$ids})";
			$db->Execute($q);
		}
	}
}
function content_rss($id = 0)
{
	global $db;
	$id = intval($id);
	if($id > 0) return content_cat_list($id, 0);
	$output = array(
	  'title'				=> config('site','title')
	 ,'description'	=> config('site','desc')
	 ,'keyword'			=> config('site','keyword')
	 ,'publish'			=> 1
	);
	$output['config'] = content_type(0, 'list');
	$q="SELECT * FROM bbc_content AS c
			LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND t.lang_id=".lang_id().")
			WHERE c.publish=1 ORDER BY id DESC LIMIT 0,".config('rules', 'content_rss');
	$output['list'] = $db->getAll($q);
	return $output;
}

function content_category_delete($ids = array())
{
	if(!empty($ids))
	{
		global $db;
		$ids  = content_category_recure($ids);
		$path = _ROOT.'images/modules/content/';
		ids($ids);
		$f = $db->getCol("SHOW COLUMNS FROM `bbc_content_cat`");
		if (in_array('image', $f))
		{
			$q = "SELECT `image` FROM `bbc_content_cat` WHERE id IN ({$ids})";
			$r = $db->getCol($q);
			foreach ($r as $img)
			{
				if (!empty($img))
				{
					if (file_exists($path.$img))
					{
						@unlink($path.$img);
					}
				}
			}
		}
		$q = "DELETE FROM bbc_content_cat WHERE id IN ({$ids})";
		$db->Execute($q);
		$q = "DELETE FROM bbc_content_cat_text WHERE cat_id IN ({$ids})";
		$db->Execute($q);
		$q = "DELETE FROM bbc_content_category WHERE cat_id IN ({$ids})";
		$db->Execute($q);
		$q = "SELECT id FROM bbc_menu WHERE content_cat_id IN ({$ids})";
		$menu_ids = $db->getCol($q);
		if(!empty($menu_ids))
		{
			menu_delete($menu_ids);
		}
		/* DELETE CONTENT AD IN ASSOCIATED CATEGORY */
		if($db->getCol("SHOW TABLES LIKE 'bbc_content_ad'"))
		{
			$ids = explode(',', $ids);
			if (in_array(0, $ids))
			{
				$r = array_keys($ids, 0);
				foreach ($r as $i)
				{
					unset($ids[$i]);
				}
			}
			ids($ids);
			$path = _ROOT.'images/modules/content/ads/';
			$q = "SELECT `image` FROM `bbc_content_ad` WHERE `cat_id` IN ({$ids})";
			$r = $db->getCol($q);
			foreach ($r as $f)
			{
				if (is_file($path.$f))
				{
					@unlink($path.$f);
				}
			}
			$db->Execute("DELETE FROM `bbc_content_ad` WHERE `cat_id` IN ({$ids})");
		}
		content_category_update();
		return true;
	}
	return false;
}
function content_category_update()
{
	return content_fcm(array(
			'data' => array(
				'type_id' => 2,
				'title'   => 'update menu',
				'url'     => _URL.'menu'
				)
			));
}
function content_fcm($fields)
{
	$output = '';
	if (defined('_FCM'))
	{
		$def = array(
			'to'   => '/topics/'.config('site', 'url'),
			'data' => array(
				'is_master' => 1,
				'type_id'   => '1',
				'title'     => '',
				'message'   => '',
				'url'       => ''
				),
			'content_available' => true,
			'delay_while_idle'  => true,
			'priority'          => 'high',
			'time_to_live'      => 864000 // 10 days
			);
		$fields = array_merge($def, $fields);
		if (!empty($fields['data']['url']))
		{
			$fields['data']['url']   = preg_replace('~://(www|api|data|m|mobile|new|test|[a-z]{2})\.~is', '://', $fields['data']['url']);
			$fields['data']['url']   = preg_replace('~://~is', '://data.', $fields['data']['url']);
		}
		$fields['data'] = array_merge($def['data'], $fields['data']);
		if (!empty($fields['data']['message']) && $fields['data']['type_id'] != 2)
		{
			$fields['notification'] = array(
				'title' => substr($fields['data']['title'], 0, 80),
				'body'  => substr($fields['data']['message'], 0, 141),
				'icon'  => 'ic_notification',
				);
			// unset($fields['data']['title'], $fields['data']['message']);
		}
		$ch = curl_init();
		// Set the URL, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: key=' . _FCM,
			'Content-Type: application/json'
			));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		curl_close($ch);
	}
	return $output;
}
function content_category_recure($ids = '', $output= array())
{
	if(!empty($ids))
	{
		global $db;
		$ids = is_array($ids) ? $ids : array($ids);
		$_ids= array();
		foreach($ids AS $id )
			if(is_numeric($id)){
				$output[] = $id;
				$_ids[]		= $id;
			}
		if(!empty($_ids))
		{
			$q = "SELECT id FROM bbc_content_cat WHERE par_id IN (".implode(',', $_ids).")";
			$r = $db->getCol($q);
			if($db->Affected_rows()) {
				$output = content_category_recure($r, $output);
			}
		}
	}
	return $output;
}
function content_cat_link($id, $title='', $_URL='')
{
	$output = content_fetch_menu($id, 'cat', $_URL);
	if(!empty($output)) return $output;
	global $db;
	if(empty($title))
	{
		$q = "SELECT `title` FROM `bbc_content_cat_text` WHERE `cat_id`=$id AND `lang_id`=".lang_id();
		$title =$db->getOne($q);
	}
	$_URL = $_URL ? $_URL : _URL;
	$output = $_URL.menu_save($title.'-'.$id).'.htm';
	return $output;
}
function content_cat_list($id, $page = 0, $config = array())
{
	$output = array();
	if(!is_numeric($id) || $id == 0) return $output;
	global $db;
	$q="SELECT * FROM bbc_content_cat AS c LEFT JOIN bbc_content_cat_text AS t
			ON (c.id=t.cat_id AND t.lang_id=".lang_id().") WHERE id=$id";
	$output = $db->getRow($q);
	if($db->Affected_rows())
	{
		if(empty($config))
		{
			if (!empty($output['is_config']) && !empty($output['config']))
			{
				$output['config'] = config_decode($output['config']);
			}
			if (empty($output['config']))
			{
				$output['config'] = content_type($output['type_id'], 'list');
			}
		}else{
			$output['config'] = $config;
		}
		$add_sql = '';
		if (!empty($config['add_sql']))
		{
			$add_sql = mysqli_real_escape_string($db->link, $config['add_sql']);
			if (!preg_match('~^(?:\s+)?and(?:\s+)?~is', $add_sql))
			{
				$add_sql = 'AND '.$add_sql;
			}
		}
		$start = intval($page) * $output['config']['tot_list'];
		$q="SELECT SQL_CALC_FOUND_ROWS c.*, t.title, t.intro, t.content FROM bbc_content_category AS cat
				LEFT JOIN bbc_content AS c ON (c.id=cat.content_id)
				LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND t.lang_id=".lang_id().")
				WHERE cat.cat_id={$id} AND c.publish=1 {$add_sql} ORDER BY id DESC LIMIT "
				. $start.", ".$output['config']['tot_list'];
		$output['list']       = $db->cacheGetAll($q);
		$output['total']      = $db->cacheGetOne("SELECT FOUND_ROWS(), $id AS id");
		$output['total_page'] = ceil($output['total'] / $output['config']['tot_list']);
		$output['link']       = content_cat_link($output['id'], $output['title']);
		$output['rss']        = site_url('index.php?mod=content.rss&id='.$output['id']);
	}
	return $output;
}
function content_kind($kind_id='none')
{
	$r = array(
		0 => 'article',
		1 => 'gallery',
		2 => 'download',
		3 => 'video',
		4 => 'audio',
		);
	if (is_numeric($kind_id))
	{
		return !empty($r[$kind_id]) ? $r[$kind_id] : $r[0];
	}else{
		return $r;
	}
}
function content_type($id=0, $type = '')
{
	global $db;
	$output = array();
	if($id > 0)
	{
		$q = "SELECT * FROM bbc_content_type WHERE id=$id";
		$output = $db->cacheGetRow($q);
		$output['detail']= config_decode($output['detail']);
		$output['list']  = config_decode($output['list']);
	}
	if(isset($output[$type])) return $output[$type];
	elseif(empty($output)){
		$output = get_config('content');
		return isset($output[$type]) ? $output[$type] : $output;
	}
	return $output;
}
function content_type_delete($ids = array())
{
	if($ids)
	{
		global $db;
		$ids = is_array($ids) ? $ids : array($ids);
		_func('menu');
		_func('path');
		$q = "SELECT menu_id FROM bbc_content_type WHERE id IN (".implode(',', $ids).")";
		$menu_ids = $db->getCol($q);
		$q = "DELETE FROM bbc_content_type WHERE id IN (".implode(',', $ids).")";
		$db->Execute($q);
		$q = "SELECT id FROM bbc_content_cat WHERE type_id IN (".implode(',', $ids).")";
		$cat_ids = $db->getCol($q);
		content_category_delete($cat_ids);
		$q = "SELECT id FROM bbc_content WHERE type_id IN (".implode(',', $ids).")";
		$content_ids = $db->getCol($q);
		content_delete($content_ids);
		menu_delete($menu_ids);
		content_type_refresh();
		return true;
	}
	return false;
}
function content_type_refresh()
{
	global $sys, $db;
	$c = $db->getOne("SELECT COUNT(1) FROM `bbc_content_type`");
	if ($c==1)
	{
		$data   = $db->getRow("SELECT * FROM `bbc_content_type` LIMIT 1");
		$detail = config_encode(json_decode($data['detail'], 1));
		$list   = config_encode(json_decode($data['list'], 1));
		$mod_id = $sys->get_module_id('content');
		$db->Execute("UPDATE `bbc_config` SET `params` = '{$detail}' WHERE `name`='detail' AND `module_id`={$mod_id}");
		$db->Execute("UPDATE `bbc_config` SET `params` = '{$list}' WHERE `name`='list' AND `module_id`={$mod_id}");
	}
	$sys->clean_cache();
}
function content_type_menu_create($id, $title, $link, $par_id = 0, $orderby = 2)
{
	if($id > 0)
	{
		global $db, $sys, $Bbc;
		$link = str_replace(_URL._ADMIN, '', $link);
		if(!$par_id)
		{
			$q="UPDATE bbc_menu SET orderby=3 WHERE is_admin=1 AND par_id=0 AND orderby=2";
			$db->Execute($q);
		}
		$q="INSERT INTO bbc_menu
				SET par_id			= '".$par_id."'
				, module_id     = '".$sys->module_id."'
				, seo						= ''
				, link					= '$link'
				, orderby				= $orderby
				, cat_id				= 0
				, is_content_cat= 0
				, content_cat_id= 0
				, protected			= 1
				, is_admin			= 1
				, active				= 1
		";
		if($db->Execute($q))
		{
			$menu_id = $db->Insert_ID();
			$q = "DELETE FROM bbc_menu_text WHERE menu_id=$menu_id";
			$db->Execute($q);
			$q = "INSERT INTO bbc_menu_text SET menu_id=$menu_id, title='$title', lang_id=".lang_id();
			$db->Execute($q);
			if(!$par_id)
			{
				content_type_menu_create($id, 'Content List', $Bbc->mod['circuit'].'.'.$id.'_content_sub_list', $menu_id, 1);
				content_type_menu_create($id, 'Add Content', $Bbc->mod['circuit'].'.'.$id.'_content_sub_add', $menu_id, 2);
				content_type_menu_create($id, 'Category', $Bbc->mod['circuit'].'.'.$id.'_category_sub', $menu_id, 3);
			}else{
				_func('menu');
				menu_repair();
			}
		}
		return $menu_id;
	}
	return false;
}
function content_frontpage()
{
	global $db, $gbl_fontpage;
	$config = !empty($gbl_fontpage) ? $gbl_fontpage : get_config('content', 'frontpage');
	$add_sql= $config['auto'] ? '1' : 'is_front=1';
	$config['tot_list']	= @intval($config['tot_list']);
	$content= $config['intro'] ? 't.intro' : 't.content';
	$q = 'SELECT c.*, t.title, '.$content.' AS content FROM bbc_content AS c
				LEFT JOIN bbc_content_text AS t ON (c.id=t.content_id AND lang_id='.lang_id().')
				WHERE '.$add_sql.' AND publish=1 ORDER BY id DESC LIMIT 0, '.$config['tot_list'];
	$arr = $db->cacheGetAll($q);
	return $arr;
}
function content_title($title, $limit=0, $limit_by = 'word' /*word | char*/, $add = '...')
{
	$limit = intval($limit);
	if(!empty($limit))
	{
		$title = trim($title);
		switch($limit_by)
		{
			case 'word':
			case 'words':
				$len	= str_word_count($title);
				if($limit < $len)
				{
					$txt	= preg_replace("/\s+/"," ",$title);
					$r		= explode(' ', $txt);
					$title= implode(' ', array_splice ($r, 0, $limit)).$add;
				}
			break;
			case 'char':
			default:
				$len	= strlen($title);
				if($limit < $len)
				{
					$title = substr($title, 0, $limit).$add;
				}
			break;
		}
	}
	return $title;
}
function content_tag_link($id, $title = '')
{
	global $db;
	if(empty($title))
	{
		$q = "SELECT title FROM bbc_content_tag WHERE id=$id";
		$title =$db->getOne($q);
	}
	$output = site_url('index.php?mod=content.'.$id.'-'.menu_save($title));
	return $output;
}
function content_ext()
{
	include _CONF.'extensions.php';
	return $extensions;
}
function content_ext_all()
{
	include _CONF.'extensions.php';
	$output = array();
	foreach ($extensions as $key => $exts)
	{
		foreach ($exts as $ext)
		{
			$output[] = $ext;
		}
	}
	return $output;
}
function content_format($ext)
{
	$extension = content_ext();
	$output    = false;
	foreach ($extension as $name => $exts)
	{
		foreach ($exts as $ex)
		{
			if ($ex==$ext)
			{
				$output = $name;
				break;
			}
		}
	}
	return $output;
}
function content_related($id, $limit = 5, $add_sql='')
{
	global $db;
	$output = array(
		'list'  => array(),
		'total' => 0
		);
	if (empty($id) || !is_numeric($id))
	{
		return $output;
	}
	if (!empty($add_sql))
	{
		$add_sql = mysqli_real_escape_string($db->link, $add_sql);
		if (!preg_match('~^(?:\s+)?and(?:\s+)?~is', $add_sql))
		{
			$add_sql = 'AND '.$add_sql;
		}
	}
	$qlimit = 'LIMIT 0, '.$limit;
	$query  = "SELECT `related_id` FROM `bbc_content_related` WHERE `content_id`={$id}";
	$rid    = $db->cacheGetCol($query);
	if(!empty($rid))
	{
		$ids		= implode(',', $rid);
		$q="SELECT c.*, t.`title`, t.`intro`, t.`content` FROM bbc_content AS c
				LEFT JOIN bbc_content_text AS t ON (c.`id`=t.`content_id` AND t.`lang_id`=".lang_id().")
				WHERE c.`publish`=1 AND c.`id` IN ({$ids}) {$add_sql} ORDER BY c.`id` ASC {$qlimit}";
		$ar = $db->cacheGetAssoc($q);
		foreach ($rid as $i)
		{
			$data = array();
			if(!empty($ar[$i]))
			{
				$ar[$i]['id']	= $i;
				$data = $ar[$i];
			}else{
				$data = content_fetch($i);
			}
			if (!empty($data))
			{
				$output['list'][]= $data;
				$output['total']++;
				if ($output['total'] >= $limit)
				{
					break;
				}
			}
		}
	}
	if (get_config('content', 'manage', 'webtype')=='1' && $output['total'] < $limit)
	{
		$qlimit  = 'LIMIT 0, '.($limit - $output['total']);
		$tag_ids = $db->cacheGetCol("SELECT `tag_id` FROM `bbc_content_tag_list` WHERE `content_id`={$id} ORDER BY RAND() {$qlimit}");
		if (!empty($tag_ids))
		{
			$q = "SELECT  c.*, t.`title`, t.`intro`, t.`content`
			FROM `bbc_content_tag_list` AS l
			LEFT JOIN `bbc_content` AS c ON (c.`id`=l.`content_id`)
			LEFT JOIN `bbc_content_text` AS t ON (c.`id`=t.`content_id` AND t.`lang_id`=".lang_id().")
			WHERE l.tag_id IN (".implode(',', $tag_ids).") AND l.`content_id`!={$id} AND c.`publish`=1
			ORDER BY RAND() {$qlimit}";
			$ar  = $db->cacheGetAll($q);
			if (!empty($ar))
			{
				foreach ($ar as $d)
				{
					$output['list'][] = $d;
					$output['total']++;
					if ($output['total'] >= $limit)
					{
						break;
					}
				}
				$output['total'] = count($output['list']);
			}
		}
	}
	if (empty($rid) && $output['total'] < $limit)
	{
		$rid	 = array();
		$title = addslashes($db->cacheGetOne("SELECT `title` FROM `bbc_content_text` WHERE `content_id`={$id} AND `lang_id`=".lang_id()));
		$q = "SELECT  c.*, t.`title`, t.`intro`, t.`content`, MATCH (t.`title`,t.`description`,t.`keyword`,t.`tags`,t.`intro`,t.`content`) AGAINST ('{$title}' IN BOOLEAN MODE) AS relevance
		FROM bbc_content AS c LEFT JOIN bbc_content_text AS t ON (c.`id`=t.`content_id` AND t.`lang_id`=".lang_id().")
		WHERE c.`publish`=1 AND c.`id`!={$id} AND MATCH (t.`title`,t.`description`,t.`keyword`,t.`tags`,t.`intro`,t.`content`) AGAINST ('{$title}' IN BOOLEAN MODE)
		ORDER BY relevance DESC {$qlimit}";
		$output['list']  = $db->cacheGetAll($q);
		if(!empty($output['list']))
		{
			$db->cache_clean($query);
			$output['total']   = count($output['list']);
			foreach($output['list'] AS $d)
			{
				if (!$db->getOne("SELECT 1 FROM `bbc_content_related` WHERE `content_id`={$id} AND `related_id`=".$d['id']))
				{
					$db->Execute("INSERT INTO `bbc_content_related` SET `content_id`=$id, `related_id`=".$d['id']);
				}
			}
		}
	}
	return $output;
}
function content_video($code)
{
	$output = '';
	if (!empty($code))
	{
		include_once _ROOT.'modules/content/constants.php';
		$output = str_replace('{code}', $code, _VIDEO_EMBED);
	}
	return $output;
}
function content_audio($code)
{
	$output = '';
	if (!empty($code))
	{
		include_once _ROOT.'modules/content/constants.php';
		$output = str_replace('{code}', $code, _AUDIO_EMBED);
	}
	return $output;
}