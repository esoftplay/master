<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function tools_block_list()
{
	_func('path');
	$r = path_list(_ROOT.'blocks/');
	$output = array();
	foreach($r AS $dir)
	{
		if(is_file(_ROOT.'blocks/'.$dir.'/_switch.php'))
		{
			$output[] = $dir;
		}
	}
	return $output;
}

function tools_block_insert($r_block_dir = array(), $r_block_db = array())
{
	global $db, $sys;
	foreach((array)$r_block_dir AS $dt)
	{
		if(!in_array($dt, $r_block_db))
		{
			$q = "INSERT INTO bbc_block_ref SET name='$dt'";
			$db->Execute($q);
		}
	}
	$sys->clean_cache();
}

function tools_block_delete($r_block_del = array())
{
	if(!empty($r_block_del))
	{
		global $db, $sys;
		$q = "DELETE FROM bbc_block_ref WHERE id IN(".implode(',', $r_block_del).")";
		$db->Execute($q);
		$q = "SELECT id FROM bbc_block WHERE block_ref_id IN(".implode(',', $r_block_del).")";
		$ids = $db->getCol($q);
		if($ids)
		{
			$q = "DELETE FROM bbc_block WHERE id IN(".implode(',', $ids).")";
			$db->Execute($q);
			$q = "DELETE FROM bbc_block_text WHERE block_id IN(".implode(',', $ids).")";
			$db->Execute($q);
			$sys->clean_cache();
		}
	}
}
function tools_block_uninstall($r_block_del = array())
{
	if(!empty($r_block_del))
	{
		global $db, $sys;
		$path = _ROOT.'blocks/';
		$q = "SELECT name FROM bbc_block_ref WHERE id IN(".implode(',', $r_block_del).")";
		$r = $db->getCol($q);
		foreach($r AS $name)
		{
			if(is_dir($path.$name))
			{
				tools_delete($path.$name);
			}
		}
		tools_block_delete($r_block_del);
		$sys->clean_cache();
	}
}
function tools_block_install($id)
{
	$do_delete = true;
	if($id > 0)
	{
		global $db, $sys;
		_func('path');
		$path   = _CACHE;
		$blocks = _ROOT.'blocks/';
		$target = $path.'blocks_temp';
		$q = "SELECT * FROM bbc_block_ref WHERE id=$id";
		$data = $db->getRow($q);
		if($db->Affected_rows())
		{
			if(is_file($path.$data['name']))
			{
				if(!is_dir($target)) path_create($target);
				$unzip	= _class('unzip');
				$unzip->setFileName($path.$data['name']);
#				$unzip->debug=true;
#				$unzip->getList();
				$unzip->unzipAll($target);
				$unzip->close();
				$r = path_list($target);
				$block_name = '';
				foreach($r AS $d)
				{
					if(is_dir($target.'/'.$d)){$block_name = $d; }
				}
				tools_move($target.'/'.$block_name, $blocks.$block_name);
				$q = "UPDATE bbc_block_ref SET name='$block_name' WHERE id=$id";
				$db->Execute($q);
				$do_delete = false;
			}
		}
		$sys->clean_cache();
	}
	if($do_delete)
	{
		$q = "DELETE FROM bbc_block_ref WHERE id=$id";
		$db->Execute($q);
	}
}
