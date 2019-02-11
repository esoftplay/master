<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @$_GET['id'];
$output = array(
	'ok'     => 0,
	'result' => ''
	);
if (!empty($id) && preg_match('~^[a-z0-9\_]+(?:/admin)?$~is', $id))
{
	if (is_file(_ROOT.'modules/'.$id.'/_switch.php'))
	{
		$result = _cpanel_menu_tasks(_ROOT.'modules/'.$id.'/_switch.php');
		if (!empty($result))
		{
			$output = array(
				'ok'     => 1,
				'result' => $result
				);
		}
	}
}
output_json($output);
function _cpanel_menu_tasks($file)
{
	$out = array();
	$txt = file_read($file);
	if (preg_match('~(?://|#)(?:\s+)?([^\r\n]+)(?:[\r\n\s]+)switch(?:\s+)?\((?:\s+)?\$Bbc\->mod~s', $txt, $m))
	{
		$out['title'] = $m[1];
	}
	if (preg_match_all('~case(?:[^\r\n]+)(?://)([^\r\n]+)~is', $txt, $m))
	{
		if (!empty($m[1]))
		{
			foreach ($m[1] as $i => $title)
			{
				if(preg_match('~case.*?(\w+)~is', $m[0][$i], $m2))
				{
					if (!isset($out['tasks']))
					{
						$out['tasks'] = array();
					}
					$out['tasks'][$m2[1]] = trim($title);
				}
			}
		}
	}
	return $out;
}