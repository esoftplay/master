<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class Jquery
{
	var $available_widget = array('gui', 'interfaces' => 'interface');
	var $available_plugin	= array('cookie', 'easing', 'tooltip', 'autocomplete', 'imgpreload', 'validate');
	var $version = '1.3.2';
	var $path = 'jquery/';
	var $is_return = false;
	var $is_loaded = array();
	var $dir;
	var $url;

	function __construct()
	{
		global $sys, $Bbc;
		$this->sys =& $sys;
		$this->bbc =& $Bbc;
		$this->dir = _LIB.$this->path;
		$this->url = _URL.'includes/lib/'.$this->path;
	}
	function load($v = '', $is_meta = true)
	{
		if(!isset($this->bbc->jquery_loaded['core'])) {
			$v = $v ? $v : $this->version;
			if(file_exists($this->dir.'jquery-'.$v.'.js'))
			{
				$this->sys->link_js($this->url.'jquery-'.$v.'.js', $is_meta);
			}else{
				$this->sys->link_js('http://ajax.googleapis.com/ajax/libs/jquery/'.$v.'/jquery.min.js', $is_meta);
			}
			$this->bbc->jquery_loaded['core'] = 1;
		}
	}
	function widget($ext = 'gui', $is_meta = true)
	{
		if(in_array($ext, $this->available_widget))
		{
			$this->load('', $is_meta);
			include_once $this->dir.$ext.'/'.$ext.'.php';
			$key = array_search($ext, $this->available_widget);
			$class = (class_exists($key)) ? $key : $ext;
			$out = new $class($is_meta, $this->path.$ext.'/');
			return $out;
		}else{
			die('Widget "'.$ext.'" is not Available');
		}
	}
	function plugin($ext = '', $is_meta = true)
	{
		if(!empty($ext) && in_array($ext, $this->available_plugin))
		{
			$this->load('', $is_meta);
			$this->sys->link_js($this->url.'plugin/jquery.'.$ext.'.js', $is_meta);
			$this->sys->link_js($this->url.'plugin/'.$ext.'/jquery.'.$ext.'.js', $is_meta);
			$this->sys->link_css($this->url.'plugin/'.$ext.'/'.$ext.'.css', $is_meta);
		}
	}
	function facebox($rel='facebox')
	{
		if(!isset($this->is_loaded['facebox'])) {
			$this->load();
			$this->sys->link_js($this->url.'facebox/facebox.js?rel='.$rel);
			$this->sys->link_css($this->url.'facebox/facebox.css');
			$this->is_loaded['facebox'] = 1;
		}
	}
	function menu($arr, $id = 'menu_nav', $layout = 'v'/* h|v*/, $param = array(),  $use_css = true)
	{
		$output = '';
		if(!isset($this->is_loaded['menu'])) {
			$this->load();
			$this->sys->link_js($this->url.'menu/menu.js');
			if($use_css) {
				$this->sys->link_css($this->url.'menu/menu.css');
			}
			$this->is_loaded['menu'] = 1;
		}
		$def_params = array(
			'mainmenuid'		=> $id
		, 'orientation'		=> $layout
		, 'classname'			=> 'menu_nav-'.$layout
#		, 'customtheme'		=> '["#1c5a80", "#18374a"]'
		, 'contentsource'	=> 'markup'
		);
		$params = array_merge($def_params, $param);
		$output = '<div id="'.$id.'" class="menu_nav-'.$layout.'">'.$this->_menu_list($arr).'<br style="clear: left" /></div>';
		$output .= '<script type="text/javascript">ddsmoothmenu.init('.$this->_set_param($params).')</script>';
		return $output;
	}
	function _menu_list($arr, $actived = 'active', $par_id = 0)
	{
		$out		= array();
		$first	= true;
		$class	= '';$i = 0;
		foreach((array)$arr AS $dt) {
			if($dt['par_id'] == $par_id) {
				$i++;
				$class = '';
				if($first) {
					$class .= 'first';
					$first = false;
				}
				if(!empty($actived) && $dt['link'] == str_replace(_URL, '', seo_uri())) {
					$class = (!empty($class)) ? $class.' '.$actived : $actived;
				}
				$class1 = !empty($class) ? ' class="'.$class.'"' : '';
				$out[$i] = '<li'.$class1.'><a href="'.$dt['link'].'" title="'.$dt['title'].'">'.$dt['title'].'</a>'.$this->_menu_list($arr, $actived, $dt['id']).'</li>';
			}
		}
		if($i > 1) {
			$class1 = !empty($class) ? ' class="last '.$class.'"' : ' class="last"';
			$out[$i] = preg_replace('/^(<li(?:[^>]+)?>)/is', '<li'.$class1.'>', $out[$i]);
		}

		if(!empty($out)) {
			$output = '<ul>'.implode(' ', $out).'</ul>';
		}else{
			$output = '';
		}
		return $output;
	}
	/*===========================================
	$param = array(
			'animated' => "slow", "normal", "fast", "milisecond" (To disable animation, remove this option entirely)
		,	'collapsed'=> true, false
		,	'unique'	=> true, false (Sets whether only one tree node can be open at any time, collapsing any previous open nodes.)
		,	'persist'	=> "location|cookie" (To disable persistence, remove this option entirely)
		,	'cookieId'=> "string" (The desired custom cookie name)
		,	'control'	=> jQuery selector
		,	'toggle'	=> callbackfunction
		,	'add'			=> jQuery selector
		,	'prerendered'=> true/false
		,	'url'			=> "source.php" // If defined, starts with an empty tree, then asynchronously adds branches to the tree when requested based on data returned from the server (in JSON format)
	);
	 *=========================================*/
	function tree($arr, $active_class='', $params = array())
	{
		if(!isset($this->is_loaded['tree'])) {
			$this->load();
			$this->sys->link_js($this->url.'tree/tree.min.js', false);
			$this->plugin('cookie', false);
			$this->sys->link_css($this->url.'tree/tree.css', false);
			$this->is_loaded['tree'] = 1;
			$load_js = true;
		}else{
			$load_js = false;
		}
		$def_params = array(
			'collapsed' => true
		, 'persist'		=> 'cookie'
		, 'useicon'		=> true
		, 'iconclose'	=> 'folder.png'
		, 'iconopen'	=> 'folder_explore.png'
		, 'iconfile'	=> 'page_white.png'
		);
		$params = array_merge($def_params, $params);
		$output = $this->_tree_list($arr, $params, $active_class);
		if($load_js)
		{
			unset($params['useicon']
			, $params['iconclose']
			, $params['iconopen']
			, $params['iconfile']);
			$output.= '<script type="text/javascript">
			$(document).ready(function(){
				$(".menu_tree").treeview('.$this->_set_param($params).');
			});</script>';
		}
		return $output;
	}

	function _tree_list($arr, $params = array(), $active_class='', $par_id = 0)
	{
		$out        = array();
		$icon_path  = $this->dir.'tree/';
		$icon_url   = $this->url.'tree/';
		$active_tag = ' class="'.$active_class.'"';
		foreach((array)$arr AS $dt)
		{
			if($dt['par_id'] == $par_id)
			{
				$child = $this->_tree_list($arr, $params, $active_class, $dt['id']);
				if(!empty($active_class))
				{
					$class = (seo_uri() == $dt['link']) ? $active_tag : '';
				}else $class = '';
				if($params['useicon'])
				{
					$title = (empty($child)) ? '<span class="file">'.$dt['title'].'</span>' : '<span class="folder">'.$dt['title'].'</span>';
				}else{
					$title = $dt['title'];
				}
				$out[]= '<li><a href="'.$dt['link'].'" title="'.$dt['title'].'"'.$class.'>'.$title.'</a>'.$child.'</li>';
			}
		}
		if(!empty($out)) {
			$id = ($par_id==0) ? ' class="menu_tree"' : '';
			$output = '<ul'.$id.'>'.implode(' ', $out).'</ul>';
		}else{
			$output = '';
		}
		return $output;
	}
	function _set_param($prm = array(), $def = array())
	{
		$params = array_merge($def, $prm);
		$arr = array();
		foreach((array)$params AS $key => $value) {
			if(is_bool($value)) $value = $value ? 'true' : 'false';
			elseif(is_int($value)) $value = $value;
			elseif (is_array($value)) $value = $this->_set_param($value);
			else $value = '"'.$value.'"';
			$arr[] = $key.': '.$value;
		}
		$output = implode(', ', $arr);
		$output = !empty($output) ? '{'.$output.'}' : '';
		return $output;
	}
}
$Bbc->jquery = $jquery = new jquery();
