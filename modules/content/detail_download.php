<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if ($id)
{
	$data = content_fetch($id);
	if (isset($data['kind_id']) && $data['kind_id']==2)
	{
		content_privilege($data);
		$doAction = false;
		$doUpdate = true;
		$_url     = content_link($id, $data['title']);
		if (!empty($data['file_register']))
		{
			$usr = @$_GET['token'];
			$usr = str_replace(' ', '+', $usr);
			$usr = decode($usr);
			$usr = config_decode($usr);
			if (empty($usr['email']))
			{
				redirect($_url);
			}else{
				$q = "SELECT id FROM `bbc_content_registrant` WHERE `email`='".$usr['email']."' AND `content_id`={$id}";
				$I = $db->getOne($q);
				if ($I > 0)
				{
					$doUpdate = false;
					$q = "UPDATE `bbc_content_registrant` SET `created`= NOW() WHERE id={$I}";
				}else{
					$q = "INSERT INTO `bbc_content_registrant` SET
						`content_id` = {$id},
						`name`       = '".addslashes($usr['name'])."',
						`email`      = '".addslashes($usr['email'])."',
						`phone`      = '".addslashes($usr['phone'])."',
						`address`    = '".addslashes($usr['address'])."',
						`created`    = NOW()";
				}
				$db->Execute($q);
				$doAction = true;
			}
		}else{
			if (!empty($_GET['token']))
			{
				$ok = $_GET['token'];
				$ok = str_replace(' ', '+', $ok);
				$ok = decode($ok);
				if ($ok > time())
				{
					$doAction = true;
				}
			}
		}
		if ($doAction)
		{
			if ($doUpdate)
			{
				$arr = array(
					'file_hit'      => ($data['file_hit']+1),
					'file_hit_time' => date('Y-m-d H:i:s'),
					'file_hit_ip'   => $_SERVER['REMOTE_ADDR']
					);
				if (!empty($data['prune']))
				{
					content_file_update($id, $arr);
				}else{
					$q = "UPDATE `bbc_content` SET
						`file_hit`      = '".$arr['file_hit']."',
						`file_hit_time` = '".$arr['file_hit_time']."',
						`file_hit_ip`   = '".$arr['file_hit_ip']."'
						WHERE `id`      = {$id}";
					$db->Execute($q);
				}
			}
			if (!empty($data['file_type']))
			{
				redirect($data['file_url']);
			}else{
				$path = _class('content')->path;
				$file = _ROOT.$path.$id.'/'.$data['file'];
				if (is_file($file) && preg_match('~((?:\.[0-9a-z]{2,5})?\.[0-9a-z]{2,5})$~is', $file, $m))
				{
					_func('download', 'file', menu_save($data['title']).$m[1], $file);
				}
			}
		}
	}
}