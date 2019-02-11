<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

if(!empty($_POST['Submit']))
{
  $msg = 'No action is executed..';
  $_POST['id'] = @intval($_POST['id']);
  switch(strtolower($_POST['Submit']))
  {
    case 'export':
      param_copy($template_id, $_POST['id']);
      $q = "UPDATE bbc_template SET last_copty_to=".$_POST['id']." WHERE id=$template_id";
      $db->Execute($q);
      $msg = 'Export parameter has completed.';
    break;
    case 'import':
      param_copy($_POST['id'], $template_id);
      $q = "UPDATE bbc_template SET last_copy_from=".$_POST['id'].", syncron_to=0 WHERE id=$template_id";
      $db->Execute($q);
      $msg = 'Import parameter has completed.';
    break;
    case 'syncron':
      param_syncron($template_id, $_POST['id']);
      $msg = 'Parameter syncronization has completed.';
    break;
    case 'reset':
      param_syncron($template_id, '0');
      $msg = 'Parameter has been reset.';
    break;
    case 'syncron to':
      param_syncron($_POST['id'], $template_id);
      $msg = 'Parameter syncronization has completed.';
    break;
    case 'download':
      param_download($template_id, $_POST['name']);
    break;
    case 'upload':
      $msg = param_upload($template_id) ? 'Installing parameter has completed.' : 'Failed to install parameter.';
    break;
  }
  echo msg($msg);
  delete_block_file();
  $db->cache_clean('template.cfg');
}
/*===========================================
 * START TO DECLARE FUNCTION...
 *==========================================*/
function param_copy($from, $to)
{
	global $db;
	$q = "SELECT * FROM bbc_block_theme WHERE template_id=$from";
	$t = $db->getAll($q);
	$q = "SELECT * FROM bbc_block WHERE template_id=$from";
	$b = $db->getAll($q);

	$r_lang = get_lang();
	$q = "SELECT id FROM bbc_block WHERE template_id=$from";
	$id= $db->getCol($q);
	$q = "SELECT * FROM bbc_block_text WHERE block_id IN (".implode(',', (array)$id).")";
	$r = $db->getAll($q); $c = array();
	foreach($r AS $dt)
	{
		$c[$dt['block_id']][$r_lang[$dt['lang_id']]] = $dt['title'];
	}

	$data = array('theme' => $t, 'block' => $b, 'text' => $c);
	param_insert($data, $to);
}
function param_syncron($from, $to)
{
	global $db;
	$q = "UPDATE bbc_template SET syncron_to=$to, last_copty_to=0, last_copy_from=0 WHERE id=$from";
	$db->Execute($q);
}
function param_download($temp_id, $file_name)
{
	global $db;
	$data = _param_download($temp_id);
	foreach((array)$data['block'] AS $i => $dt)
	{
	  $data['block'][$i]['config'] = config_decode($dt['config']);
	}
	header("Cache-control: private"); // fix for IE
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Description: Template Parameters");
	header("Content-Disposition: attachment; filename=\"template_".$file_name.".json\"");
	echo param_encode($data);
	die();
}
function param_upload($temp_id)
{
	$output = false;
	if(isset($_FILES['params']) && is_uploaded_file($_FILES['params']['tmp_name']) && strtolower(substr($_FILES['params']['name'], -5)) == '.json')
	{
		global $db;
		$_file = _ROOT.'images/param_template.json';
		move_uploaded_file($_FILES['params']['tmp_name'], $_file);
		@chmod ($_file, 0777);
		$txt = file_read($_file);
		@unlink($_file);
		$param  = param_decode($txt);
		$output = param_insert($param, $temp_id);
		param_syncron($temp_id, 0);
	}
	return $output;
}
function param_encode($arr, $deep = 0)
{
	if(empty($arr)) return '""';
	$output = '';
	if (defined( 'JSON_PRETTY_PRINT' )) {
		$output = json_encode($arr, JSON_PRETTY_PRINT);
	}else{
		$output = json_encode($arr);
	}
	$output = str_replace('\"', '”', $output);
	return $output;
}
function param_decode($txt)
{
  $txt = str_replace('”','\"',$txt);
  return json_decode($txt, true);
}
function _param_download($temp_id)
{
	global $db;
	$q = "SELECT * FROM bbc_block_theme WHERE template_id=$temp_id";
	$t = $db->getAll($q);
	$q = "SELECT * FROM bbc_block WHERE template_id=$temp_id";
	$b = $db->getAll($q);

	$r_lang = get_lang();
	$q = "SELECT id, name FROM bbc_block_ref WHERE 1";
	$rr= $db->getAssoc($q);
	$q = "SELECT id, name FROM bbc_block_position WHERE 1";
	$ps= $db->getAssoc($q);
	$q = "SELECT id FROM bbc_block WHERE template_id=$temp_id";
	$id= $db->getCol($q);
	$q = "SELECT * FROM bbc_block_text WHERE block_id IN (".implode(',', (array)$id).")";
	$r = $db->getAll($q);
	$c = array();
	foreach($r AS $dt)
	{
		$c[$dt['block_id']][$r_lang[$dt['lang_id']]] = $dt['title'];
	}
	return array('theme' => $t, 'block_ref' => $rr, 'position' => $ps, 'block' => $b, 'text' => $c);
}

