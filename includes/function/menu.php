<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function menu_seo($seo, $title = '', $menu_id = '0')
{
	global $db;
	$output  = '';
	$menu_id = intval($menu_id);
	$seo     = !empty($seo) ? menu_save($seo, false) : menu_save($title, false);
	$seo     = !empty($seo) ? $seo : $menu_id;
	$q       = "SELECT '1' FROM bbc_menu WHERE `id` != $menu_id AND is_admin=0 AND `seo`='$seo'";
	if($db->getOne($q)){
		$output = menu_seo($seo.$menu_id, $title, $menu_id);
	}else{
		$output = $seo;
	}
	return $output;
}

function menu_save($txt, $url = false, $replace='')
{
	$_url = $url ? _URL : '';
	if (empty($replace))
	{
		$replace = menu_delimiter();
	}
	$output = $_url.strtolower(preg_replace( array('~\&~i','~[^a-z0-9\-_]~i','~'.preg_quote($replace, '~').'{2,}~i'), array('n',$replace,$replace), trim($txt)));
	return trim($output, $replace);
}

function menu_delimiter()
{
	$s = config('rules', 'uri_separator');
	if (!empty($s) && is_string($s))
	{
		$o = $s;
	}else{
		$o = '_';
	}
	return $o;
}

function menu_delete($ids)
{
	if(!empty($ids))
	{
		global $db;
		$r_menu = menu_fetch_recure($ids);
		if(count($r_menu) > 0)
		{
			$r = array_chunk($r_menu, 10);
			foreach ($r as $is)
			{
				ids($is);
				$db->Execute("DELETE FROM bbc_menu WHERE id IN({$is})");
				$db->Execute("DELETE FROM bbc_menu_text WHERE menu_id IN({$is})");
			}
			foreach($r_menu AS $id)
			{
				$q = "UPDATE bbc_user_group SET menus=REPLACE(menus, ',$id,', ',') WHERE menus LIKE '%,$id,%'";
				$db->Execute($q);
			}
			$q = "UPDATE bbc_user_group SET menus='' WHERE menus=','";
			$db->Execute($q);
			menu_repair();
			return true;
		}
	}else return false;
}

function menu_fetch_recure($ids, $add = array())
{
	global $db;
	$ids = (is_array($ids) && count($ids) > 0) ? $ids : array($ids);
	$r = array();
	if(!empty($ids))
	{
		if(in_array(0, $ids))
		{
			$r = array();
			foreach($ids AS $i)
			{
				if($i > 0)
				{
					$r[] = $i;
				}
			}
			$ids = $r;
		}
		$rr = array_chunk($ids, 10);
		foreach ($rr as $is)
		{
			ids($is);
			$q = "SELECT id FROM bbc_menu WHERE par_id IN({$is}) ORDER BY id";
			$r = array_merge($r, $db->getCol($q));
		}
	}
	$output = array_unique(array_merge($ids, $add));
	if(count($r) > 0){
		return call_user_func(__FUNCTION__, $r, $output);
	}else{
		return $output;
	}
}
/*===========================================
Mengambil ID menu yang perlu di highlight untuk ditampilkan pada layout
biasanya diambil dari URL saat itu
 *=========================================*/
function menu_parent_ids($id, $arr_menu=array(), $out = array())
{
	global $Bbc;
	if (!empty($Bbc->menu_parent_ids[$id]))
	{
		return $Bbc->menu_parent_ids[$id];
	}
	if (!empty($arr_menu))
	{
		foreach ($arr_menu as $m)
		{
			if ($m['id'] == $id)
			{
				$out[] = $id;
				if (!empty($m['par_id']))
				{
					$out = call_user_func_array(__FUNCTION__, [$m['par_id'], $arr_menu, $out]);
					break;
				}
			}
		}
	}
	$Bbc->menu_parent_ids[$id] = $out;
	return $out;
}

function menu_repair()
{
	global $db;
	$i = 1;
	$last = array(
		'is_admin'=> '0'
	,	'cat_id'	=> '1'
	,	'par_id'	=> '0'
	);
	$db->Execute("UPDATE bbc_menu SET cat_id=1 WHERE is_admin=1");
	$q="SELECT id, par_id, orderby, is_admin, cat_id FROM bbc_menu
			ORDER BY is_admin, cat_id, par_id, orderby, id";
	$r = $db->getAll($q);
	foreach($r AS $data)
	{
		if($last['is_admin']!= $data['is_admin']) $i = 1;
		if($last['cat_id']	!= $data['cat_id']) $i = 1;
		if($last['par_id']	!= $data['par_id']) $i = 1;
		if($i != $data['orderby'])
		{
			$q = "UPDATE bbc_menu SET orderby=$i WHERE id=".$data['id'];
			$db->Execute($q);
		}
		$last = $data;
		$i++;
	}
	lang_refresh();
}
/*===========================================
 * $menus = array(
 							  'id'		=> 0
 							, 'par_id'=> 0
 							, 'title'	=> [text]
 							, 'link'	=> [url]
 							);
*	 $y = down || top
*	 $x = right || left
 *=========================================*/
