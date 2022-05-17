<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

require __DIR__.'/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

/**
 * overide images class for Google Cloud Storage
 * please add in config.php
 * define('_IMAGE_STORAGE', 'gcp');
 * define('_IMAGE_CREDENTIAL', '/real/path/gcp_storage.json');
 * define('_IMAGE_PATH', 'domain.com/cgi-bin/help/'); // opsional
 */
require_once _CLASS.'images.php';
class images_class extends images
{
	protected $bucket = null;

	function __construct($path = '', $img = '')
	{
		Parent::__construct($path, $img);
		$storage = new StorageClient(['keyFilePath' => _IMAGE_CREDENTIAL]);
		$this->bucket  = $storage->bucket('bbo-images');
	}

	function move_upload($from, $to='')
	{
		if (empty($to))
		{
			$to = $from;
		}
		if ($from == $to)
		{
			$out = true;
		}else{
			$out = move_uploaded_file($from, $to);
			if (!$out)
			{
				$dir = dirname($to);
				if (!is_dir($dir))
				{
					path_create($dir);
				}
				$out = @rename($from, $to);
			}
		}
		if ($out)
		{
			$dst = $this->_dest($to);
			$src = @fopen($to , 'r');
			if ($src)
			{
				$this->bucket->upload($src, [
					'name'          => $dst,
					'predefinedAcl' => 'publicRead'
				]);
			}
		}
		return $out;
	}

	function delete($img)
	{
		if (file_exists($this->root.$this->path.$img))
		{
			$this->delete($this->root.$this->path.$img);
		}else{
			$is_dir = 0;
			if (is_dir($img))
			{
				$is_dir = 1;
				if (substr($img, -1) != '/')
				{
					$img .= '/';
				}
			}else
			if (substr($img, -1) == '/')
			{
				$is_dir = 1;
			}
			if ($is_dir)
			{
				$r = path_list($img);
				foreach ($r as $file)
				{
					$this->delete($img.$file);
				}
			}else{
				if (is_file($img))
				{
					@chmod($img, 0777);
					unlink($img);
				}
				$file = $this->_dest($img);
				$this->bucket->object($file)->delete();
			}
		}
		return true;
	}

	function move($path, $img='', $imgfrom = '')
	{
		if(!empty($imgfrom))
		{
			$this->img = $imgfrom;
		}
		if(empty($img))
		{
			$img = $this->img;
		}
		$path = preg_replace('~^'.preg_quote($this->root).'~s', '', $path);
		$this->rename($this->root.$this->path.$this->img, $this->root.$path.$img);
	}

	function copying($path, $img='', $imgfrom = '')
	{
		if(!empty($imgfrom))
		{
			$this->img = $imgfrom;
		}
		if(empty($img))
		{
			$img = $this->img;
		}
		$path = preg_replace('~^'.preg_quote($this->root).'~s', '', $path);
		$from = $this->_dest($this->root.$this->path.$this->img);
		$dest = $this->_dest($this->root.$path.$img);
		$obj  = $this->bucket->object($from);
		$obj->copy($dest);
	}

	function rename($oldname, $newname)
	{
		if (is_dir($oldname))
		{
			if (substr($oldname, -1) != '/')
			{
				$oldname .= '/';
			}
			if (substr($newname, -1) != '/')
			{
				$newname .= '/';
			}
			$r = path_list($oldname);
			foreach ($t as $file)
			{
				$this->rename($oldname.$file, $newname.$file);
			}
		}else{
			$dir = dirname($newname);
			if (!is_dir($dir))
			{
				path_create($dir);
			}
			$from = $this->_dest($oldname);
			$dest = $this->_dest($newname);
			$obj  = $this->bucket->object($from);
			$obj->rename($dest);
			return @rename($oldname, $newname);
		}
	}

	private function _dest($path)
	{
		if (defined('_IMAGE_PATH'))
		{
			$out = str_replace(_ROOT, _IMAGE_PATH, $path);
		}else{
			$out = str_replace(_ROOT, config('site', 'url').'/', $path);
		}
		return $out;
	}
}