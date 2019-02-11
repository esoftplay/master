<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (isset($_seo['URI']) && _ADMIN=='')
{
	$Bbc->valid_access = false;
	if (preg_match('~^(?:www\.)?data\.~is', @$_SERVER['HTTP_HOST']))
	{
		$_seo['break']   = 1;
		$_seo['tmp_URI'] = $_seo['URI'];
		$_seo['URI']     = preg_replace('~\?.*?$~', '', $_seo['URI']); // bersihkan URI dari simbol ?
		$r               = explode('/', $_seo['URI']);

		if (preg_match('~^(.*?)\.(html?)$~is', $r[0], $o))
		{
			if ($o[2]=='html')
			{
					$_seo['q']    = "SELECT `link` FROM bbc_menu WHERE `seo`='{$o[1]}' AND `is_admin`=0";
					$_seo['link'] = $db->cacheGetOne($_seo['q']);
					if (preg_match('~\.htm(?:\?.*?)?$~is', $_seo['link']))
					{
						$_seo['URI'] = $_seo['link'];
						$r           = explode('/', $_seo['URI']);
						if (!empty($r) && !preg_match('~^index\.php\?mod=~', $r[0]))
						{
							preg_match('~^(.*?)\.(html?)$~is', $r[0], $o);
						}
					}
					if (preg_match('~index\.php\?(mod=.*?)$~is', $_seo['link'], $o))
					{
						 $_seo['URI'] = preg_replace('~^'.preg_quote(_URL, '~').'~s', '', site_url($_seo['link'], false));
						 $r           = explode('/', $_seo['URI']);
						 preg_match('~^(.*?)\.(html?)$~is', $r[0], $o);
					}
			}
			if ($o[2]=='htm')
			{
				if(preg_match('~^(?:([0-9]+)\-)?(.*?)(?:(\-|_)([0-9]+))?\.htm$~is', $r[0], $_seo['match']))
				{
					// 1 => id tag, 2 => task, 3  => content(_) / category(-), 4 = id content/category
					$r[0] = 'content';
					if(!empty($_seo['match'][1]))
					{
						$r[1]       = 'tag';
						$_GET['id'] = $_seo['match'][0];
					}else
					if (!empty($_seo['match'][3]) && !empty($_seo['match'][4]))
					{
						$_GET['id'] = $_seo['match'][4];
						if ($_seo['match'][3] == '_')
						{
							$r[1] = 'detail';
						}else{
							$r[1] = 'list';
						}
					}else{
						$r[1] = !empty($_seo['match'][2]) ? $_seo['match'][2] : 'type';
					}
				}
			}
		}
		if (!empty($r[0]))
		{
			if (empty($r[1]))
			{
				$r[1] = 'main';
			}
			$_GET['_mod'] = $r[0].'.'.$r[1];
		}
		$r[1] = !empty($r[0]) ? $r[0] : 'main';
		$r[0] = '_cpanel';
		$r2   = explode('/', $_seo['tmp_URI']);
		if (count($r2) > 1)
		{
			foreach ($r2 as $i => $o)
			{
				if ($i > 0)
				{
					$r[] = $o;
				}
			}
		}
		$_seo['URI'] = implode('/', $r);
		/* POST SECURITY */
		if (!empty($_SERVER['HTTP_MASTERKEY']))
		{
			if(_class('crypt')->decode($_SERVER['HTTP_MASTERKEY']) == _URL.substr($Bbc->uri, strlen(_URI)))
			{
				$Bbc->valid_access = true;
			}
		}
		if (!empty($_POST) && !$Bbc->valid_access)
		{
			$_POST = array();
		}
		unset($r,$o);
	}
}
