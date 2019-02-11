<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$data = $db->getRow("SELECT * FROM `bbc_alert` WHERE `id`={$id}");
	$data = _func('alert', 'view', $data);
	if (!empty($data))
	{
		if (empty($data['is_open']) && !empty($data['url']))
		{
			$ok = $db->Execute("UPDATE `bbc_alert` SET `is_open`=1, `updated`=NOW() WHERE `id`={$id}");
		}else $ok = false;
		if (!empty($Bbc->alert_no_redirect))
		{
			output_json(
				array(
					'ok' => $ok ? 1 : 0,
					)
				);
		}
		if (!empty($data['url']))
		{
			$redirect_url = $data['url'];
			if (!empty($_GET['return']))
			{
				$redirect_url .= preg_match('~\?~s', $redirect_url) ? '&' : '?';
				$redirect_url .= 'return='.urlencode($_GET['return']);
			}
			redirect($redirect_url);
		}else{
			echo msg('message cannot be opened', 'danger');
		}
	}
}
if (!empty($_GET['return']))
{
	echo $sys->button($_GET['return']);
}
