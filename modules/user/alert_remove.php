<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if (!empty($id))
{
	$ok = $db->Execute("DELETE FROM `bbc_alert` WHERE `id`={$id}");
	output_json(array('ok' => $ok ? 1 : 0));
}