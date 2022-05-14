<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

_func('content');
class content_class {
	var $lang_id;
	var $r_lang;
	var $error = array();
	var $allowed_tags = '<br><a><i><b><u>';
	var $check_types = array();
	function __construct()
	{
		global $db, $user, $sys, $Bbc;
		$path             = 'images/modules/content/';
		$this->lang_id    = lang_id();
		$this->r_lang     = lang_assoc();
		$this->path       = $path;
		$this->img_path   = _ROOT.$path.'images/';
		$this->img_url    = _URL.$path.'images/';
		$this->tmp_path   = _ROOT.'images/tmp/content/';
		$this->text_path  = _ROOT.$path.'comments/';
		$this->prune_path = content_prune_path();
		$this->conf       = get_config('content');
		$this->img        = _class('images', $this->img_path);
		$this->img_lib    = _class('image_lib');
		$this->db         = &$db;
		$this->user       = &$user;
		$this->sys        = &$sys;
		$this->Bbc        = &$Bbc;
		if(!is_dir($this->img_path))
		{
			_func('path');
			path_create($this->img_path);
		}
	}
	function content_save($data, $content_id = 0)
	{
		$post = $this->_content_data($data, $content_id);
		if(empty($post))
		{
			return false;
		}
		$tmp_data = content_fetch($content_id, false);
		// old image exists but different than the new one in POST
		if (!empty($tmp_data['image']) && $tmp_data['image']!=$post['image'])
		{
			_class('images')->delete($this->img_path.$tmp_data['image']);
			_class('images')->delete($this->img_path.'p_'.$tmp_data['image']);
		}
		if ($post['img_rename'])
		{
			$new_image_name = menu_save($post['text'][lang_id()]['title']);
			$cur_image_name = $post['image'];
			if (!empty($cur_image_name))
			{
				if(preg_match('~(\.[a-z0-9]+)$~is', $cur_image_name, $m))
				{
					$new_image_name .= $m[1];
					if ($new_image_name != $cur_image_name)
					{
						if(_class('images')->rename($this->img_path.$cur_image_name, $this->img_path.$new_image_name))
						{
							$post['image'] = $new_image_name;
							_class('images')->rename($this->img_path.'p_'.$cur_image_name, $this->img_path.'p_'.$new_image_name);
						}
					}
				}
			}
		}
		if (!empty($tmp_data['image']) && empty($post['image']))
		{
			$post['image'] = $tmp_data['image'];
		}
		$path = _ROOT.$this->path.$content_id.'/';
		if (!empty($tmp_data['file']))
		{
			if (empty($post['file']) && $post['kind_id']==2)
			{
				$post['file'] = $tmp_data['file'];
			}else{
				if ($tmp_data['file']!=$post['file'])
				{
					_class('images')->delete($path.$tmp_data['file']);
				}
			}
		}
		if (!empty($tmp_data['images']))
		{
			$images_old = config_decode($tmp_data['images']);
			$images_new = config_decode($post['images']);
			// Convert $images_old to array_values of images only
			if (!empty($images_old))
			{
				$r = array();
				foreach ($images_old as $d)
				{
					$r[] = $d['image'];
				}
				$images_old = $r;
			}else $images_old = array();
			// Convert $images_new to array_values of images only
			if (!empty($images_new))
			{
				$r = array();
				foreach ($images_new as $d)
				{
					$r[] = $d['image'];
				}
				$images_new = $r;
			}else $images_new = array();
			// Loop $images_old in case some images are deleted
			foreach ($images_old as $img)
			{
				if (!in_array($img, $images_new))
				{
					_class('images')->delete($path.$img);
					_class('images')->delete($path.'thumb_'.$img);
				}
			}
		}
		if(!empty($tmp_data['prune']))
		{
			$tmp_content_id = $this->_content_save_prune($content_id, $tmp_data, $post);
		}else{
			$tmp_content_id = $this->_content_save_db($content_id, $tmp_data, $post);
		}
		if($tmp_content_id > 0)
		{
			/*================================================
			 * INSERT RELATED CONTENT IF EXISTS IN POST DATA
			 *===============================================*/
			if (isset($post['content_related']))
			{
				$r1 = $this->db->getAssoc("SELECT `id`, `related_id` FROM `bbc_content_related` WHERE `content_id`={$tmp_content_id}");
				// HAPUS DR DB JIKA TIDAK ADA DI POST
				foreach($r1 AS $i => $d)
				{
					if(!in_array($d, $post['content_related']))
					{
						$this->db->Execute("DELETE FROM `bbc_content_related` WHERE `id`={$i}");
					}
				}
				// HAPUS DR DB JIKA ADA RELATED YG DOUBLE
				$r2 = array_values($r1);
				if ($r2 != array_unique($r2))
				{
					$new_r = array();
					foreach ($r1 as $i => $j)
					{
						if (!in_array($j, $new_r))
						{
							$new_r[$i] = $j;
						}else{
							$this->db->Execute("DELETE FROM `bbc_content_related` WHERE `id`={$i}");
						}
					}
					$r1 = $new_r;
				}
				// MASUKKAN KE DB JIKA ADA DI POST TAPI TIDAK ADA DI DB
				foreach($post['content_related'] AS $i)
				{
					if(!in_array($i, $r1))
					{
						if(content_check($i)) // CHECK DULU APA BENAR CONTENT_ID ITU VALID
						{
							$this->db->Execute("INSERT INTO `bbc_content_related` SET `content_id`={$tmp_content_id},`related_id`={$i}");
						}
					}
				}
			}
			/*================================================
			 * INSERT CONTENT TAGS IF EXISTS IN POST DATA AND WEB TYPE IS ARTICLES
			 *===============================================*/
			if (@$this->conf['manage']['webtype']=='1' && _ADMIN)
			{
				$post['tags_new'] = array();
				$r1 = $this->db->getCol("SELECT tag_id FROM `bbc_content_tag_list` WHERE content_id={$tmp_content_id}");
				// HAPUS DR DB JIKA TIDAK ADA DI POST
				foreach($r1 AS $i)
				{
					if(!in_array($i, $post['tags_ids']))
					{
						$this->db->Execute("DELETE FROM `bbc_content_tag_list` WHERE `tag_id`={$i}");
						$total = $this->db->getOne("SELECT COUNT(1) FROM `bbc_content_tag_list` WHERE `tag_id`={$i}");
						$this->db->Execute("UPDATE `bbc_content_tag` SET `total`={$total}, `updated`=NOW() WHERE `id`={$i}");
					}
				}
				// MASUKKAN KE DB JIKA TIDAK ADA DI DB
				foreach($post['tags_ids'] AS $i)
				{
					if(!in_array($i, $r1))
					{
						if (is_numeric($i))
						{
							$this->db->Execute("INSERT INTO `bbc_content_tag_list` SET `content_id`={$tmp_content_id},`tag_id`={$i}");
							$total = $this->db->getOne("SELECT COUNT(1) FROM `bbc_content_tag_list` WHERE `tag_id`={$i}");
							$this->db->Execute("UPDATE `bbc_content_tag` SET `total`={$total}, `updated`=NOW() WHERE `id`={$i}");
						}else{
							$post['tags_new'][] = $i;
						}
					}
				}
				// BUAT CONTENT TAGS BARU JIKA ADA MASUKAN USER
				if (!empty($post['tags_new']))
				{
					foreach ($post['tags_new'] as $i)
					{
						if($this->db->Execute("INSERT INTO `bbc_content_tag` SET `title`='{$i}', `total`=1, `created`=NOW()"))
						{
							$j = $this->db->Insert_ID();
							$this->db->Execute("INSERT INTO `bbc_content_tag_list` SET `content_id`={$tmp_content_id},`tag_id`={$j}");
						}
					}
				}
			}
			/*================================================
			 * INSERT CONTENT'S MENUS
			 *===============================================*/
			$form_act = ($content_id) ? 'edit' : 'add';
			$av_menu = @$_SESSION[$form_act.'content_menus_exists'];
			if(!empty($av_menu))
			{
				$module_id = $this->sys->get_module_id('content');
				$link_title= $post['text'][$this->lang_id]['title'];
				foreach((array)$av_menu AS $menu)
				{
					if($menu['code']=='delete')
					{
						menu_delete($menu['id']);
					}else
					if($menu['code']=='new')
					{
						$q = "INSERT INTO `bbc_menu` SET
							`par_id`		= '".$menu['par_id']."',
							`module_id`		= '".$module_id."',
							`seo`					= '".menu_seo($menu['seo'], $menu['title'])."',
							`link`				= 'index.php?mod=content.detail&id=$tmp_content_id&title=$link_title',
							`orderby`			= '".$menu['orderby']."',
							`cat_id`			= '".$menu['cat_id']."',
							`is_content`	= 1,
							`content_id`	= '".$tmp_content_id."',
							`protected`		= 0,
							`is_admin`		= 0,
							`active`			= 1
							";
						if($this->db->Execute($q))
						{
							$menu_id = $this->db->Insert_ID();
							$q = "SELECT lang_id FROM bbc_menu_text WHERE menu_id=$menu_id";
							$r_key_lang = $this->db->getCol($q);
							// INSERT TITLE
							foreach((array)$menu['titles'] AS $lang_id => $title)
							{
								if(in_array($lang_id, $r_key_lang))
								{
									$q = "UPDATE `bbc_menu_text` SET `title`='{$title}' WHERE `menu_id`={$menu_id} AND `lang_id`={$lang_id}";
								}else{
									$q = "INSERT INTO `bbc_menu_text` SET `menu_id`={$menu_id}, `title`='{$title}', `lang_id`={$lang_id}";
								}
								$this->db->Execute($q);
							}
							// REPAIR ORDERBY..
							$q="UPDATE `bbc_menu` SET `orderby`=(`orderby`+1)
								WHERE `cat_id`= ".$menu['cat_id']."
								AND `par_id`	= ".$menu['par_id']."
								AND `is_admin`= 0
								AND `orderby`>=".$menu['orderby']."
								AND `id`		 != ".$menu_id."
							";
							$this->db->Execute($q);
							menu_repair();
						}
					}
				}
			}
			$this->sys->clean_cache();
		}
		return $tmp_content_id;
	}
	function _content_save_prune($content_id, $tmp_data, $post)
	{
		$tmp_content_id = 0;
		$post           = stripslashes_r($post);
		$user           = $this->getUser();
		if(!empty($tmp_data) && $content_id > 0)
		{
			$r = array(
				'id'               => $content_id,
				'par_id'           => $tmp_data['par_id'],
				'type_id'          => $tmp_data['type_id'],
				'kind_id'          => $post['kind_id'],
				'file'             => $post['file'],
				'file_url'         => $post['file_url'],
				'file_format'      => $post['file_format'],
				'file_type'        => $post['file_type'],
				'file_register'    => $post['file_register'],
				'file_hit'         => $post['file_hit'],
				'file_hit_time'    => $post['file_hit_time'],
				'file_hit_ip'      => $post['file_hit_ip'],
				'video'            => $post['video'],
				'audio'            => $post['audio'],
				'image'            => $post['image'],
				'caption'          => $post['caption'],
				'images'           => $post['images'],
				'created'          => $tmp_data['created'],
				'created_by'       => $tmp_data['created_by'],
				'created_by_alias' => !empty($post['created_by_alias']) ? $post['created_by_alias'] : $tmp_data['created_by_alias'],
				'modified'         => date('Y-m-d H:i:s'),
				'modified_by'      => $user->id,
				'revised'          => (intval($tmp_data['revised']) + 1),
				'privilege'        => $post['privilege'],
				'hits'             => $tmp_data['hits'],
				'rating'           => $tmp_data['rating'],
				'last_hits'        => $tmp_data['last_hits'],
				'is_popimage'      => $tmp_data['is_popimage'],
				'is_front'         => 0,
				'is_config'        => $post['is_config'],
				'config'           => $post['config'],
				'publish'          => $post['publish']
				);
			file_write($this->prune_path.$content_id.'.cfg', json_encode($r));
		}
		if($content_id > 0)
		{
			/*================================================
			 * INSERT CONTENT'S TEXT
			 *===============================================*/
			foreach($this->r_lang AS $d)
			{
				$r = array(
					'content_id'	=> $content_id,
					'title'				=> $post['title'],
					'description'	=> $post['description'],
					'keyword'			=> $post['keyword'],
					'intro'				=> $post['intro'],
					'content'			=> $post['content'],
					'lang_id'			=> $d['id']
					);
				file_write($this->prune_path.$content_id.'-'.$d['id'].'.cfg', json_encode($r));
			}
			/*================================================
			 * INSERT CONTENT'S CATEGORY
			 *===============================================*/
			file_write($this->prune_path.$content_id.'-cat.cfg', json_encode($post['cat_ids']));
		}
		return $content_id;
	}
	function _content_save_db($content_id, $tmp_data, $post)
	{
		$tmp_content_id = 0;
		$post = addslashes_r($post);
		if($content_id > 0 && !empty($tmp_data))
		{
			$q = "UPDATE `bbc_content` SET
				`par_id`           = '".$post['par_id']."',
				`kind_id`          = '".$post['kind_id']."',
				`file`             = '".$post['file']."',
				`file_url`         = '".$post['file_url']."',
				`file_format`      = '".$post['file_format']."',
				`file_type`        = '".$post['file_type']."',
				`file_register`    = '".$post['file_register']."',
				`video`            = '".$post['video']."',
				`audio`            = '".$post['audio']."',
				`image`            = '".$post['image']."',
				`caption`          = '".$post['caption']."',
				`images`           = '".$post['images']."',
				`created_by_alias` = '".$post['created_by_alias']."',
				`revised`          = (`revised`+1),
				`privilege`        = '".$post['privilege']."',
				`is_popimage`      = '".$post['is_popimage']."',
				`is_front`         = '".$post['is_front']."',
				`is_config`        = '".$post['is_config']."',
				`config`           = '".$post['config']."',
				`publish`          = '".$post['publish']."',
				`modified`         = NOW(),
				`modified_by`      = ".$post['modified_by']."
				WHERE `id`         = {$content_id}";
			$this->db->Execute($q);
			$tmp_content_id = $content_id;
		}else{
			$q = "INSERT INTO `bbc_content` SET
				`par_id`           = '".$post['par_id']."',
				`type_id`          = '".$post['type_id']."',
				`kind_id`          = '".$post['kind_id']."',
				`file`             = '".$post['file']."',
				`file_url`         = '".$post['file_url']."',
				`file_format`      = '".$post['file_format']."',
				`file_type`        = '".$post['file_type']."',
				`file_register`    = '".$post['file_register']."',
				`file_hit`         = '0',
				`file_hit_time`    = '0000-00-00 00:00:00',
				`file_hit_ip`      = '',
				`video`            = '".$post['video']."',
				`audio`            = '".$post['audio']."',
				`image`            = '".$post['image']."',
				`caption`          = '".$post['caption']."',
				`images`           = '".$post['images']."',
				`created`          = NOW(),
				`created_by`       = ".$post['created_by'].",
				`created_by_alias` = '".$post['created_by_alias']."',
				`revised`          = 0,
				`privilege`        = '".$post['privilege']."',
				`is_popimage`      = '".$post['is_popimage']."',
				`is_front`         = '".$post['is_front']."',
				`is_config`        = '".$post['is_config']."',
				`config`           = '".$post['config']."',
				`publish`          = '".$post['publish']."'";
			$this->db->Execute($q);
			$tmp_content_id = $this->db->Insert_ID();
			if ($tmp_content_id > 0 && in_array($post['kind_id'], array(1, 2)))
			{
				$t_f = _ROOT.$this->path.'0/';
				$t_t = _ROOT.$this->path.$tmp_content_id.'/';
				$t_r = path_list($t_f);
				foreach ($t_r as $f)
				{
					_class('images')->rename($t_f.$f, $t_t.$f);
				}
				// _class('images')->rename(_ROOT.$this->path.'0/', _ROOT.$this->path.$tmp_content_id.'/');
				if ($post['kind_id']==1)
				{
					$r = json_decode(stripslashes($post['images']), 1);
					if (!empty($r[0]['image']) && $post['image']!=$this->path.$tmp_content_id.'/'.$r[0]['image'])
					{
						$q = "UPDATE `bbc_content` SET `image`='{$this->path}{$tmp_content_id}/".$r[0]['image']."' WHERE id={$tmp_content_id}";
						$this->db->Execute($q);
					}
				}
			}
			$tmp_data['cat_ids'] = array();
			$this->_content_prune();
		}
		if($tmp_content_id > 0)
		{
			/*================================================
			 * INSERT CONTENT'S TEXT
			 *===============================================*/
			$q = "SELECT `lang_id` FROM `bbc_content_text` WHERE `content_id`={$tmp_content_id}";
			$r_key_lang = $this->db->getCol($q);
			foreach((array)$post['text'] AS $lang_id => $dt)
			{
				if(in_array($lang_id, $r_key_lang))
				{
					$q = "UPDATE `bbc_content_text` SET
						`title`         = '".$dt['title']."',
						`description`   = '".$dt['description']."',
						`keyword`       = '".$dt['keyword']."',
						`tags`          = '".$dt['tags']."',
						`intro`         = '".$dt['intro']."',
						`content`       = '".$dt['content']."'
						WHERE `lang_id` = {$lang_id} AND `content_id`={$tmp_content_id}";
				}else{
					$q = "INSERT INTO `bbc_content_text` SET
						`title`       = '".$dt['title']."',
						`description` = '".$dt['description']."',
						`keyword`     = '".$dt['keyword']."',
						`tags`        = '".$dt['tags']."',
						`intro`       = '".$dt['intro']."',
						`content`     = '".$dt['content']."',
						`content_id`  = {$tmp_content_id},
						`lang_id`     = {$lang_id}";
				}
				$this->db->Execute($q);
			}
			/*================================================
			 * INSERT CONTENT TO CATEGORY
			 *===============================================*/
			$q = "SELECT `cat_id` FROM `bbc_content_category` WHERE `content_id`={$tmp_content_id}";
			$r_key_cat = $this->db->getCol($q);
			foreach((array)$r_key_cat AS $cat_id)
			{
				if(!in_array($cat_id, $post['cat_ids']))
				{
					$q = "DELETE FROM `bbc_content_category` WHERE `cat_id`={$cat_id} AND `content_id`={$tmp_content_id}";
					$this->db->Execute($q);
				}
			}
			foreach((array)$post['cat_ids'] AS $cat_id)
			{
				if(!in_array($cat_id, $r_key_cat))
				{
					$q = "INSERT INTO `bbc_content_category` SET
						`content_id` = {$tmp_content_id},
						`cat_id`     = {$cat_id},
						`pruned`     = 0";
					$this->db->Execute($q);
				}
			}
			/*================================================
			 * INSERT CONTENT SCHEDULE IF EXISTS IN POST DATA
			 *===============================================*/
			if (@$this->conf['manage']['webtype']=='1' && _ADMIN)
			{
				$r1 = $this->db->getCol("SELECT `id` FROM `bbc_content_schedule` WHERE `content_id`={$tmp_content_id}");
				$r2 = array();
				foreach ($post['schedule'] as $schedule)
				{
					if ($schedule['id'] > 0)
					{
						$r2[] = $schedule['id'];
						$q    = "UPDATE `bbc_content_schedule` SET `action`=".$schedule['action'].", `action_time`='".$schedule['action_time']."' WHERE `id`=".$schedule['id'];
					}else{
						$q    = "INSERT INTO `bbc_content_schedule` SET `action`=".$schedule['action'].", `action_time`='".$schedule['action_time']."', `created`=NOW(), `content_id`={$tmp_content_id}";
					}
					$this->db->Execute($q);
				}
				foreach ($r1 as $i)
				{
					if (!in_array($i, $r2))
					{
						$this->db->Execute("DELETE FROM `bbc_content_schedule` WHERE `id`={$i}");
					}
				}
			}
		}
		return $tmp_content_id;
	}
	function _content_prune()
	{
		$content_max = intval(get_config(0, 'rules', 'content_max'));
		if($content_max > 0)
		{
			$q = "SELECT COUNT(1) FROM `bbc_content` WHERE 1";
			$t = $this->db->getOne($q);
			if($t > $content_max)
			{
				$config       = get_config('content', 'frontpage');
				$add_sql      = $config['auto'] ? '1' : '`is_front` != 1';
				$limit_delete = $t - $content_max;
				if ($limit_delete > 20)
				{
					$limit_delete = 20;
				}
				$q = "SELECT `id` FROM `bbc_content` WHERE {$add_sql} ORDER BY `id` ASC LIMIT $limit_delete";
				$ids = $this->db->getCol($q);
				if(!empty($ids))
				{
					$ids = implode(',', $ids);
				}else{
					return false;
				}

				// MAIN
				$q = "SELECT * FROM `bbc_content` WHERE `id` IN ({$ids})";
				$r = $this->db->getAll($q);
				foreach($r AS $d)
				{
					file_write($this->prune_path.$d['id'].'.cfg', json_encode($d));
				}
				$q = "DELETE FROM `bbc_content` WHERE id IN ({$ids})";
				$this->db->Execute($q);

				// TEXT
				$q = "SELECT * FROM `bbc_content_text` WHERE `content_id` IN ({$ids})";
				$r = $this->db->getAll($q);
				foreach($r AS $d)
				{
					file_write($this->prune_path.$d['content_id'].'-'.$d['lang_id'].'.cfg', json_encode($d));
				}
				$q = "DELETE FROM `bbc_content_text` WHERE `content_id` IN ({$ids})";
				$this->db->Execute($q);

				// CATEGORY
				$r_cat = array();
				$q = "SELECT * FROM `bbc_content_category` WHERE `content_id` IN ({$ids})";
				$r = $this->db->getAll($q);
				foreach($r AS $d)
				{
					$r_cat[$d['content_id']][] = $d['cat_id'];
				}
				foreach($r_cat AS $content_id => $d)
				{
					file_write($this->prune_path.$content_id.'-cat.cfg', json_encode($d));
				}
				$q = "DELETE FROM `bbc_content_category` WHERE `content_id` IN ({$ids})";
				$this->db->Execute($q);

				$q = "UPDATE `bbc_content` SET `par_id`=0 WHERE `par_id` IN ({$ids})";
				$this->db->Execute($q);
			}
		}
	}
	function _content_data($input, $content_id=0)
	{
		$is_config = $this->_enum(@$input['is_config']);
		$par_id    = @intval($input['par_id']);
		$type_id   = @intval($input['type_id']);
		$user      = $this->getUser();
		if (!isset($this->check_types[$type_id]))
		{
			if(!$this->db->getOne("SELECT 1 FROM `bbc_content_type` WHERE id={$type_id}"))
			{
				$q = "SELECT `id` FROM `bbc_content_type` WHERE `active`=1 ORDER BY id";
				$type_id = intval($this->db->getOne($q));
				$this->check_types[$type_id] = true;
			}
		}
		if (empty($input['tmp_dir']))
		{
			$input['tmp_dir'] = date('YmdHis');
		}
		$this->tmp_path .= $input['tmp_dir'] ? $input['tmp_dir'].'/' : '';
		$def_config = content_type($type_id, 'detail');
		$kind_id    = @intval($input['kind_id']);
		if ($kind_id > 4 || $kind_id < 0)
		{
			$kind_id = 0;
		}
		$output = array(
			'is_config'        => $is_config,
			'config'           => ($is_config == '1') ? json_encode($input['config']) : '',
			'par_id'           => $par_id,
			'type_id'          => $type_id,
			'kind_id'          => $kind_id,
			'file'             => '',
			'file_url'         => '',
			'file_format'      => '',
			'file_type'        => 0,
			'file_register'    => 0,
			'video'            => '',
			'audio'            => '',
			'image'            => '',
			'caption'          => @$input['caption'],
			'images'           => '',
			'privilege'        => ',all,',
			'cat_ids'          => isset($input['cat_ids']) ? $this->_get_cat($input['cat_ids']) : array(),
			'tags_ids'         => isset($input['tags_ids']) && is_array($input['tags_ids']) ? $input['tags_ids'] : array(),
			'text'             => array(),
			'schedule'         => array(),
			'modified_by'      => (!empty($input['modified_by']) ? $input['modified_by'] : $user->id),
			'created_by'       => (!empty($input['created_by']) ? $input['created_by'] : $user->id),
			'created_by_alias' => (!empty($input['created_by_alias']) ? $input['created_by_alias'] : @$user->name),
			'content_related'  => (!empty($input['content_related']) ? $input['content_related'] : array()),
			'is_popimage'      => $this->_enum(@$input['is_popimage']),
			'is_front'         => $this->_enum(@$input['is_front']),
			'publish'          => $this->_enum(@$input['publish']),
			'img_rename'       => 0 // to detect file image will be rename as title or not
			);
		if (!isset($input['content_related']))
		{
			unset($output['content_related']);
		}
		// REPAIR SUBMITED SCHEDULE
		if (!empty($input['schedule']) && !empty($input['schedule']['action_time']) && is_array($input['schedule']['action_time']))
		{
			$regex = '~^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$~s';
			foreach ($input['schedule']['action_time'] as $i => $time)
			{
				if (preg_match($regex, $time))
				{
					$output['schedule'][] = array(
						'id'          => $input['schedule']['id'][$i],
						'action'      => $input['schedule']['action'][$i],
						'action_time' => $input['schedule']['action_time'][$i]
						);
				}
			}
		}
		// GET TEXT SUBMITED
		$text = array();
		if(isset($input['text']) && is_array($input['text']))
		{
			foreach($this->r_lang AS $lang_id => $dt)
			{
				$title			= @$input['text']['title'][$lang_id];
				$description= @$input['text']['description'][$lang_id];
				$keyword		= @$input['text']['keyword'][$lang_id];
				$tags				= @$input['text']['tags'][$lang_id];
				$intro			= isset($input['text']['intro'][$lang_id]) ? $input['text']['intro'][$lang_id] : @$input['text_intro_'.$lang_id];
				$content		= isset($input['text']['content'][$lang_id]) ? $input['text']['content'][$lang_id] : @$input['text_content_'.$lang_id];
				$s_intro		= $intro = $this->strip($intro);
				if(empty($s_intro)) $intro = $this->strip($content);
				if(empty($description)) $description = $this->strip($intro, 150);
				if(empty($keyword) && config('manage','is_nested')!='1') $keyword = $title.', '.$this->strip($description, 80);
				$text[$lang_id] = array(
					'title'				=> $title,
					'description'	=> $description,
					'keyword'			=> $keyword,
					'tags'				=> $tags,
					'intro'				=> nl2br($intro),
					'content'			=> $content
					);
			}
		}else
		if(isset($input['title']) && isset($input['content']))
		{
			if(is_array($input['title']) && is_array($input['content']))
			{
				$text = array();
				foreach($this->r_lang AS $lang_id => $dt)
				{
					$title			= @$input['title'][$lang_id];
					$description= @$input['description'][$lang_id];
					$keyword		= @$input['keyword'][$lang_id];
					$tags				= @$input['tags'][$lang_id];
					$intro			= @$input['intro'][$lang_id];
					$content		= @$input['content'][$lang_id];
					$s_intro		= $intro = $this->strip($intro);
					if(empty($s_intro)) $intro = $this->strip($content);
					if(empty($description)) $description = $this->strip($intro, 150);
					if(empty($keyword) && config('manage','is_nested')!='1') $keyword = $title.', '.$this->strip($description, 80);
					$text[$lang_id] = array(
						'title'				=> $title,
						'description'	=> $description,
						'keyword'			=> $keyword,
						'tags'				=> $tags,
						'intro'				=> nl2br($intro),
						'content'			=> $content
						);
				}
			}else{
				$text = array();
				$title			= @$input['title'];
				$description= @$input['description'];
				$keyword		= @$input['keyword'];
				$tags				= @$input['tags'];
				$intro			= @$input['intro'];
				$content		= @$input['content'];
				$s_intro		= $intro = $this->strip($intro);
				if(empty($s_intro)) $intro = $this->strip($content);
				if(empty($description)) $description = $this->strip($intro, 150);
				if(empty($keyword) && config('manage','is_nested')!='1') $keyword = $title.', '.$this->strip($description, 80);
				foreach($this->r_lang AS $lang_id => $dt)
				{
					$text[$lang_id] = array(
						'title'				=> $title,
						'description'	=> $description,
						'keyword'			=> $keyword,
						'tags'				=> $tags,
						'intro'				=> nl2br($intro),
						'content'			=> $content
						);
				}
			}
		}else return false;
		$output['text'] = $text;

		_func('path');
		$path = _ROOT.$this->path.$content_id.'/';
		switch ($kind_id)
		{
			case '1': /* GALLERY */
				$images = array();
				path_create($path);
				foreach ((array)@$input['images']['order'] as $i => $img)
				{
					if (is_file($this->tmp_path.$img) || is_file($path.$img))
					{
						if (is_file($this->tmp_path.$img))
						{
							_class('images')->rename($this->tmp_path.$img, $path.$img);
							_class('images')->rename($this->tmp_path.'thumb_'.$img, $path.'thumb_'.$img);
						}
						$images[] = array(
							'image'       => $img,
							'title'       => @$input['images']['title'][$i],
							'description' => @$input['images']['description'][$i]
							);
						if (empty($output['image']))
						{
							$output['image']   = $this->path.$content_id.'/'.$img;
							$output['caption'] = @$input['images']['description'][$i];
						}
					}
				}
				$output['images'] = config_encode($images);
				break;
			case '2': /* DOWNLOAD */
				path_create($path);
				$output['file_type']     = $this->_enum(@$input['file_type']);
				$output['file_register'] = $this->_enum(@$input['file_register']);
				if (!empty($output['file_type'])) // file in other URL
				{
					$output['file_format'] = $input['file_format'];
					$output['file_url']    = $input['file_url'];
					if (!is_url($output['file_url']))
					{
						$output['file_url'] = 'http://'.$output['file_url'];
					}
				}else{
					$is_valid = false;
					$exts     = array();
					$rext     = content_ext();
					foreach ($rext as $val => $r1)
					{
						foreach ($r1 as $key)
						{
							$exts[$key] = $val;
						}
					}
					if(@is_uploaded_file($_FILES['file']['tmp_name']))
					{
						if (preg_match('~\.([a-z0-9]+)$~is', $_FILES['file']['name'], $m))
						{
							$ext = strtolower($m[1]);
							if (isset($exts[$ext]))
							{
								if (_class('images')->move_upload($_FILES['file']['tmp_name'], $path.$_FILES['file']['name']))
								{
									$is_valid              = true;
									$output['file']        = $_FILES['file']['name'];
									$output['file_format'] = $exts[$ext];
								}
							}
						}
					}else
					if (!empty($input['file']))
					{
						$is_valid = false;
						if (preg_match('~\.([a-z0-9]+)$~is', $input['file'], $m))
						{
							$ext = strtolower($m[1]);
							if (isset($exts[$ext]))
							{
								$is_valid = true;
								_class('images')->rename($this->tmp_path.$input['file'], $path.$input['file']);
							}
						}
						if ($is_valid)
						{
							$output['file']        = $input['file'];
							$output['file_format'] = $exts[$ext];
						}
					}
					if (!$is_valid)
					{
						return $is_valid;
					}
				}
				break;
			case '3': /* VIDEO */
				include_once _ROOT.'modules/content/constants.php';
				if (!empty($input['video']))
				{
					if (is_url($input['video']))
					{
						if (preg_match('~v=([^&/]+)~s', $input['video'], $m))
						{
							$output['video'] = $m[1];
						}else
						if (preg_match('~youtu\.be/([^/&\?]+)~s', $input['video'], $m))
						{
							$output['video'] = $m[1];
						}
					}else{
						$output['video'] = $input['video'];
					}
					if (!empty($output['video']))
					{
						$output['image'] = str_replace('{code}', $output['video'], _VIDEO_IMAGE);
					}
				}
				if (empty($output['video']))
				{
					return false;
				}
				break;
			case '4': /* AUDIO */
				include_once _ROOT.'modules/content/constants.php';
				if (!empty($input['audio']))
				{
					if (is_url($input['audio']))
					{
						if (preg_match('~v=([^&/]+)~s', $input['audio'], $m))
						{
							$output['audio'] = $m[1];
						}
					}else{
						$output['audio'] = $input['audio'];
					}
					if (!empty($output['audio']) && empty($input['image_text']) && !@is_uploaded_file($_FILES['image']['tmp_name']))
					{
						$url  = str_replace('{code}', $output['audio'], _AUDIO_IMAGE);
						$json = $this->sys->curl($url);
						$arr  = json_decode($json, 1);
						if (!empty($arr['artwork_url']))
						{
							$output['image'] = str_replace("-large.", "-t500x500.", $arr['artwork_url']);
						}
					}
				}
				if (empty($output['audio']))
				{
					return false;
				}
				break;
		}
		/* GET IMAGE UPLOADED */
		if (empty($output['image']))
		{
			$thumbsize = ($is_config == '1') ? intval($input['config']['thumbsize']) : $def_config['thumbsize'];
			// upload file with unsuported browser (javascript auto upload not working)
			if(@is_uploaded_file($_FILES['image']['tmp_name']))
			{
				$output['image']      = $this->img->upload($_FILES['image']);
				$output['img_rename'] = 1;
				$this->_content_data_image($this->img_path, $output['image'], $thumbsize);
			}else{
				// image is in input text not in input file
				if (!empty($input['image_text']))
				{
					$img_file  = str_replace(_URL, '', $input['image_text']);
					$img_cache = str_replace(_ROOT, '', $this->tmp_path);
					// image is taken from thirdparty URL
					if (is_url($img_file))
					{
						$output['image'] = $img_file;
					}else
					// image file is exists
					if (is_file(_ROOT.$img_file))
					{
						// image is from auto upload (javascript)
						if (preg_match('~images/cache/?~', $input['image_text']))
						{
							if (preg_match('~([^/]+)$~s', $input['image_text'], $m))
							{
								$output['img_rename'] = 1;
								$image_name           = $m[1];
								if (_class('images')->rename(_ROOT.$img_file, $this->img_path.'p_'.$image_name))
								{
									$output['image'] = $image_name;
									_class('images')->rename(dirname(_ROOT.$img_file).'/thumb_'.$image_name, $this->img_path.$image_name);
								}
							}
						}else{
							// image is from "Browse server!" button
							$output['image'] = $img_file;
						}
					}
				}
			}
		}
		_func('path', 'delete', $this->tmp_path);
		/* CONTENT PRIVILEGES */
		if (!empty($input['protect']))
		{
			$output['privilege'] = repairImplode($input['privilege']);
		}else{
			$output['privilege'] = ',all,';
		}
		if(!empty($output['content_related']))
		{
			if (is_array($output['content_related']))
			{
				$output['content_related'] = array_unique($output['content_related']);
			}else{
				$output['content_related'] = preg_replace(array('~^[^0-9]+~','~[^0-9]+$~','~[^0-9]+~', '~,{2,}~'), array('','',',',','), $output['content_related']);
				if(!empty($output['content_related']))
				{
					$output['content_related'] = array_unique(explode(',',$output['content_related']));
				}
			}
			if (!empty($content_id) && !empty($output['content_related']))
			{
				$i = array_search($content_id, $output['content_related']);
				if (is_numeric($i))
				{
					unset($output['content_related'][$i]);
				}
			}
		}else $output['content_related'] = array();
		return $output;
	}
	function _content_data_image($path, $image, $thumbsize)
	{
		// resize large image
		$this->_content_data_image_resize($path, $this->conf['manage']['image_size'], $image, 'p_'.$image);
		// image watermark
		$this->_content_data_image_watermark($path, 'p_'.$image);
		// resize thumbnail
		$this->_content_data_image_resize($path, $thumbsize, 'p_'.$image, $image);
	}
	function _content_data_image_resize($path, $sizes, $image, $result)
	{
		$config = image_size($sizes, true);
		$sizes  = getimagesize($path.$image);
		$this->img->setpath($path);
		$this->img->setimage($image);
		if($config[0] == $config[1]) // width and height are the same size
		{
			$this->img->resize($config, $result);
		}else{
			$max = image_transform($sizes[0], $sizes[1], $config[0], $config[1]);
			$this->img->resize($max);
			$cfg = array(
				'image_library'  => 'gd2',
				'source_image'   => $path.$image,
				'new_image'      => $path.$result,
				'maintain_ratio' => false,
				'width'          => $config[0],
				'height'         => $config[1],
				);
			if($max[0] > $cfg['width'])
			{
				$cfg['x_axis'] = ceil(($max[0]-$cfg['width'])/2);
			}else{
				$cfg['x_axis'] = 0;
			}
			if($max[1] > $cfg['height'])
			{
				$cfg['y_axis'] = ceil(($max[1]-$cfg['height'])/2);
			}else{
				$cfg['y_axis'] = 0;
			}
			$this->img_lib->initialize($cfg);
			$this->img_lib->crop();
			$this->img_lib->clear();
		}
	}
	function _content_data_image_watermark($path, $image)
	{
		if (@$this->conf['manage']['image_watermark']=='1'
			&& !empty($this->conf['manage']['image_watermark_file'])
			&& file_exists($path.$this->conf['manage']['image_watermark_file']))
		{
			$cfg = array(
				'wm_type'         => 'overlay',
				'source_image'    => $path.$image,
				'wm_overlay_path' => $path.$this->conf['manage']['image_watermark_file'],
				'wm_position'     => $this->conf['manage']['wm_position'],
				'wm_opacity'       => 50,
				'wm_x_transp'      => 4,
				'wm_y_transp'      => 4,
				);
				switch ($cfg['wm_position'])
        {
          case 'top-left':
            $cfg['wm_vrt_alignment'] = 'top';
            $cfg['wm_hor_alignment'] = 'left';
            break;
          case 'top-right':
            $cfg['wm_vrt_alignment'] = 'top';
            $cfg['wm_hor_alignment'] = 'right';
            break;
          case 'bottom-left':
            $cfg['wm_vrt_alignment'] = 'bottom';
            $cfg['wm_hor_alignment'] = 'left';
            break;
          case 'bottom-right':
            $cfg['wm_vrt_alignment'] = 'bottom';
            $cfg['wm_hor_alignment'] = 'right';
            break;
          default:
            $cfg['wm_vrt_alignment'] = 'middle';
            $cfg['wm_hor_alignment'] = 'center';
          	break;
        }
        $this->img_lib->initialize($cfg);
				$this->img_lib->watermark();
				$this->img_lib->clear();
		}
	}
	function _get_cat($ids)
	{
		if(empty($ids)) return array();
		else{
			$output = array();
			foreach((array)$ids AS $i)
			{
				$output = array_merge($output, $this->_cat($i));
			}
		}
		return array_unique($output);
	}
	function _cat($i)
	{
		if(empty($this->Bbc->r_cat))
		{
			$this->Bbc->r_cat = $this->db->getAssoc("SELECT id, par_id, type_id FROM `bbc_content_cat` ORDER BY type_id, id ASC");
		}
		$output = array();
		if(!empty($this->Bbc->r_cat[$i]))
		{
			$output[] = $i;
			if(!empty($this->Bbc->r_cat[$this->Bbc->r_cat[$i]['par_id']]))
			{
				$output = array_merge($output, $this->_cat($this->Bbc->r_cat[$i]['par_id']));
			}
		}
		return $output;
	}
	function _enum($i)
	{
		$i = intval($i);
		return $i ? 1 : 0;
	}
	private function getUser()
	{
		if (empty($this->user) || empty($this->user->id))
		{
			$usr = $this->db->getRow("SELECT * FROM `bbc_user` WHERE `group_ids` LIKE '%,1,%' ORDER BY `id` ASC LIMIT 1");
			if (empty($usr))
			{
				$usr = $this->db->getRow("SELECT * FROM `bbc_user` WHERE 1 ORDER BY `id` ASC LIMIT 1");
			}
			$obj = new stdClass;
			if (!empty($usr))
			{
				$acc = $this->db->getRow("SELECT * FROM `bbc_account` WHERE `user_id`={$usr['id']} ORDER BY `id` ASC LIMIT 1");
				if (!empty($acc))
				{
					$obj->id    = $usr['id'];
					$obj->name  = $acc['name'];
					$obj->email = $acc['email'];
				}
			}
			if (empty($obj) || empty($obj->id))
			{
				$obj->id    = 1;
				$obj->name  = 'Administrator';
				$obj->email = 'danang@fisip.net';
			}
			return $obj;
		}else{
			return $this->user;
		}
	}
	private function strip($text, $end = 255)
	{
		$text = strip_tags($text, $this->allowed_tags);
		$text = preg_replace('~\s{2,}~is', ' ', $text);
		return substr(trim($text), 0, $end);
	}
}