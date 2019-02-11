<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

# modules/content/detail.php
$sys->no_tpl = 1;
if (!empty($data['publish']))
{
	/* CATEGORY */
	$r = array();
	if (!empty($data['cat_ids']))
	{
		foreach ($data['cat_ids'] as $i)
		{
			$r[] = array(
				'id'    => $i,
				'title' => @$data['cat_names'][$i],
				'url' => content_cat_link($i, @$data['cat_names'][$i])
				);
		}
	}
	$data['cats'] = $r;

	/* TAGS */
	$r = array();
	unset($data['tags']);
	if (config('manage', 'webtype') == '1')
	{
		$q = "SELECT t.id, t.title
						FROM bbc_content_tag_list AS l
						LEFT JOIN bbc_content_tag AS t ON (l.tag_id=t.id)
					WHERE l.content_id=".$data['id'];
		$arr = $db->getAll($q);
		foreach ($arr as $d)
		{
			$r[] = array(
				'id'    => $d['id'],
				'title' => $d['title'],
				'url' => content_tag_link($d['id'], $d['title'])
				);
		}
	}
	$data['tags'] = $r;

	/* RELATED */
	$r   = array();
	$l   = !empty($data['config']['comment_list']) ? $data['config']['comment_list'] : 6;
	$cat = content_related($data['id'], $l);
	if (!empty($cat['list']))
	{
		foreach ($cat['list'] as $d)
		{
			$r[] = array(
				'id'      => $d['id'],
				'title'   => $d['title'],
				'image'   => content_src($d['image']),
				'created' => $d['created'],
				'url'     => content_link($d['id'], $d['title'])
				);
		}
	}
	$data['related'] = $r;

	/* CONTENT KIND */
	$_URL           = preg_replace('~//data\.~is', '//', _URL);
	$data['link']   = '';
	$data['code']   = '';
	switch ($data['kind_id'])
	{
		case '1': // gallery
			  $r = array();
			  $R = json_decode($data['images'], 1);
			  foreach ($R as $v)
			  {
			    if (is_file($Bbc->mod['dir'].$data['id'].'/'.@$v['image']))
			    {
			      $r[] = array(
			        'image'       => $Bbc->mod['image'].$data['id'].'/'.$v['image'],
			        'title'       => @$v['title'],
			        'description' => @$v['description']
			        );
			    }
			  }
			  $data['images'] = $r;
			break;

		case '2': // download
			$data['link'] = $_URL.'detail_download.htm/'.$data['id'].'?token='.urlencode(encode(strtotime('+1 HOUR')));
			break;

		case '3': // video
			$data['code'] = $data['video'];
			break;

		case '4': // audio
			$data['code'] = $data['audio'];
			break;
	}
	if (!is_array($data['images']))
	{
		$data['images'] = array();
	}

	$comment_total = 0;
	if (!empty($config['comment']) && $config['comment']==1)
	{
		$config['comment'] = 1;
		$comment_total     = intval($db->getOne("SELECT COUNT(*) FROM bbc_content_comment WHERE content_id=".$data['id']." AND publish=1"));
	}else $config['comment'] = 0;

	$output = array(
		'id'            => $data['id'],
		'type'          => content_kind($data['kind_id']),
		'url'           => content_link($data['id'], $data['title'], false, $_URL),
		'link'          => $data['link'],
		'code'          => $data['code'],
		'image'         => content_src($data['image'], false, true),
		'caption'       => $data['caption'],
		'images'        => $data['images'],
		'title'         => $data['title'],
		'intro'         => $data['intro'],
		'content'       => $data['content'],
		'description'   => $data['description'],
		'created'       => $data['created'],
		'updated'       => $data['modified'],
		'comment'       => $config['comment'],
		'comment_list'  => intval($config['comment_list']),
		'comment_total' => intval($comment_total),
		'tags'          => $data['tags'],
		'cats'          => $data['cats'],
		'related'       => $data['related']
		);
	$data_output = _cpanel_result($output);
}