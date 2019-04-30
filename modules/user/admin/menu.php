<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$menu_admin = menu_admin();

$out = user_menu_tree($menu_admin['allMenu']);
if (defined('JSON_PRETTY_PRINT'))
{
	$output = json_encode($out, JSON_PRETTY_PRINT);
}else{
	$output = json_encode($out);
}
header('content-type: application/json; charset: UTF-8');
header('cache-control: must-revalidate');
header('expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
echo $output;
exit();
function user_menu_tree($arr, $par_id=0)
{
	$out = array();
	foreach ($arr as $d)
	{
		if ($d[1] == $par_id)
		{
			$isCpanel    = (substr($d[3], 0, 26) == 'index.php?mod=_cpanel.main') ? 1 : 0;
			$childCpanel = (substr($d[3], 0, 22) == 'index.php?mod=_cpanel.') ? 1 : 0;

			$data = array(
				'id'     => 'T'.$d[0],
				'text'   => $d[2],
				'ref'    => $d[3],
				'cls'    => 'file',
				'icon'   => !empty($d[4]) ? _URL.'modules/_cpanel/admin/images/'.$d[4] : _URL.'templates/admin/images/bogus.png',
				'leaf'   => ($isCpanel||!empty($d[5])) ? false : true,
				'cpanel' => $childCpanel,
				);
			if (!empty($d[5]))
			{
				$data['singleClickExpand'] = true;
				if (!$childCpanel)
				{
					unset($data['icon']);
				}
				$data['cls']      = 'folder';
				$data['children'] = call_user_func_array(__FUNCTION__, [$d[5], $d[0]]);
			}else
			if ($isCpanel)
			{
				global $menu_admin;
				$data['singleClickExpand'] = true;
				$data['leaf']              = false;
				$data['cls']               = 'folder';
				$data['icon']              = _URL.'templates/admin/images/gears.gif';
				$data['children']          = call_user_func_array(__FUNCTION__, [$menu_admin['allCpanel'], @$menu_admin['allCpanel'][0][1]]);
			}
			if ($childCpanel && !$isCpanel && !empty($d[5]))
			{
				$tmp = $data;
				unset($tmp['children']);
				$data['id']  .= 'P';
				$tmp['leaf'] = true;
				array_unshift($data['children'], $tmp);
			}
			$out[] = $data;
		}
	}
	return $out;
}

/*
[{
    id: 1,
    text: 'A leaf Node',
    leaf: true
},{
    id: 2,
    text: 'A folder Node',
    leaf: false,
    children: [{
        id: 3,
        text: 'A child Node',
        leaf: true
    }]
}]

[0] => id
[1] => par_id
[2] => title
[3] => link
[4] => icon
[5] => children


*/