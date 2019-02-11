<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$content = _class('content');
$publish= @intval($_POST['publish']);
$text		= array(
	'title'		=> $_POST['title'],
	'content' => $_POST['content'],
	'publish' => $publish
);
if($act == 'edit')
{
	$q = "SELECT content_id FROM agenda WHERE id=$id";
	$content_id = $db->getOne($q);
	$content_id = $content->content_save($text, $content_id);
	$q = "UPDATE `agenda` SET
		`content_id`   = $content_id,
		`start_date`   = '".@$_POST['start_date']."',
		`start_hour`   = '".@$_POST['start_hour']."',
		`start_minute` = '".@$_POST['start_minute']."',
		`end_date`     = '".@$_POST['end_date']."',
		`end_hour`     = '".@$_POST['end_hour']."',
		`end_minute`   = '".@$_POST['end_minute']."',
		`publish`      = $publish
	WHERE `id`=$id";
	$db->Execute($q);
	echo msg('Succeed to update data');
}else{
	$content_id = $content->content_save($text);
	$q = "INSERT INTO `agenda` SET
		`content_id`   = $content_id,
		`cat_id`       = '".$cat_id."',
		`start_date`   = '".@$_POST['start_date']."',
		`start_hour`   = '".@$_POST['start_hour']."',
		`start_minute` = '".@$_POST['start_minute']."',
		`end_date`     = '".@$_POST['end_date']."',
		`end_hour`     = '".@$_POST['end_hour']."',
		`end_minute`   = '".@$_POST['end_minute']."',
		`publish`      = $publish";
	$db->Execute($q);
	echo msg('Succeed to add data');
}