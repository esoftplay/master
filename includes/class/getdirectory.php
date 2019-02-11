<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

class getDirectory
{
	var $root;
	var $url;
	var $mainDir;
	var $thisDir;
	var $lastDir;
	function __construct($path = '', $root = '', $url = '')
	{
		$this->root = empty($root) ? _ROOT : $root;
		$this->url  = empty($url) ? _URL : $url;
		$this->setPath($path);
	}
	function setpath($path)
	{
		if(!empty($path)) {
			if(preg_match('#^'.addslashes($this->root).'#is', $path)){
				$path = preg_replace('#^'.addslashes($this->root).'#is', '', $path);
			}
			if(preg_match('#^'.addslashes($this->url).'#is', $path)){
				$path = preg_replace('#^'.addslashes(_URL).'#is', '', $path);
			}
		}
		if(!empty($path)) {
			if(substr($path, 0, 1)=='/') $path = substr($path, 1);
			if(substr($path, -1)!='/'){
				$path .= '/';
			}
		}
		$this->mainDir = $path;
	}
	function dirList($thisDir = '', $order = 'asc')
	{
		$output = array();
		$readDir = $this->root.$this->mainDir.$thisDir;
		if ($dir = @opendir($readDir)) {
			while (($data = readdir($dir)) !== false) {
				if($data != '.' and $data != '..'){
					$output[] = $data;
				}
			}
			closedir($dir);
		}
		if(strtolower($order) == 'desc')		rsort($output);
		else		sort($output);
		reset($output);
		return $output;
	}
	function dirListRecure($thisDir = '', $order = '')
	{
		$out = $this->dirListRecureGet($thisDir, $order);
		$this->output	= array();
		$this->dirListRecureFetch($out);
		$output = $this->output;
		unset($this->output);
		switch(strtolower($order))
		{
			case 'asc': sort($output); break;
			case 'desc':rsort($output); break;
			default: break;
		}
		reset($output);
		return $output;
	}
	function dirListRecureGet($thisDir = '', $order = 'asc')
	{
		$output = array();
		$readDir = $this->root.$this->mainDir.$thisDir;
		if ($dir = @opendir($readDir)) {
			while (($data = readdir($dir)) !== false) {
				if($data != '.' and $data != '..'){
					if(is_file($readDir.'/'.$data)){
						$output[] = array(
							'type' => 'file'
						,	'name' => $data
						, 'path' => str_replace($this->root, '', $readDir.$data)
						);
					}else{
						$output[] = array(
							'type' => 'dir'
						,	'name' => $data
						, 'path' => str_replace($this->root, '', $readDir.$data)
						, 'list' => $this->dirListRecureGet($thisDir.$data.'/', $order)
						);
					}
				}
			}
			closedir($dir);
		}
		if(strtolower($order) == 'desc') rsort($output);
		else		sort($output);
		reset($output);
		return $output;
	}
	function dirListRecureFetch($arr)
	{
		if(!empty($arr))
		{
			if(is_array($arr))
			{
				foreach($arr AS $dir => $data)
				{
					if($data['type']=='file'){
						$this->output[] = $data['path'];
					}elseif($data['type']=='dir'){
						$this->dirListRecureFetch($data['list']);
						$this->output[] = $data['path'];
					}
				}
			}
		}
	}
	function delete($_file)
	{
		$file = $this->root.$this->mainDir.$_file;
		if (file_exists($file)) {
			chmod($file,0777);
			if (is_dir($file)) {
				$handle = opendir($file);
				while($filename = readdir($handle)) {
					if ($filename != "." && $filename != "..") {
						$this->delete($_file."/".$filename);
					}
				}
				closedir($handle);
				rmdir($file);
			} else {
				unlink($file);
			}
		}
	}
	function update($filename, $text)
	{
		$file_path = $this->root.$this->mainDir.'/'.$filename;
		$file = fopen($file_path, "w+");
		fputs($file, $text);
		fclose($file);
	}
}
