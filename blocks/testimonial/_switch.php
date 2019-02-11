<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan daftar testimoni para user yang telah dimasukkan melalui module testimonial, block ini tidak menampilkan semua data jadi anda harus memberi batasan berapa testimoni yang ingin ditampilkan
$output= array();
$output['config']=$config;
$sql = 'ORDER BY ';
switch(@$config['orderby'])
{
	case '2': $sql .= '`id` DESC';break;
	case '3': $sql .= '`id` ASC';break;
	default	: $sql .= 'RAND()';break;
}
$sql .= ' LIMIT '.@intval($config['limit']);
$q = "SELECT * FROM testimonial WHERE publish=1 ".$sql;
$r_list = $db->getAll($q);
if($db->Affected_rows())
{
	foreach((array)$r_list AS $data)
	{
		$output['data'][]= array(
		'email'   => $data['email'],
		'name'    => $data['name'],
		'date'    => $data['date'],
		'message' => $data['message']
		);
	}
}
include tpl(@$config['template'].'.html.php', 'default.html.php');