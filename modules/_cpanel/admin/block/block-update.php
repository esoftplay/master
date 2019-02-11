<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$r_name  = $db->getAssoc("SELECT id, name FROM bbc_block_ref");
$r_ids   = array_flip($r_name);
$readDir = _ROOT.'blocks/';
if ($dir = @opendir($readDir))
{
	$r_data = array();
	while (($data = readdir($dir)) !== false)
	{
		if(is_file($readDir.'/'.$data.'/_switch.php'))
		{
			$link_to = $data;
			$r_data[] = $data;
		}
	}
	closedir($dir);
}
// UPDATE FROM FOLDER TO DB
foreach($r_data AS $dir)
{
	if(!in_array($dir, $r_name))
	{
		$q = "INSERT INTO bbc_block_ref SET `name`='$dir'";
		$db->Execute($q);
	}
}
// UPDATE FROM DB TO FOLDER
$is_any_delete = false;
foreach($r_name AS $dir)
{
	if(!in_array($dir, $r_data))
	{
		$db->Execute("DELETE FROM bbc_block_ref WHERE `name`='$dir'");
		$block_id = $db->getCol("SELECT id FROM bbc_block WHERE block_ref_id=".@$r_ids[$dir]);
		if (!empty($block_id))
		{
			$ids = implode(',', $block_id);
			$db->Execute("DELETE FROM bbc_block WHERE `id` IN ({$ids})");
			$db->Execute("DELETE FROM bbc_block_text WHERE `block_id` IN ({$ids})");
			$is_any_delete = true;
		}
	}
}
if ($is_any_delete)
{
	$tmp_template_id = @$template_id;
	$r = $db->getCol("SELECT id FROM bbc_template");
	foreach ($r as $i)
	{
		if ($i!=$tmp_template_id)
		{
			$template_id = $i;
			include 'block-repair.php';
		}
	}
	$template_id = $tmp_template_id;
}
include 'block-repair.php';