function menu_horizontal($menus, $y='', $x='', $level = -1) // $y = 'down' || 'top' AND $x = 'right'|| 'left'
{
	$output = '';
	if (!empty($menus))
	{
		$highlight = menu_parent_ids(@$_GET['menu_id'], $menus);
		if ($level == -1)
		{
			$output = call_user_func(__FUNCTION__, menu_parse($menus), $y,$x,++$level);
		}else
		if (empty($level))
		{
			$cls = !empty($y) ? ' nav-'.$y : '';
			$cls.= !empty($x) ? ' nav-'.$x : '';
			$out = '';
			foreach ($menus as $menu)
			{
				$sub = call_user_func(__FUNCTION__, $menu['child'], $y,$x,++$level);
				if (!empty($sub))
				{
					$act = in_array($menu['id'], $highlight) ? ' active' : '';
					$out .= '<li class="dropdown'.$act.'"><a role="button" data-toggle="dropdown" tabindex="-1" href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].' <b class="caret"></b></a>'.$sub.'</li>';
				}else{
					$act = in_array($menu['id'], $highlight) ? ' class="active"' : '';
					$out.= '<li'.$act.'><a href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a></li>';
				}
			}
			$output = '<ul class="nav navbar-nav'.$cls.'">'.$out.'</ul>';
		}else {
			$out = '';
			foreach ($menus as $menu)
			{
				$sub = call_user_func(__FUNCTION__, $menu['child'], $y,$x,++$level);
				if (!empty($sub))
				{
					$act = in_array($menu['id'], $highlight) ? ' active' : '';
					$out .= '<li class="dropdown-submenu'.$act.'"><a tabindex="-1" href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a>'.$sub.'</li>';
				}else{
					$act = in_array($menu['id'], $highlight) ? ' class="active"' : '';
					$out.= '<li'.$act.'><a href="'.$menu['link'].'" title="'.$menu['title'].'">'.$menu['title'].'</a></li>';
				}
			}
			$output = '<ul class="dropdown-menu" role="menu">'.$out.'</ul>';
		}
	}
	return $output;
}
function menu_vertical($menus, $level = -1, $id='')
{
	$output = '';
	if (!empty($menus))
	{
		$highlight = menu_parent_ids(@$_GET['menu_id'], $menus);
		if ($level == -1)
		{
			$output = call_user_func(__FUNCTION__, menu_parse($menus), ++$level);
		}else
		if (empty($level))
		{
			global $Bbc;
			if (empty($Bbc))
			{
				$Bbc = new stdClass;
			}
			if (empty($Bbc->menu_vertical))
			{
				$Bbc->menu_vertical = 1;
			}else{
				$Bbc->menu_vertical++;
			}
			$id = 'menu_v'.$Bbc->menu_vertical;
			$out = '';
			foreach ($menus as $menu)
			{
				$sub = call_user_func(__FUNCTION__, $menu['child'], ++$level, $id);
				$act = in_array($menu['id'], $highlight) ? ' active' : '';
				$alt = trim(strip_tags($menu['title']));
				if (!empty($sub))
				{
					$out .= '<a href="#'.$id.$level.'" class="list-group-item'.$act.'" data-toggle="collapse" data-parent="#'.$id.'" title="'.$alt.'">'.$menu['title'].' <span class="caret down"></span></a>';
					$out .= $sub;
				}else{
					$out .= '<a href="'.$menu['link'].'" class="list-group-item'.$act.'" data-parent="#'.$id.'" title="'.$alt.'">'.$menu['title'].'</a>';
				}
			}
			$output = '<div id="'.$id.'"><div class="list-group">'.$out.'</div></div>';
		}else {
			$id .= $level;
			$out = '';
			$in  = '';
			foreach ($menus as $menu)
			{
				$sub = call_user_func(__FUNCTION__, $menu['child'], ++$level, $id);
				$act = in_array($menu['id'], $highlight) ? ' active' : '';
				$alt = trim(strip_tags($menu['title']));
				if ($act)
				{
					$in = ' in';
				}
				if (!empty($sub))
				{
					$out .= '<a href="#'.$id.$level.'" class="list-group-item'.$act.'" data-toggle="collapse" data-parent="#'.$id.'" title="'.$alt.'">'.$menu['title'].' <span class="caret down"></span></a>';
					$out .= $sub;
				}else{
					$out .= '<a href="'.$menu['link'].'" class="list-group-item'.$act.'" data-parent="#'.$id.'" title="'.$alt.'">'.$menu['title'].'</a>';
				}
			}
			$output = '<div id="'.$id.'" class="collapse'.$in.' list-group-submenu">'.$out.'</div>';
		}
	}
	return $output;
}
/*===========================================
 * OLD FASHION MENU
 * $r_menu[] = array(
 							  'id'		=> 0
 							, 'par_id'=> 0
 							, 'title'	=> [text]
 							, 'link'	=> [url]
 							);
 * $layout = 'vertical|horizontal bottom|top left|right';
 * $b_id = $block->id
 *=========================================*/
