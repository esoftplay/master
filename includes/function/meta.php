<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
$action = 1->(pop first); 2->(replace); 3->(pop to end);
*/
function meta_title($txt, $action = 1, $add = ', ')
{
	if(!empty($txt))
	{
		global $_CONFIG;
		switch($action)
		{
			case 1:
				$_CONFIG['site']['title'] = $txt.$add.$_CONFIG['site']['title'];
			break;
			case 3:
				$_CONFIG['site']['title'] = $_CONFIG['site']['title'].$add.$txt;
			break;
			default:
				$_CONFIG['site']['title'] = $txt;
			break;
		}
	}
}

function meta_desc($txt, $action = 2, $add = ', ')
{
	if(!empty($txt))
	{
		global $_CONFIG;
		switch($action)
		{
			case 1:
				$_CONFIG['site']['desc'] = $txt.$add.$_CONFIG['site']['desc'];
			break;
			case 3:
				$_CONFIG['site']['desc'] = $_CONFIG['site']['desc'].$add.$txt;
			break;
			default:
				$_CONFIG['site']['desc'] = $txt;
			break;
		}
	}
}

function meta_keyword($txt, $action = 2, $add = ', ')
{
	if(!empty($txt))
	{
		global $_CONFIG;
		switch($action)
		{
			case 1:
				$_CONFIG['site']['keyword'] = $txt.$add.$_CONFIG['site']['keyword'];
			break;
			case 3:
				$_CONFIG['site']['keyword'] = $_CONFIG['site']['keyword'].$add.$txt;
			break;
			default:
				$_CONFIG['site']['keyword'] = $txt;
			break;
		}
	}
}
function meta_add($text = '')
{
	global $sys, $_CONFIG;
	$meta = array();
	if(!empty($text))
	{
		if(is_array($text))
		{
			$title= !empty($text['title']) ? $text['title'] : $_CONFIG['site']['title'];
			$desc	= !empty($text['description']) ? $text['description'] : (!empty($text['desc']) ? $text['desc'] : $_CONFIG['site']['desc']);
			$url	= !empty($text['url']) ? $text['url'] : seo_uri();
			$type	= !empty($text['type']) ? $text['type'] : 'website';
			$site	= !empty($text['site']) ? $text['site'] : config('site', 'url');
			if(!empty($text['image']))
			{
				$meta[0] = '<link rel="image_src" href="'.$text['image'].'" />';
				$meta[4] = '<meta property="og:image" content="'.$text['image'].'" />';
			}
			$meta[1] = '<meta property="og:title" content="'.$title.'" />';
			$meta[2] = '<meta property="og:type" content="'.$type.'" />';
			$meta[3] = '<meta property="og:url" content="'.$url.'" />';
			$meta[5] = '<meta property="og:site_name" content="'.$site.'" />';
			$meta[6] = '<meta property="og:description" content="'.$desc.'" />';
		}else{
			$meta[] = $text;
		}
		ksort($meta);
		reset($meta);
	}
	$sys->meta_add(implode("\n\t", $meta));
}