function param_insert($param, $temp_id)
{
	global $db;
	if(empty($param))	return false;
	if(!isset($param['theme'])|| empty($param['theme']))return false;
	if(!isset($param['block'])|| empty($param['block']))return false;
	if(!isset($param['text']) || empty($param['text']))	return false;
	$param = addslashes_r($param);
	$q     = "SELECT id FROM bbc_block WHERE template_id=$temp_id";
	$ids   = $db->getCol($q);
	$ids[] = 0;
	$q     = "DELETE FROM bbc_block_text WHERE block_id IN (".implode(',', $ids).")";
	$db->Execute($q);
	$q = "DELETE FROM bbc_block WHERE template_id=$temp_id";
	$db->Execute($q);
	$q = "DELETE FROM bbc_block_theme WHERE template_id=$temp_id";
	$db->Execute($q);
	$r_position = $db->getAssoc("SELECT name, id FROM bbc_block_position WHERE 1");
	$r_ref      = $db->getAssoc("SELECT name, id FROM bbc_block_ref WHERE 1");
	$r_theme    = $r_block = array();
	foreach((array)$param['theme'] AS $data)
	{
		$q = "INSERT INTO bbc_block_theme
				SET template_id	= $temp_id
				,	name					= '".$data['name']."'
				,	content				= '".$data['content']."'
				,	active				= '".$data['active']."'
		";
		$db->Execute($q);
		$r_theme[$data['id']] = $db->Insert_ID();
	}
	foreach((array)$param['block'] AS $data)
	{
		$block_id             = $data['id'];
		$data['theme_id']     = $r_theme[$data['theme_id']];
		$data['template_id']  = $temp_id;
		$data['block_ref_id'] = @$r_ref[$param['block_ref'][$data['block_ref_id']]];
		$data['position_id']  = @$r_position[$param['position'][$data['position_id']]];
		if (!empty($data['block_ref_id']) && !empty($data['position_id']))
		{
			unset($data['id']);
			$sql = array();
			foreach($data AS $field => $value)
			{
			  if(is_array($value))
			  {
			  	$value = stripslashes_r($value);
			    $value = config_encode($value);
			  }
				$sql[] = "`$field` = '$value'";
			}
			$q = "INSERT INTO bbc_block SET ".implode(', ', $sql);
			$db->Execute($q);
			$r_block[$block_id] = $db->Insert_ID();
		}
	}
	// INSERT TITLE OF BLOCK
	$r_lang = array_flip(get_lang());
	foreach((array)$param['text'] AS $block_id => $data)
	{
		if (!empty($r_block[$block_id]))
		{
			foreach($data AS $code => $title)
			{
				if(isset($r_lang[$code]))
				{
					$q="INSERT INTO bbc_block_text
							SET title	= '".$title."'
							,	block_id= ".$r_block[$block_id]."
							,	lang_id	= ".$r_lang[$code];
					$db->Execute($q);
				}
			}
		}
	}
	return true;
}
