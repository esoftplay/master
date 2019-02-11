<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$id = @intval($_GET['id']);
$q = "SELECT 1 FROM bbc_user WHERE id=$id";
if($db->getOne($q))
{
	redirect(_URL.'user/force2Login/'.urlencode(urlencode(encode($id))));
}