<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$is_custom = false;
require_once _CLASS.'images.php';
if (defined('_IMAGE_STORAGE'))
{
	if (file_exists(__DIR__.'/vendor_'._IMAGE_STORAGE.'.php'))
	{
		$is_custom = true;
		require_once __DIR__.'/vendor_'._IMAGE_STORAGE.'.php';
	}
}
if (!$is_custom)
{
	class images_class extends images
	{
	}
}