function menu_list($r_menu, $layout = 'vertical top right', $b_id = 1)
{
	global $sys, $Bbc;
	$output = '';
	if (!empty($r_menu))
	{
		if (!is_numeric($layout))
		{
			$Bbc->menu_list = isset($Bbc->menu_list) ? $Bbc->menu_list : 0;
			if(!empty($r_menu))
			{
				$Bbc->menu_list++;
				$l = array(
				 0 => preg_match('/vert/i', $layout)	? 'v' : 'h'
				,1 => preg_match('/top/i', $layout)		? 't' : 'b'
				,2 => preg_match('/left/i', $layout)	? 'l' : 'r'
				);
				$output .= '<div id="MM'.$b_id.$Bbc->menu_list.'">';
				$output .= call_user_func(__FUNCTION__, $r_menu, 0);
				$output .= '<script type="text/javascript">'
								.	 'cmDrawFromText("MM'.$b_id.$Bbc->menu_list.'", "'.implode('', $l).'", cmThemeOffice, "ThemeOffice");'
								.	 '</script></div>';
				$sys->link_js(_URL.'includes/function/js/menu/menu.js', false);
			}
		}else{
			$out    = array();
			$par_id = $layout;
			foreach((array)$r_menu AS $dt) {
				if($dt['par_id'] == $par_id) {
					$out[] = '<li><span></span><a href="'.$dt['link'].'">'.$dt['title'].'</a>'.call_user_func(__FUNCTION__, $r_menu, $dt['id']).'</li>';
				}
			}
			if(!empty($out)) {
				$output = '<ul>'.implode(' ', $out).'</ul>';
			}
		}
	}
	return $output;
}
function menu_ulli($arr, $par_id = 0, $attr_ul = '', $attr_li = '')
{
	$out		= array();
	foreach((array)$arr AS $dt)
	{
		if($dt['par_id'] == $par_id)
		{
			$out[] = '<li '.$attr_li.'><a href="'.$dt['link'].'" title="'.$dt['title'].'">'.$dt['title'].'</a>'.call_user_func(__FUNCTION__, $arr, $dt['id'], $attr_ul, $attr_li).'</li>';
		}
	}
	if(!empty($out))
	{
		$output = '<ul '.$attr_ul.'>'.implode(' ', $out).'</ul>';
	}else{
		$output = '';
	}
	return $output;
}
function menu_parse($arr, $par_id=0)
{
	$out  = array();
	foreach ($arr as $d)
	{
		if ($d['par_id']==$par_id)
		{
			$d['child'] = call_user_func(__FUNCTION__, $arr, $d['id']);
			$out[] = $d;
		}
	}
	return $out;
}

function menu_nav_tree($arr, $active_class='', $params = array())
{
	$jquery = _lib('jquery');
	return $jquery->tree($arr, $active_class, $params);
}

function menu_nav($arr, $id = 'menu_nav', $layout = 'v'/* h|v*/, $param = array(), $use_css = false)
{
	$jquery = _lib('jquery');
	return $jquery->menu($arr, $id, $layout,  $param, $use_css);
}
function menu_admin($allMenu=array(), $allCpanel=array())
{
	$output = array();
	if (empty($allMenu))
	{
		$allMenu = (array)@$GLOBALS['Bbc']->menu->left;
	}
	if (empty($allCpanel))
	{
		$allCpanel = (array)@$GLOBALS['Bbc']->menu->cpanel;
	}
	if (!empty($allMenu))
	{
		if (!empty($allCpanel))
		{
			$lastID = $GLOBALS['db']->getOne("SELECT id FROM bbc_menu ORDER BY id DESC LIMIT 1");
		}else{
			$lastID = 0;
		}
		$i      = 12;
		foreach($allMenu AS $d)
		{
			if($d['par_id'] == 0)
			{
				$i += 24;
			}
		}
		$height = $i > 500 ? 500 : ($i < 300 ? 300 : $i);
		$output = array(
		 'username'  => $GLOBALS['user']->name
		,'allMenu'   => menu_admin_parse($allMenu, 0)
		,'allCpanel' => menu_admin_parse($allCpanel, $lastID)
		,'height'    => $height
		);
	}
	return $output;
}
function menu_admin_parse($menus, $lastID = 0, $par_id = 0)
{
	$output = array();
	$_func  = __FUNCTION__;
	foreach($menus AS $i => $d)
	{
		if($par_id == $d['par_id'])
		{
			$d['id']     += $lastID;
			$d['par_id'] += $lastID;
			if (!$lastID)
			{
				$d['link'] .= '&admin_id='.$d['id'];
				$d['image'] = $d['seo'];
			}else{
				$d['link'] .= '&_admin_id='.$d['id'];
			}
			if (!empty($d['is_shortcut']))
			{
				if (empty($GLOBALS['Bbc']->shortcut))
				{
					$GLOBALS['Bbc']->shortcut = array();
				}
				$GLOBALS['Bbc']->shortcut[] = array($d['id'],$d['title'],$d['image']);
			}
			$out = array(	$d['id'], $d['par_id'], $d['title'], $d['link'], $d['image'], $_func($menus, $lastID, $menus[$i]['id']));
			$output[] = $out;
		}
	}
	return $output;
}
