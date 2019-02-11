<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

unset($output, $r_list, $data);
$Bbc->exitpage = true;
$data_output   = array(
	'ok'      => 0,
	'message' => 'Maaf anda salah akses'
	);
// Mohon jangan menggunakan module ini untuk untuk di tampilkan di menu public
switch ($Bbc->mod['task'])
{
	case 'main':
		include 'main.php';
		break;
	case 'category':
		include tpl('category.html.php');
		break;
	case 'tag':
		include tpl('tag.html.php');
		break;
	case 'menu':
		include tpl('menu.html.php');
		break;
	case 'search':
		include tpl('search.html.php');
		break;
	default:
		include 'default.php';
		break;
}
if ($Bbc->exitpage)
{
	output_json($data_output);
}