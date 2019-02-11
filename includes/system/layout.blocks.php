<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class blockSystem
{
	var $_db;
	var $modules = array();
	var $is_load = array();
	function __construct($db = 'db')
	{
		$this->_db = $GLOBALS[$$db];
	}
	function get_block_all()
	{
		global $db, $sys, $user, $Bbc, $_CONFIG, $_LANG;
		if (empty($_CONFIG['site'])) {
			redirect(_URL.'user/repair/serialize_json?redirect='.urlencode(seo_uri()));
		}
		$block_output = array();
		$Bbc->currDir = getcwd().'/';
		if($_CONFIG['rules']['lang_auto']) $Bbc->module_id_from = $sys->module_id;
		$q = "
			SELECT b.*, x.title, t.content, t.name AS theme_block, p.name AS position_name
			, r.name AS ref_name FROM bbc_block AS b
			LEFT JOIN bbc_block_text AS x ON (b.id=x.block_id AND lang_id=".lang_id().")
			LEFT JOIN bbc_block_ref AS r ON(r.id=b.block_ref_id)
			LEFT JOIN bbc_block_theme AS t ON(t.id=b.theme_id)
			LEFT JOIN bbc_block_position AS p ON(p.id=b.position_id)
			WHERE b.active=1 AND b.template_id=".$this->get_template_id($_CONFIG['template'])."
			ORDER BY b.position_id, b.orderby";
		$file                = 'blocks/'.$_CONFIG['template'].'_'.lang_id().'.cfg';
		$r                   = $this->_db->cache('getAll', $q, $file);
		$module_real_task    = $Bbc->mod['task'];
		$sys->module_id_real = $sys->module_id;
		foreach($r AS $block_dt)
		{
			$passed = $this->is_allow($block_dt['menu_ids'], $sys->menu_id, $sys->menu_real);
			if(!$passed)$passed = $this->is_allow($block_dt['module_ids_allowed'], $sys->module_id_real);
			if($passed)	$passed = $this->is_allow($block_dt['menu_ids_blocked'], $sys->menu_id, $sys->menu_real) ? false : true;
			if($passed && $this->is_allow($block_dt['module_ids_blocked'], $sys->module_id_real))
				$passed = false;
			if($passed){
				$is_logged = (@intval($user->id) > 0) ? true : false;
				$passed = $this->group_allow($block_dt['group_ids'], @$user->group_ids, $is_logged);
			}
			if($passed)
			{
				preg_match('~^(.*?)(?:_[^_]+)?$~', $block_dt['ref_name'], $m);
				$block			= new stdClass();
				$tmp				= 'blocks/'.$block_dt['ref_name'].'/';
				$block->id	= $block_dt['id'];
				$block->dir = _ROOT.$tmp;
				$block->url = _URL.$tmp;
				@chdir($block->dir);
				$sys->module_change($m[1]);
				$Bbc->mod['task'] = $module_real_task;
				ob_start();
				if(is_file('_switch.php'))
				{
					$config = config_decode($block_dt['config']);
					$block->title = $block_dt['show_title'] ? !empty($block_dt['link']) ? '<a href="'.site_url($block_dt['link']).'" title="'.$block_dt['title'].'">'.$block_dt['title'].'</a>' : $block_dt['title'] : '';
					if(file_exists('_config.php')) include '_config.php';
					if($block_dt['cache'] > 0)
					{
						$block_dt['file']= _CACHE.'blocks/'.$block->id.'_'.lang_id().'.cfg';
						$block_dt['exp'] = strtotime('-'.$block_dt['cache'].' SECOND');
						$block_dt['arr'] = config_decode(file_read($block_dt['file']));
						if(isset($block_dt['arr'][0]) && $block_dt['arr'][0] > $block_dt['exp'])
						{
							$block->content = @$block_dt['arr'][1];
						}else{
							include '_switch.php';
							$block->content = ob_get_contents();
							$block_dt['arr'] = array(strtotime('NOW'), $block->content);
							file_write($block_dt['file'], json_encode($block_dt['arr']));
						}
					}else{
						include '_switch.php';
						$block->content = ob_get_contents();
					}
				}else $block->content = 'none';
				ob_end_clean();
				if(_SEO)$block_output[strtolower($block_dt['position_name'])][] = preg_replace_callback($Bbc->regex, 'module_replace', $this->text_replace($block_dt['content'], $block));
				else		$block_output[strtolower($block_dt['position_name'])][] = $this->text_replace($block_dt['content'], $block);
				$sys->module_clear();
			}
		}
		if($_CONFIG['rules']['lang_auto']) $sys->module_id = $Bbc->module_id_from;
		chdir($Bbc->currDir);
		return $block_output;
	}
	function get_template_id($template)
	{
		global $db;
		$file = _CACHE.'template_'.$template.'.cfg';
		if(is_file($file))
		{
			$data = config_decode(file_read($file));
		}else{
			$q = "SELECT * FROM bbc_template WHERE name='".$template."'";
			$data = $this->_db->getRow($q);
			if($this->_db->Affected_rows())
			{
				if($data['syncron_to'] > 0 && $data['syncron_to'] != $data['id'])
				{
					$q = "SELECT name FROM bbc_template WHERE id=".$data['syncron_to'];
					$temp_name = $this->_db->getOne($q);
					if($this->_db->Affected_rows())
					{
						return $this->get_template_id($temp_name);
					}
				}
				file_write($file, json_encode($data));
			}
		}
		return @intval($data['id']);
	}
	function delete_block_file()
	{
		global $sys;
		$sys->clean_cache();
	}
	function group_allow($value, $group_ids, $is_logged)
	{
		global $sys;
		$output = false;
		if($value == ',all,'){
			$output = true;
		}else{
			$arr = repairExplode($value);
			if($is_logged)
			{
				$output = in_array('logged', $arr) ? true : false;
				if(!$output)
				{
					foreach((array)$group_ids AS $group_id)
					{
						if(in_array($group_id, $arr))
						{
							$output = true;
							break;
						}
					}
				}
			}else
			if(in_array('unassigned', $arr))
			{
				$output = true;
			}
		}
		return $output;
	}
	function is_allow($value, $id, $config = true)
	{
		global $sys;
		$output = false;
		if($value == ',all,'){
			$output = true;
		}else{
			$arr = repairExplode($value);
			if(!$config){ // $sys->menu_real OR $user->group_id
				if(in_array('unassigned', $arr)){
					$output = true;
				}else{
					$output = false;
				}
			}else{
				if(in_array($id, $arr)){
					$output = true;
				}else{
					$output = false;
				}
			}
		}
		return $output;
	}
	function text_replace( $c, $b)
	{
		$r	= '#\[((?:\s{0,}<[^>]+>){0,}\s{0,})(\w+)((?:\s{0,}<[^>]+>){0,}\s{0,})\]#is';
		$r_c = $r_t = array();
		if(!is_array($c))	$r_c[0] = $c;
		else	$r_c = $c;
		foreach($r_c AS $_i => $t)
		{
			preg_match_all( $r, $t, $m );
			$o = array();
			foreach( $m[2] as $i => $v)
			{
				$v	= strip_tags($v);
				$_r = (empty($b->$v)) ? '' : $m[1][$i].trim($b->$v).$m[3][$i];
				$t	= str_replace( $m[0][$i], $_r, $t );
			}
			$r_t[$_i] = $t;
		}
		if(!is_array($c))	$output = $r_t[0];
		else	$output = $r_t;
		return $output;
	}
}
