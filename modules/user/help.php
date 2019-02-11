<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$url     = '';
$helpURL = 'http://help.fisip.net/';

if (!empty($_GET['url']))
{
	$url = @$_GET['url'];
	$type_id = 1; // Secara default yang dicari adalah type_id=1 (Admin Area)
	require_once _ROOT.'modules/_cpanel/admin/menu/menuQRY.php';
	$url = preg_replace('~((?:&|\?)_?return(?:=|,).*?)$~s', '', $url);
	$url = preg_replace('~^('.preg_quote(_URL, '~').')~s', '', $url);
	if (!preg_match('~^admin/~s', $url))
	{
		$type_id = 2;
		$url     = link_parse($url);
	}
	$url = preg_replace('~\.[0-9]+_~is', '.', $url);
	$url = 'find?id='.urlencode($url).'&type_id='.$type_id;
}else
if (!empty($_GET['id']))
{
	if (@$_GET['dataType']=='json')
	{
		$url = $helpURL.'find/block/'.$_GET['id'].'?dataType=json';
		$ids = json_decode($sys->curl($url), 1);
		output_json($ids);
	}else{
		$url = 'find/block/'.$_GET['id'];
	}
}else{
	$url = 'find';
}

// Tambahkan semua module yang dimiliki
$r = $db->cacheGetCol("SELECT name FROM bbc_module ORDER BY id ASC");
$url .= preg_match('~\?~', $url) ? '&' : '?';
$url .= 'modules='.implode(',', $r);

$sys->stop();
?>
<html>
	<head>
		<title>Bantuan Penggunaan Framework</title>
		<style type="text/css"> body{margin: 0px; padding: 0px;} </style>
	</head>
	<body>
		<iframe src="<?php echo $helpURL.$url;?>" frameBorder="0" width="100%" height="100%" scrolling="auto" allowfullscreen allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>
	</body>
</html>
