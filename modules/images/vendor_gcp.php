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
	function move_upload($from, $to)
	{
		if ($from != $to)
		{
			$out = move_uploaded_file($from, $to);
		}else{
			$to  = $from;
			$out = true;
		}
		if ($out)
		{
			if (defined('_IMAGE_PATH'))
			{
				$dst = str_replace(_ROOT, _IMAGE_PATH, $to);
			}else{
				$dst = str_replace(_ROOT, config('site', 'url').'/', $to);
			}
			$this->bucket->upload(fopen($to , 'r'), [
				'name'          => $dst,
				'predefinedAcl' => 'publicRead'
			]);
		}
		return $out;
	}
	function delete($img)
	{
		if(is_file($this->root.$this->path.$img))
		{
			@chmod($this->root.$this->path.$img, $this->perm);
			unlink($this->root.$this->path.$img);
			$img    = $this->root.$this->path.$img;
			$output = true;
		}else
		if(is_file($img))
		{
			@chmod($img, 0777);
			unlink($img);
			$output = true;
		}else{
			$output = false;
		}
		if (defined('_IMAGE_PATH'))
		{
			$dst = str_replace(_ROOT, _IMAGE_PATH, $img);
		}else{
			$dst = str_replace(_ROOT, config('site', 'url').'/', $img);
		}
		$object = $bucket->object($dst);
		$object->delete();
		return true;
	}
}