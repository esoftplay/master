<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function imageslider_delete($ids)
{
	global $db, $Bbc;
	$ids = implode(',', $ids);
	if(!empty($ids))
	{
		$q = "SELECT `image` FROM `imageslider` WHERE `id` IN ($ids)";
		$r = $db->getCol($q);
		foreach($r AS $img)
		{
			if(is_file($Bbc->mod['dir'].$img))
			{
				chmod($Bbc->mod['dir'].$img, 0777);
				unlink($Bbc->mod['dir'].$img);
			}
		}
		$db->Execute("DELETE FROM `imageslider` WHERE `id` IN ($ids)");
		$db->Execute("DELETE FROM `imageslider_text` WHERE `imageslider_id` IN ($ids)");
		imageslider_repair();
	}
}
function imageslider_save($id = 0)
{
  global $db,$Bbc;
  if(!empty($_GET['id']))
  {
    $id = $_GET['id'];
  }
  $q = "SELECT cat_id, image FROM imageslider WHERE id=$id";
  $r = $db->getRow($q);
  $image = $Bbc->mod['dir'].$r['image'];
  if(file_exists($image))
  {
    $cat = $db->getRow("SELECT * FROM imageslider_cat WHERE id=".$r['cat_id']);
    $cfg_resize = array(
      'source_image'  => $image
    ,	'width'         => $cat['width']
    ,	'height'        => $cat['height']
    ,	'maintain_ratio'=> false
    );
    $img = _class('image_lib');
    $img->initialize($cfg_resize);
    $img->resize();
  }
  imageslider_repair();
}
function imageslider_repair()
{
	global $db;
	$i = $cat_id = 0;
	$q = "SELECT id, cat_id, orderby FROM imageslider ORDER BY cat_id, orderby ASC";
	$r = $db->getAll($q);
	foreach($r AS $d)
	{
		$i = ($cat_id != $d['cat_id']) ? 1 : $i+1;
		if($i != $d['orderby'])
		{
			$q = "UPDATE imageslider SET orderby=$i WHERE id=".$d['id'];
			$db->Execute($q);
		}
		$cat_id = $d['cat_id'];
	}
	$db->cache_clean();
}
function imageslider_isdesc()
{
	global $db, $Bbc;
	if (isset($Bbc->imageslider_is_desc))
	{
		return $Bbc->imageslider_is_desc;
	}
	$fields = $db->getCol("SHOW FIELDS FROM imageslider_text");
	$Bbc->imageslider_is_desc = in_array('description', $fields) ? 1 : 0;
	return $Bbc->imageslider_is_desc;
}