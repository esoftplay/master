<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function get_template_id($template)
{
	include_once _SYS.'layout.blocks.php';
	$b = new blockSystem();
	return $b->get_template_id($template);
}
$add_link = '';
if(!empty($_GET['template_id']))
{
  $tmp_id = intval($_GET['template_id']);
  $q = "SELECT name FROM bbc_template WHERE id={$tmp_id}";
  $tpl = $db->getOne($q);
  if($db->Affected_rows())
  {
    $_CONFIG['template'] = $tpl;
    $add_link = '&template_id='.$tmp_id;
  }
}
$template_id = get_template_id($_CONFIG['template']);
include_once 'delete_block_file.php';
if(!empty($_POST))
{
  delete_block_file($_CONFIG['template']);
}

switch($_GET['act'])
{
	case 'theme':
		include 'theme.php';
		break;
	case 'theme_edit':
		include 'theme_edit.php';
		break;

	case 'block_position':
		include 'block_position.php';
		break;
	case 'block_position_save':
		include 'block_position_save.php';
		break;
	case 'block_position_new':
		include 'block_position_new.php';
		break;
	case 'block_position_edit':
		$id = intval($_GET['id']);
		include 'block_position_edit.php';
		break;
	case 'block_position_info':
		include 'block_position_info.php';
		break;

	case 'edit':
		include 'block_edit.php';
		break;
	case 'edit_field':
		include 'edit_field.php';
		break;

	case 'main':
	default:
		include 'main.php';
		break;
}