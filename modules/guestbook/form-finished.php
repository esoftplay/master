<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!$sys->menu_real)
{
	$sys->nav_change(lang('Guest Book'), 'guestbook');
	$sys->nav_add(lang('Guest Book Sent'));
}
echo msg(@$_SESSION['guestbook']);