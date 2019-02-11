<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$cfg = array(
	'table'   => 'bbc_content_comment',
	'field'   => 'content',
	'id'      => 0,						// id dari detail data misal content_id
	'par_id'  => 0,						// par_id jika ini reply message maka pastinya par_id > 0
	'type'    => 1,						// [1=Normal Form, 0=No Comment, 2=Facebook Comment]
	'list'    => 9,						// number of comment to show per page
	'form'    => 1,						// show/hide comment form
	'page'    => 0,						// current page
	'captcha' => 0,						// show/hide captcha in form comment if form enable
	'approve' => 1,						// disable/enable auto publish if approve=0 admin must approve every comment manually
	'alert'   => 1,						// disable/enable alert to author of data
	'module'  => 'content',		// module name
	'expire'  => strtotime('+900 SECOND')
	);
foreach ($_GET as $key => $value)
{
	if (isset($cfg[$key]))
	{
		switch ($key)
		{
			case 'table':
			case 'field':
			case 'module':
				$cfg[$key] = preg_replace('~[^a-z0-9\_]+~is', '', $_GET[$key]);
				break;

			default:
				$cfg[$key] = intval($_GET[$key]);
				break;
		}
	}
}
if (!empty($cfg['par_id']))
{
	$_POST['par_id'] = $cfg['par_id'];
}else{
	$_POST['par_id'] = 0;
}
$_POST['token'] = encode(json_encode($cfg));
include _ROOT.'modules/user/comment_post.php';