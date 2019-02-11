<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$output = array('message' => msg(lang('failed to submit comment'),'danger'), 'success' => '0');
$config = array();
if (!empty($_POST['token']))
{
	@list($token, $config) = _class('comment')->decode($_POST['token']);
	if (!empty($config['db']))
	{
		$db = ${$config['db']};
		unset($config['db']);
	}
}
$output['id'] = @intval($config['id']);
if (!empty($config['id']) && !empty($config['expire']) && $config['expire'] > time())
{
	$post = $_POST;
	_class('comment')->session();
	if (empty($post['name']) && !empty($user->name))
	{
		$post['name'] = $user->name;
	}
	if (empty($post['email']) && !empty($user->email))
	{
		$post['email'] = $user->email;
	}
	if (empty($post['user_id']))
	{
		$post['user_id'] = $user->id;
	}
	$post['image'] = @$post['image'];
	if (empty($post['image']) && !empty($user->image))
	{
		$post['image'] = $user->image;
	}
	$post['user_id']  = intval($post['user_id']);
	$config['par_id'] = @intval($post['par_id']);
	if($config['captcha'] && !_lib('captcha')->Validate())
	{
		$output['message'] = msg(lang('validation code is incorrect'),'danger');
	}else
	if(empty($post['name']))
	{
		$output['message'] = msg(lang('name is empty'),'warning' );
	}else
	if(empty($post['email']))
	{
		$output['message'] = msg(lang('email is empty'),'warning');
	}else
	if(!is_email($post['email']))
	{
		$output['message'] = msg(lang('email is invalid'),'warning');
	}else
	if(str_replace('http://', '', $post['website']) != '' && !is_url($post['website']))
	{
		$output['message'] = msg(lang('url is invalid').pr($post, 1));
	}else
	if(empty($post['content']))
	{
		$output['message'] = msg(lang('comment is empty'));
	}else{
		$post['email'] = strtolower($post['email']);
		if(str_replace('http://', '', $post['website']) == '')
		{
			$post['website'] = '';
		}
		$publish   = $config['approve'] ? 1 : 0;
		$db->debug = 0; // untuk ngecheck judul data yang di comment
		$posfix    = '_comment';
		if (preg_match('~(_[a-z0-9]+)$~is', $config['table'], $m))
		{
			$posfix = $m[1];
		}
		$data = $db->getRow("SELECT * FROM ".str_replace($posfix, '_text', $config['table'])." WHERE ".$config['field']."_id=".$config['id']);
		$data = array_merge($data, $db->getRow("SELECT * FROM ".str_replace($posfix, '', $config['table'])." WHERE id=".$config['id']));
		$pref = $config['field'];
		$q    = "INSERT INTO `".$config['table']."` SET
			`par_id`        = ".$config['par_id'].",
			`user_id`       = ".$post['user_id'].",
			`date`          = NOW(),
			`{$pref}_id`    = '".$config['id']."',
			`{$pref}_title` = '".@addslashes($data['title'])."',
			`name`          = '".addslashes($post['name'])."',
			`image`         = '".addslashes($post['image'])."',
			`email`         = '".$post['email']."',
			`website`       = '".$post['website']."',
			`content`       = '".addslashes($post['content'])."',
			`publish`       = '".$publish."'";
		if($db->Execute($q))
		{
			_func($config['field']); // me-load semua function di module berhubungan
			$id     = $post['id'] = $db->Insert_ID();
			$emails = array($post['email']);

			// Menambah jumlah reply di parent comment
			if ($config['par_id'])
			{
				$a_q = $publish ? ', `reply_on`=(`reply_on`+1)' : '';
				$q   = "UPDATE `".$config['table']."` SET `reply_all`=(`reply_all`+1) {$a_q} WHERE id=".$config['par_id'];
				$db->Execute($q);
			}

			// Jika author juga mendapat notifikasi
			$post['link'] = !empty($config['link']) ? $config['link'] : (function_exists($config['field'].'_link') ? call_user_func($config['field'].'_link', $config['id'], @$data['title']) : '');
			$post['link'] = preg_replace('~\://data\.~s', '://', $post['link']);
			if($config['alert'])
			{
				$e = '';
				$q = '';
				$r = array(
					'created_by' => 'user_id',
					'user_id'    => 'user_id',
					'account_id' => 'id',
					'author'     => 'user_id'
					);
				$user_id = 0;
				foreach ($r as $key => $field)
				{
					if (!empty($data[$key]))
					{
						$i = intval($data[$key]);
						$q = "SELECT `email`, `user_id` FROM `bbc_account` WHERE `{$field}`={$i}";
						$d = $db->getRow($q);
						if (!empty($d))
						{
							$user_id = $d['user_id'];
							if (is_email($d['email']) && $user_id!=$post['user_id'])
							{
								$emails[] = $d['email'];
							}
							break;
						}
					}
				}
				// Author gak perlu di kasih alert kalo dia sendiri yang posting komentar
				if ($user_id!=$post['user_id'])
				{
					_func('alert');
					$post['url']       = $post['link'];
					$post['url_admin'] = 'index.php?mod='.$config['module'].'.comment_edit&id='.$id;
					alert_add(lang('Comment::').' '.$data['title'], $post['content'], $post, $user_id, 0, $config['field']);
				}
			}
			$post['message'] = $post['content'];
			$post['date']    = date(get_config('content', 'rules','content_date'), strtotime('NOW'));
			$post['status']  = !empty($config['approve']) ? msg(lang('comment approved auto'),'success') : msg(lang('comment approved manual'),'success');
			$sys->mail_send($emails, 'comment_post', array_merge((array)@$data, $post));

			$post['message']	= $post['status'];
			$output['success']= '1';
			$output						= array_merge($output, $post);
			$output['content']= preg_replace('~\n~is', '<br />', _func('smiley', 'parse', $output['content']));
			if (empty($output['image']) || !is_url($output['image']))
			{
				$output['image'] = $sys->avatar($output['email'], 1);
			}
			$output['avatar']	= '<img src="'.$output['image'].'" alt="'.$output['name'].'" title="'.$output['name'].'" class="media-object" style="width: 60px;" />';
		}
	}
}
output_json($output);