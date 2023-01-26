<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$Bbc->is_mobile = false;
$Bbc->uri       = $_SERVER['REQUEST_URI'];
if (_SEO && _ADMIN == '')
{
	if(!empty($_GET['mod']))
	{
		die( 'Restricted access' );
	}
	$_seo          = array();
	$_seo['break'] = false;
	$_seo['URI']   = preg_replace('#^'._URI.'#is', '', $_SERVER['REQUEST_URI']);
	$q = "SELECT name FROM bbc_module WHERE is_config=1 AND active=1 ORDER BY id ASC";
	$r = $db->cacheGetCol($q);
	foreach($r AS $f)
	{
		if(is_file(_ROOT.'modules/'.$f.'/_config.php'))
		{
			require _ROOT.'modules/'.$f.'/_config.php';
		}else{
			die('Error: file not found "modules/'.$f.'/_config.php"');
		}
		if ($_seo['break']) {
			break;
		}
	}
	if(preg_match('~\?~is', $_seo['URI']))
	{
		$_seo['URI'] = substr($_seo['URI'], 0, strpos($_seo['URI'], '?'));
		$_seo['add'] = substr(strchr($_seo['URI'], '?'), 1);
		$_seo['add'] = empty($_seo['add']) ? '' : '&'.$_seo['add'];
	}else{
		$_seo['add'] = '';
	}
	$_seo['tmp']['lang']	= lang_assoc('code');
	if(preg_match('~^/?(?:([a-z]{2})/)?~is', $_seo['URI'], $_seo['tmp']['fetch']))
	{
		if(!empty($_seo['tmp']['fetch'][1]) && !empty($_seo['tmp']['lang'][$_seo['tmp']['fetch'][1]]['id']))
		{
			$_seo['URI'] = preg_replace('~^/?[a-z]{2}/?~is', '', $_seo['URI']);
			$_seo['add'] .= '&lang_id='.$_seo['tmp']['lang'][$_seo['tmp']['fetch'][1]]['id'];
			$Bbc->url_prefix = $_seo['tmp']['fetch'][1].'/';
		}
	}
	$_seo['r_URL']= explode('/', $_seo['URI']);
	if (preg_match('~\.html?$~s', $_seo['r_URL'][0]))
	{
		$_seo['start'] = 1; // index path setelah task
	}else{
		$_seo['start'] = 2; // index path setelah task
	}
	if (count($_seo['r_URL']) > $_seo['start'])
	{
		$_seo['tmp'] = $_seo['r_URL'];
		for($_seo['i']=$_seo['start'];$_seo['i'] < count($_seo['tmp']);$_seo['i']++)
		{
			unset($_seo['r_URL'][$_seo['i']]);
			if(!strstr($_seo['tmp'][$_seo['i']], ','))
			{
				$_seo['tmp'][$_seo['i']] = 'id,'.$_seo['tmp'][$_seo['i']];
			}
			$_seo['add'] .= '&'.str_replace(',','=',$_seo['tmp'][$_seo['i']]);
		}
		unset($_seo['tmp'],$_seo['i']);
	}
	unset($_seo['start']);
	if($_seo['r_URL'][0]=='')
	{
		seo_link_request('?menu_id=-1'.$_seo['add']);
	}else{
		preg_match('~^(.*?)\.([a-z0-9]+)~is', $_seo['r_URL'][0], $_seo['tmp']);
		switch(@$_seo['tmp'][2])
		{
			case 'xml':
				seo_link_request('index.php?mod=content.'.$_seo['tmp'][1]);
			break;
			case 'htm':
				$_seo['link'] = 'index.php?mod=content.';
				preg_match('~^(.*?)(?:([\-\_]){1}([0-9]+))?\.htm$~is', $_seo['r_URL'][0], $_seo['match']);
				if(isset($_seo['match'][3]) && is_numeric($_seo['match'][3]))
				{
					$_seo['link'] .= $_seo['match'][2] == '_' ? 'detail' : 'list';
					$_seo['link'] .= '&id='.$_seo['match'][3].'&title='.$_seo['match'][1];
				}
				elseif(!empty($_seo['match'][1]))
				{
					$_seo['link'] .= $_seo['match'][1];
				}else
				$_seo['link']		.= 'main';
				$_seo['link']		.= seo_link_add_request($_seo['r_URL'], ',', 1);
				seo_link_request($_seo['link'].$_seo['add']);
			break;
			case 'html':
				$_seo['r_URL'][1] = preg_replace('~.html$~s', '', $_seo['r_URL'][0]);
				if(!empty($_seo['r_URL'][1]))
				{
					$q = "SELECT `seo`, `id`, `link` FROM bbc_menu WHERE is_admin=0";
					$_seo['tmp']['menu'] = $db->cacheGetAssoc($q, 7200, 'lang/menu_seo_assoc.cfg');
					if(!empty($_seo['tmp']['menu'][$_seo['r_URL'][1]]))
					{
						$_seo['menu']		= $_seo['tmp']['menu'][$_seo['r_URL'][1]];
						$_seo['menu']['link'] .= preg_match('~\?~s', $_seo['menu']['link']) ? '&menu_id='.$_seo['menu']['id'] : '?menu_id='.$_seo['menu']['id'] ;
						seo_link_request($_seo['menu']['link'].$_seo['add']);
					}
				}
			break;
			default:
				if(!preg_match('/^(index.*?\?|\?)/i', $_seo['URI']) and $_seo['URI'] != '')
				{
					$_seo['r_URL'][1] = !empty($_seo['r_URL'][1]) ? '.'.$_seo['r_URL'][1] : '.main';
					$_seo['link'] = 'index.php?mod='.$_seo['r_URL'][0].$_seo['r_URL'][1];
					$_seo['link'].= seo_link_add_request($_seo['r_URL'], ',', 2);
					seo_link_request($_seo['link'].$_seo['add']);
				}
			break;
		}
	}
}else{
	$_seo = array();
	$_seo['URI']	= str_replace(_URI, '', $_SERVER['REQUEST_URI']);
	if(stristr($_seo['URI'], '&mod='))
	{
		die( 'Restricted access' );
	}
	if(empty($_seo['URI']))
	{
		seo_link_request('?menu_id=-1');
	}else
	if(!empty($_GET['mod']) && !preg_match('~\.~s', @$_GET['mod']))
	{
		$_GET['mod'] .= '.main';
	}
}
$_URI = array_merge(array(substr(_URI, 1, -1)), explode('/', $_seo['URI']));unset($_seo);

