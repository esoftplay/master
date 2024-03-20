<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$out = array(
		'ok'    => 0,
		'title' => 'no data found'
		);
	$data = $db->getRow("SELECT * FROM `bbc_content_text` WHERE `content_id`={$id} AND `lang_id`=".lang_id());
	if (!empty($data))
	{
		$out = array(
			'ok'          => 1,
			'title'       => $data['title'],
			'description' => $data['description'],
			'url'         => content_link($id, $data['title'])
			);
	}
	output_json($out);
}