<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!defined('_FCM_SENDER_ID') || !defined('_FCM_SERVER_JSON'))
{
	redirect('index.php?mod=_cpanel.user&act=fcm-activate');
}

$tabs = [
	'Sending Message' => '',
	'List Topic'      => '',
	'Create Topic'    => ''
];

ob_start();
include 'fcm_send.php';
$tabs['Sending Message'] = ob_get_contents();
ob_clean();

ob_start();
include 'fcm_create.php';
$tabs['Create Topic'] = ob_get_contents();
ob_clean();

ob_start();
include 'fcm_list.php';
$tabs['List Topic'] = ob_get_contents();
ob_clean();

echo tabs($tabs, $use_cookie = 1, 'topicmanager');