function site_url($string='', $add_URL = true)
{
	global $Bbc;
	if (empty($Bbc->menu_seo) && !empty($Bbc->menu) && !empty($Bbc->menu->all_array))
	{
		$Bbc->menu_seo = array();
		foreach ($Bbc->menu->all_array as $m)
		{
			if (!empty($m['link']))
			{
				$Bbc->menu_seo[$m['link']] = $m['seo'].'.html';
			}
		}
	}
	if(func_num_args() <= 2 && !empty($Bbc->mod))
	{
		if (function_exists($Bbc->mod['name'].'_'.__FUNCTION__))
		{
			return call_user_func_array($Bbc->mod['name'].'_'.__FUNCTION__, func_get_args());
		}
	}
	if(!empty($Bbc->site_url_arr[$string]))
	{
		return $Bbc->site_url_arr[$string];
	}
	$string1 = preg_replace('#^'._URL.'#is','', $string);
	$string1 = preg_replace('#^'.$Bbc->url_prefix.'#is','', $string1);
	// $string1 = preg_replace('#^([a-z]{2}/)?#is','', $string1);
	if(empty($string1))
	{
		$proccess = false;
	}else
	if(_SEO != 1)
	{
		$proccess = false;
	}else
	if(_ADMIN != '')
	{
		return site_url_admin($string);
	}else
	if(preg_match('~^[a-z]+\://~is',$string1))
	{
		return $string;
	}else
	if(preg_match('~^[a-z]+\:[\sa-z]+~is',$string))
	{
		return $string;
	}else
	if(preg_match('~^#~is',$string1))
	{
		return $string;
	}else
	if(!preg_match('~\?mod=~s', $string1))
	{
		$proccess = false;
		$match = explode('/', '/'.$string1);
		if (count($match) == 3)
		{
			if (!empty($Bbc->menu_seo['index.php?mod='.$match[1].'.'.$match[2]]))
			{
				$string1 = $Bbc->menu_seo['index.php?mod='.$match[1].'.'.$match[2]];
			}
		}
	}else{
		$proccess = true;
	}
	if($proccess && $string1)
	{
		$output = _URL.$Bbc->url_prefix;
		$string = str_replace('&amp;', '&', $string);
		$get    = array();
		preg_match('~mod\=([^\.]+)?\.?(.*?)(?:\&(.*?))?$~is', $string, $match);
		if(!empty($match[3]))
		{
			parse_str($match[3], $get);
		}
		if($match[1]=='content')
		{
			if(in_array($match[2], ['detail', 'list']))
			{
				$s = $match[2] == 'detail' ? '_' : '-';
				if (empty($get['title']))
				{
					$get['title'] = '';
				}
				$output .= menu_save($get['title']).$s.@intval($get['id']).'.htm';
				unset($get['title'], $get['id']);
			}else{
				$output .= $match[2].'.htm';
			}
		}else
		if($match[1]=='rss')
		{
			$output .= 'rss';
			if (empty($get['title']))
			{
				$get['title'] = '';
			}
			if(@intval($get['cat_id']) > 0)
			{
				$output .= '/'.$get['cat_id'].'/'.menu_save($get['title']);
				unset($get['title'], $get['cat_id']);
			}else
			if(@intval($get['content_id']) > 0)
			{
				$output .= '/'.menu_save($get['title']).'_'.$get['content_id'];
				unset($get['title'], $get['content_id']);
			}
		}else{
			if (!empty($Bbc->menu_seo['index.php?mod='.$match[1].'.'.$match[2]]))
			{
				$output .= $Bbc->menu_seo['index.php?mod='.$match[1].'.'.$match[2]];
			}else{
				$output .= $match[1].'/'.$match[2];
			}
		}
		if (!empty($get))
		{
			$r_add = array();
			foreach($get AS $id => $value)
			{
				if (strlen($value) > 12 || preg_match('~[/\?&\%]~s', $value))
				{
					$r_add[] = $id.'='.urlencode($value);
				}else{
					$id      = ($id != 'id') ? $id : '';
					$id     .= !empty($id) ? ',' : '';
					$output .= '/'.$id.urlencode($value);
				}
			}
			if (!empty($r_add))
			{
				$output .= '?'.implode('&', $r_add);
			}
		}
	}else
	if(empty($string1))
	{
		$output = _URL.$Bbc->url_prefix;
	}else{
		$output = _URL;
		if (!file_exists(_ROOT.$string1))
		{
			$output .= $Bbc->url_prefix;
		}
		$output .= $string1;
	}
	$Bbc->site_url_arr[$string] = $output;
	return $output;
}
function site_url_admin($url)
{
	if(preg_match('~^([a-z]+)\/([a-z]+)(.*)$~is', $url, $m))
	{
		$output = 'index.php?mod='.$m[1].'.'.$m[2].str_replace('?', '&', $m[3]);
	}else $output = $url;
	return $output;
}
function seo_url()
{
	global $Bbc;
	return _URL.preg_replace('~^'.preg_quote(_URI, '~').'~is', '', $Bbc->uri);
}
function seo_uri($id = 'none')
{
	global $_URI, $Bbc;
	if(func_num_args() <= 1 && !empty($Bbc->mod))
	{
		if (function_exists($Bbc->mod['name'].'_'.__FUNCTION__))
		{
			return call_user_func_array($Bbc->mod['name'].'_'.__FUNCTION__, func_get_args());
		}
	}
	if (_ADMIN)
	{
		// $output = _URL.$Bbc->url_prefix;
		$URI = $_SERVER['REQUEST_URI'];
		if (!empty($id) && $id != 'none')
		{
			// unset($_GET[$id]);
			$URI = preg_replace('~(&?'.preg_quote($id, '~').'=[^&]+(&)?)~is', '$2', $URI);
		}
		// $output .= '?'.http_build_query($_GET);
		return _URL.preg_replace('~^'._URI.'~s', '', $URI);
	}else
	if(_SEO)
	{
		if(strpos($_URI[1], '.htm') || strpos($_URI[1], '.html'))
		{
			return seo_uri_parse($id);
		}
		if(is_numeric($id)) {
			$output = @$_URI[$id];
		}else{
			$output = _URL.$Bbc->url_prefix;
			unset($_URI[0]);
			if($id == 'none')
			{
				$output .= implode('/', $_URI);
			} else {
				$out = '';
				foreach((array)$_URI AS $d)
				{
					$var = substr($d, 0, intval(strpos($d, ',')));
					if(empty($var)) $var = 'id';
					if($var != $id)
					{
						$out .= '/'.urlencode(urlencode($d));
					}
				}
				$output .= substr($out, 1);
			}
		}
	}else{
		$output = _URL.$Bbc->url_prefix;
		$id = ($id == 'none') ? '' : $id;
		foreach((array)$_GET AS $var => $val)
		{
			if($var != $id)
			{
				$output .= (preg_match('~\?~s', $output)) ? '&' : '?';
				$output .= $var.'='.$val;
			}
		}
	}
	$var_to_add = array();
	foreach ($_GET as $key => $value)
	{
		if (!in_array($key, array('mod','id','menu_id')))
		{
			if (!preg_match('~'.preg_quote($key,'~').'~s', $output))
			{
				$var_to_add[] = $key.'='.urlencode($value);
			}
		}
	}
	if (!empty($var_to_add))
	{
		$output .= '?'.implode('&', $var_to_add);
	}
	return $output;
}
function seo_uri_parse($id)
{
	$output	= 'index.php';
	$out		= array();
	$id			= ($id != 'none') ? array($id) : array();
	$id[]		= 'menu_id';
	$id[]		= 'lang_id';
	foreach((array)$_GET AS $var => $val)
	{
		if(!in_array($var, $id))
		{
			$out[] = $var.'='.urlencode($val);
		}
	}
	if(count($out) > 0) $output .= '?';
	$output .= implode('&', $out);
	return site_url($output);
}

