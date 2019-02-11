<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$data = content_fetch($id);
if(!empty($data['publish']))
{
	$link_page = content_link($data['id'], $data['title']);
	$edit_data = (content_posted_permission() && $user->id == $data['created_by']) ? 1 : 0;
	content_privilege($data, $link_page);

	/* DOWNLOAD ACTION */
	if ($data['kind_id']==2 && !empty($_POST['ok']))
	{
		$output = array(
			'ok' => 0,
			'message' => msg(lang('failed to send message'), 'danger')
			);
		if (!empty($data['file_register']))
		{
			if (!empty($_POST['name'])
				&&!empty($_POST['email'])
				&&!empty($_POST['phone'])
				&&!empty($_POST['address']))
			{
				$_POST['email'] = trim(strtolower($_POST['email']));
				if (!is_email($_POST['email']))
				{
					$output['message'] = msg(lang('email is not valid'), 'danger');
				}else{
					$title = $data['title'];
					$url   = _URL.'detail_download.htm/'.$id.'?token=';
					$url  .= urlencode(encode(config_encode(stripslashes_r($_POST))));
					$sys->mail_send($_POST['email'], 'download_register');
					$output = array(
						'ok' => 1,
						'message' => msg(lang('download link has been sent to your email'), 'success')
						);
				}
			}
		}else{
			$output = array(
				'ok' => 1,
				'url' => _URL.'detail_download.htm/'.$id.'?token='.urlencode(encode(strtotime("+1 HOUR")))
				);
		}
		output_json($output);
	}

	/* START CONTENT */
	meta_title($data['title'], 2);
	meta_desc($data['description'], 2);
	meta_keyword($data['keyword'], 2);
	if(!$data['prune'])
	{
		content_hit($data['id']);
	}
	if(empty($_GET['menu_id']))
	{
		$sys->nav_change($data['title']);
	}
	$config = $data['config'];
	include tpl(@$config['template'], 'detail.html.php');
}else{
	echo msg(lang('not found'));
}