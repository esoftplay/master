<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function _cpanel_result($output)
{
	return array(
		'ok'      => 1,
		'message' => 'success',
		'result'  => $output
		);
}
function _cpanel_ads(&$array, $cat_id=0)
{
	global $db;
	$f = $db->getCol("SHOW TABLES LIKE 'bbc_content_ad'");
	if (!empty($f))
	{
		$cat_id  = intval($cat_id);
		$cat_ids = array(0);
		$order   = '';
		if ($cat_id > 0)
		{
			$cat_ids[] = $cat_id;
			$order = '`cat_id` DESC,';
		}
		$db->Execute("UPDATE `bbc_content_ad` SET `active`=0 WHERE `expire`=1 AND `expire_date` < NOW()");
		$ad = $db->getRow("SELECT * FROM `bbc_content_ad` WHERE `cat_id` IN (".implode(',', $cat_ids).") AND `active`=1 ORDER BY {$order} `displayed` ASC LIMIT 1");
		if (!empty($ad))
		{
			$db->Execute("UPDATE `bbc_content_ad` SET `displayed`=NOW() WHERE `id`=".$ad['id']);
			$_URL = str_replace('//data.', '//', _URL);
			switch ($ad['type_id'])
			{
				case '0': // logo & text
				case '1': // banner
					if (!empty($ad['image']))
					{
						$path = 'images/modules/content/ads/';
						if (_class('images')->exists(_ROOT.$path.$ad['image']))
						{
							$ad['image'] = $_URL.$path.$ad['image'];
						}else{
							$ad['image'] = '';
						}
					}
					if ($ad['type_id']=='1')
					{
						$ad['title'] = '';
					}
					break;
				case '2': // text only
					$ad['image'] = '';
					break;
			}
			$array[] = array(
				'id'          => $ad['id'],
				'title'       => $ad['title'],
				'intro'       => '',
				'description' => '',
				'image'       => $ad['image'],
				'created'     => 'sponsor',
				'updated'     => $ad['updated'],
				'url'         => $_URL.'ads.htm?id='.urlencode(encode($ad['id'].'#'.date('Y-m-d H:i:s', strtotime('+2 HOUR')))),
				'publish'     => $ad['active']
				);
		}
	}
}