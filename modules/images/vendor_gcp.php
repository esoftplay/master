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
		$out = move_uploaded_file($from, $to);
		if ($out)
		{
			if (defined('_IMAGE_PATH'))
			{
				$dst =	 str_replace(_ROOT, _IMAGE_PATH, $to);
			}else{
				$dst =	 str_replace(_ROOT, config('site', 'url').'/', $to);
			}
			$this->bucket->upload(fopen($to , 'r'), [
				'name'          => $dst,
				'predefinedAcl' => 'publicRead'
			]);
			// pr($out, __FILE__.':'.__LINE__);
		}
		return $out;
	}
}