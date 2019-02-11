<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

#!/includes/function/layout.php
$output = array();
if (!empty($_POST['id'])
	&&!empty($_POST['value'])
	&&!empty($_POST['token']))
{
	$output = array(
		'success' => 0,
		'content' => '',
		'message' => lang('Failed to vote this content')
		);
	extract($_POST);
	$id     = intval($id);
	$value  = intval($value);
	$value  = $value > 0 && $value < 6 ? $value : 1;
	$params = json_decode(decode($token), 1);
	$table  = '';
	$voter  = 'voter';
	if (!empty($params) && is_array($params))
	{
		$table = @$params['table'];
		$voter = @$params['voter'];
		if (!empty($params['db']))
		{
			$db = ${$params['db']};
		}
	}
	$table  = menu_save($table);
	$rating = $db->getOne("SELECT rating FROM `{$table}` WHERE id={$id}");
	if ($db->Affected_rows())
	{
		$r = explode( ',', $rating );
		$o = array();
		for($i=0; $i < 5;$i++)
		{
			$j   = $i + 1;
			$v   = @intval($r[$i]);
			$v  += ($j==$value) ? 1 : 0;
			$o[] = $v;
		}
		$rating = implode(',', $o);
		$q      = "UPDATE `{$table}` SET `rating`='{$rating}' WHERE id={$id}";
		if ($db->Execute($q))
		{
			$output = array(
			'success' => 1,
			'content' => rating($rating, '', '', $voter),
			'message' => lang('Thank you for your rate!')
			);
			$_SESSION['bbc_rating'][$table][$id] = 1;
		}
	}
}
output_json($output);