function seo_link_request($link)
{
	global $_GET, $_REQUEST, $_SERVER;
	if(!empty($link))
	{
		$r_get   = $_GET;
		$request = substr(strrchr($link, '?'), 1);
		$r = explode('&', $request);
		$_GET = array();
		foreach($r AS $qry)
		{
			$var = substr($qry, 0, strpos($qry, '='));
			$val = substr(strchr($qry, '='), 1);
			if(!empty($var))
			{
				$_GET[$var] = $_REQUEST[$var] = urldecode($val);
			}
		}
		foreach ($r_get as $key => $value)
		{
			if (!isset($_GET[$key]))
			{
				$_GET[$key] = $_REQUEST[$key] = urldecode($value);
				$link .= '&'.$key.'='.$_GET[$key];
			}
		}
		$_SERVER['QUERY_STRING'] 	= $link;
		$_SERVER['REQUEST_URI'] 	= _URI.$link;
	}
}

function seo_parse($inp, $delim = ',')
{
	if(stristr($inp, $delim)){
		$output = array( substr($inp, 0, strpos($inp, $delim)), substr(strchr($inp, $delim), 1));
	}else{
		$output = array('id', $inp);
	}
	return $output;
}

function seo_link_add_request($inp, $delim = '=', $istart = 0)
{
	$output = '';
	if(!is_array($inp))
	{
		list($var, $val) = seo_parse($inp, $delim);
		if(!empty($var))	$output .= '&'.$var.'='.$val;
	}else{
		$tot = count($inp);
		if($tot > $istart)
		{
			for($i=$istart; $i < $tot;$i++)
			{
				list($var, $val) = seo_parse($inp[$i], $delim);
				if(!empty($var))	$output .= '&'.$var.'='.$val;
			}
		}
	}
	return $output;
}
