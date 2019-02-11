<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$r_tpl = array();
$def_template = 0;
$readDir = dirname($sys->template_dir);
if ($dir = @opendir($readDir)) {
	while (($folder = readdir($dir)) !== false) {
		$thisDir = $readDir.'/'.$folder;
		if(is_file($thisDir.'/index.php')
		&& $folder != '.' 
		&& $folder != '..' 
		&& !preg_match('~admin~', $folder)){
			$r_tpl[] = $folder;
		}
	}
	closedir($dir);
}
asort($r_tpl);
$q = "SELECT id, name FROM bbc_template";
$r_temp = $db->getAssoc($q);
foreach($r_temp AS $temp_id => $dt)
{ // delete unavailable templates
	if(!in_array($dt, $r_tpl))
	{
		$q = "DELETE FROM bbc_template WHERE id=$temp_id";
		$db->Execute($q);
		// DELETE BLOCKS
		$q = "SELECT id FROM bbc_block WHERE template_id=$temp_id";
		$block_ids = $db->getCol($q);
		if($db->Affected_rows())
		{
			$q = "DELETE FROM bbc_block WHERE template_id=$temp_id";
			$db->Execute($q);
			$q = "DELETE FROM bbc_block_text WHERE block_id IN (".implode(',', $block_ids).")";
			$db->Execute($q);
		}
		$q = "DELETE FROM bbc_block_theme WHERE template_id=$temp_id";
		$db->Execute($q);
	}
	if($dt == $_CONFIG['template'])
		$def_template = $temp_id;
}
foreach($r_tpl AS $dt)
{  // insert unsaved templates
	if(!in_array($dt, $r_temp)){
		$q = "INSERT INTO bbc_template SET name='$dt', installed=NOW(), syncron_to=$def_template";
		$db->Execute($q);
		@chmod(_ROOT.'templates/'.$dt.'/css/style.css', 0777);
	}
}
delete_block_file();
redirect($Bbc->mod['circuit'].'.'.$Bbc->mod['task']);
