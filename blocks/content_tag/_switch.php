<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan data content tags. Block ini hanya bisa anda gunakan jika tipe website anda adalah 'News Article' bisa anda tentukan di "Content / Configuration" pada tag "Configuration"
/* 'type_id'	=> array(1 => 'Popular Tags', 2 => 'Insert Multiple Tag IDs', 3 => 'Latest Tags') */
$Content = _class('content');
$limit   = @intval($config['limit']);
$tags    = array();
switch (@$config['tag_type'])
{
	case '1': // Popular Tags
		$duration_stamp = strtotime('-'.$config['duration']);
		$sql            = '1';
		if ($duration_stamp < time())
		{
			$date = date('Y-m-d', $duration_stamp);
			$sql = "(`created` > '{$date}' || `updated` > '{$date}')";
		}
		$q = "SELECT * FROM `bbc_content_tag` WHERE {$sql} ORDER BY `total` DESC LIMIT {$limit}";
		$tags = $db->getAll($q);
		break;

	case '2': // Insert Multiple Tag IDs
		$tag_ids = preg_replace('~[^0-9]+~', ' ', $config['tag_ids']);
		$r       = explode(' ', trim($tag_ids));
		$ids     = implode(',', $r);
		if (!empty($ids))
		{
			$q  = "SELECT * FROM `bbc_content_tag` WHERE `id` IN ({$ids})";
			$ar = $db->getAssoc($q);
			// Make sure the order is same as what user fill
			foreach ($r as $i)
			{
				if (!empty($ar[$i]))
				{
					$ar[$i]['id'] = $i;
					$tags[]       = $ar[$i];
				}
			}
		}
		break;

	default: // Latest Tags
		$q = "SELECT * FROM `bbc_content_tag` WHERE 1 ORDER BY `updated` DESC, `created` DESC LIMIT {$limit}";
		$tags = $db->getAll($q);
		break;
}
include tpl(@$config['template'].'.html.php', 'default.html.php');