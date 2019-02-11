<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Untuk menampilkan informasi kontak anda pada module "contact", bisa juga untuk menampilkan form contact apabila form contact dibuat khusus untuk template anda yang sedang aktif (Control Panel / Site Template)
if (@$config['type'] == 'widget')
{
	include _ROOT.'modules/contact/widget.php';
}else{
	$output = array();
	$conf   = get_config('contact', 'widget');
	if($conf['auto_check'])
	{
		$q = "SELECT * FROM contact_messenger WHERE publish=1 ORDER BY orderby LIMIT ".$conf['ym_show'];
	}else{
		$q = "SELECT * FROM contact_messenger WHERE publish=1 AND online=1 ORDER BY orderby LIMIT ".$conf['ym_show'];
	}
	$r                 = $db->getAll($q);
	$output['data']    = array();
	$output['show_js'] = false;
	$output['js_link'] = site_url('index.php?mod=contact.chat&id=');
	if (!empty($r))
	{
		foreach ($r as $dt)
		{
			$output['data'][] = array(
				'href' => empty($dt['code']) ? 'ymsgr:sendIM?'.$dt['username'] : '#" onclick="return ym_chat('.$dt['id'].');',
				'name' => !empty($conf['name']) ? ' '.$dt['name'] : '',
				'src'  => !empty($conf['auto_check']) ? 'http://opi.yahoo.com/online?u='.$dt['username'].'&m=g&t='.$conf['icon'] : _URL.'modules/contact/images/1/'.$conf['icon'].'.gif'
				);
			if (!empty($dt['code']))
			{
				$output['show_js'] = true;
			}
		}
	}
	$d = get_config('contact', 'form');
	$output['address'] = !empty($conf['address']) ? $d['address'] : '';
	include tpl(@$config['template'].'.html.php', 'contact.html.php');
}