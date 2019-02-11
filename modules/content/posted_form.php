<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(content_posted_permission())
{
	$id = @intval($_GET['id']);
	$dt = content_fetch($id, false);

	if($id > 0 && $user->id != @$dt['created_by'])
	{
		$sys->denied();
	}
	$conf    = get_config('content', 'entry');
	$type_id = @intval($conf['type_id']);

	if(isset($_POST['submit_delete']) && $conf['delete'])
	{
		content_delete($dt['id']);
		redirect($Bbc->mod['circuit'].'.posted');
	}
	if(isset($_POST['submit_update']))
	{
		$text		= array(
			'title'		=> $_POST['title'],
			'intro'		=> $_POST['intro'],
			'content' => $_POST['content']
		);
		if($id > 0)
		{
			$text['is_config']= $dt['is_config'];
			$text['config']   = $dt['config'];
			$text['cat_ids']  = isset($_POST['cat_ids']) ? $_POST['cat_ids'] : $dt['cat_ids'];
			$text['publish'] = $dt['publish'];
		}else{
			$text['type_id'] = $type_id;
			$text['cat_ids'] = isset($_POST['cat_ids']) ? $_POST['cat_ids'] : array();
			$text['publish'] = $conf['auto'];
		}
		$text = array_merge($text, $_POST);
		$content_id = $Content->content_save($text, $id);
		if($content_id)
		{
			if(!$id && $conf['alert'])
			{
				$lang_id= lang_id();
				$d	= $Content->_content_data($text);
				$param = array(
					'title'		=> $d['text'][$lang_id]['title']
				,	'intro'		=> $d['text'][$lang_id]['intro']
				,	'content' => $d['text'][$lang_id]['content']
				, 'status'	=> $conf['auto'] ? lang('entry approved auto') : lang('entry approved manual')
				, 'link'		=> $conf['auto'] ? content_link($content_id, $d['text'][$lang_id]['title']) : ''
				);
				extract($param);
				$email = is_email($conf['address']) ? $conf['address'] : config('email', 'address');
				$sys->mail_send($email, 'entry_post');
			}
			echo msg(lang('Succeed to update content'));
		}else{
			echo msg(lang('Failed to update content'));
		}
	}
	if(@$dt['id'] > 0)
	{
		$data  = content_fetch_admin($id);
		$title = lang('entry edit');
	}else{
		$data = array(
			'id'               => '0',
			'par_id'           => !empty($_GET['par_id']) ? intval($_GET['par_id']) : 0,
			'kind_id'          => '0',
			'cat_ids'          => array(),
			'config'           => content_type($type_id, 'detail'),
			'created_by_alias' => $user->name,
			'image'            => '',
			'images'           => '',
			'file_type'        => '0',
			'file_format'      => '',
			'is_popimage'      => '1',
			'is_front'         => '0',
			'is_config'        => '0',
			'publish'          => '1'
		);
		$title								= lang('entry add');
	}
	_func('editor');
	$r_lang     = lang_assoc();
	$content_id = $id;
	$manage     = get_config('content','manage');


	/* START IMAGES CONFIG */
	$tmp_dir = date('YmdHis');
	$path    = 'images/modules/content/'.$content_id.'/';
	$temp    = 'content/'.$tmp_dir;
	$params  = array(
		'path' => array(
			'folder' => $path,
			'tmp'    => $temp,
			),
		'ext'       => array('jpg','gif','png','bmp'),
		'resize'    => @$manage['image_size'],
		'thumbnail' => array(
			'size'   => $data['config']['thumbsize'],
			'prefix' => 'thumb_',
			'is_dir' => 0,
			),
		'folder' => _ROOT.$path,
		'expire' => strtotime('+3 HOUR')
		);
	if (@$manage['image_watermark'] == '1')
	{
		$params['watermark'] = array(
			'wm_overlay_path' => _ROOT.dirname($path).'/'.$manage['image_watermark_file'],
			'wm_position'     => $manage['image_watermark_position'],
			);
	}
	$imgs = (encode(json_encode($params)));
	$temp = str_replace(_ROOT, _URL, _CACHE).$temp.'/';
	$exts = array();
	$rext = content_ext();
	foreach ($rext as $val => $r1)
	{
		foreach ($r1 as $key)
		{
			$exts[$key] = $val;
		}
	}
	/* HAPUS CONTENT CACHE YG LEBIH DARI 3 JAM */
	_func('path');
	$dir_del = _CACHE.'content/';
	$r = path_list($dir_del);
	if (!empty($r))
	{
		sort($r);
		$timelast = date('YmdHis', strtotime('-3 HOUR'));
		$lastdir  = $r[count($r)-1];
		if ($timelast > $lastdir)
		{
			path_delete($dir_del);
		}
	}else{
		path_delete($dir_del);
	}
	include_once 'constants.php';

	include tpl('posted_form.html.php');
}else{
	$sys->denied();
}