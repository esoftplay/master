<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (imageslider_isdesc() == 0)
{
	$db->Execute("ALTER TABLE `imageslider_text` ADD `description` TEXT  NULL DEFAULT '' AFTER `title`");
}
redirect();