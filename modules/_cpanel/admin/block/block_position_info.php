<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

include 'block_position-information.php';

$q = "SELECT * FROM bbc_block WHERE id=".intval($_GET['id']);
$dt = $db->getRow($q);
$dt['config'] = config_decode($dt['config']);


// DEFINE CONTENT
$info = array();
$info[]	='<td>Name</td><td>'.$r_ref[$dt['block_ref_id']].'</td>';
$bool		= $dt['show_title'] ? 'Show' : 'Hide';
$info[]	='<td>Title</td><td>'.$bool.'</td>';
$info[]	= '<td>Theme</td><td>'.$r_theme[$dt['theme_id']].'</td>';
if (!empty($dt['config']['template']))
{
	$info[]	= '<td>Template</td><td>'.$dt['config']['template'].'.html.php</td>';
}
$bool		= $dt['active'] ? 'Yes' : 'No';
$info[]	='<td>Published</td><td>'.$bool.'</td>';

$info[]	='<td>Menu to show</td><td>'.list_repair($dt['menu_ids'], $r_menu).'</td>';
if(!empty($dt['menu_ids_blocked']))
	$info[]	='<td>Menu to hidden</td><td>'.list_repair($dt['menu_ids_blocked'], $r_menu).'</td>';
if(!empty($dt['module_ids_allowed']))
	$info[]	='<td>Module allow</td><td>'.list_repair($dt['module_ids_allowed'], $r_module).'</td>';
if(!empty($dt['module_ids_blocked']))
	$info[]	='<td>Module block</td><td>'.list_repair($dt['module_ids_blocked'], $r_module).'</td>';
if(!empty($dt['group_ids']))
	$info[]	='<td>Permision Groups</td><td>'.list_repair($dt['group_ids'], $r_group).'</dd>';
echo '<table class="block_info"><tr>'.implode('</tr><tr>', $info).'</tr></table>';
die();