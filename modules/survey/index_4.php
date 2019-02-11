<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// CHECK SESSION...
if(!$user->id && $config['def_user_id'] > 0)
{
	$i = intval($config['def_user_id']);
	$q = "SELECT 1 FROM bbc_user WHERE id=$i";
	if($db->getOne($q))
	{
		survey_sess('index_3_id', $i);
		redirect($Bbc->mod['circuit'].'.index_5');
	}
}
if(!isset($sess['index']) || empty($sess['index'])) redirect($Bbc->mod['circuit']);
if(!isset($sess['index_2']) || empty($sess['index_2'])) redirect($Bbc->mod['circuit'].'.index_2');
if($user->id > 0) redirect($Bbc->mod['circuit'].'.index_5');

include_once _ROOT.'modules/user/user_form.php';
echo user_form('survey_particular', 'Insert Your Profile');

function survey_particular()
{
	global $Bbc;
	survey_sess('index_4', $_POST);
	redirect($Bbc->mod['circuit'].'.index_5');
}